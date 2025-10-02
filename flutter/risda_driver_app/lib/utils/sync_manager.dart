import 'dart:async';
import '../services/connectivity_service.dart';
import '../repositories/driver_log_repository.dart';
import '../services/api_service.dart';
import '../services/hive_service.dart';

class SyncManager {
  final ConnectivityService _connectivityService;
  final DriverLogRepository _driverLogRepository;
  
  Timer? _syncTimer;
  bool _isSyncing = false;
  
  // Stream to broadcast sync status
  final _syncStatusController = StreamController<SyncStatus>.broadcast();
  Stream<SyncStatus> get syncStatusStream => _syncStatusController.stream;
  
  SyncManager({
    required ConnectivityService connectivityService,
    required DriverLogRepository driverLogRepository,
  })  : _connectivityService = connectivityService,
        _driverLogRepository = driverLogRepository {
    // Listen for connectivity changes
    _connectivityService.connectivityStream.listen(_handleConnectivityChange);
  }
  
  void _handleConnectivityChange(bool isConnected) {
    if (isConnected) {
      // Try to sync when connection is restored
      syncNow();
    }
    
    // Broadcast status
    _syncStatusController.add(SyncStatus(
      isOnline: isConnected,
      isSyncing: _isSyncing,
    ));
  }
  
  // Start periodic sync
  void startPeriodicSync({Duration period = const Duration(minutes: 15)}) {
    _syncTimer?.cancel();
    _syncTimer = Timer.periodic(period, (_) => syncNow());
  }
  
  // Stop periodic sync
  void stopPeriodicSync() {
    _syncTimer?.cancel();
    _syncTimer = null;
  }
  
  // Sync now
  Future<Map<String, dynamic>> syncNow() async {
    if (_isSyncing) return {'success': false, 'message': 'Sync already in progress'};
    
    final isConnected = await _connectivityService.checkConnectivity();
    if (!isConnected) return {'success': false, 'message': 'No internet connection'};
    
    _isSyncing = true;
    _syncStatusController.add(SyncStatus(
      isOnline: isConnected,
      isSyncing: _isSyncing,
    ));
    
    try {
      // Process sync queue
      final apiService = ApiService();
      final result = await apiService.processSyncQueue();
      
      // Sync unsynced logs
      await _driverLogRepository.syncUnsyncedLogs();
      
      return {
        'success': true,
        'message': 'Sync completed successfully',
        'timestamp': DateTime.now().toString(),
        'successCount': result['successCount'] ?? 0,
        'totalCount': result['totalCount'] ?? 0,
        'failedItems': result['failedItems'] ?? [],
      };
    } catch (e) {
      print('DEBUG: Sync error: $e');
      return {
        'success': false,
        'message': 'Sync failed: $e',
        'timestamp': DateTime.now().toString(),
      };
    } finally {
      _isSyncing = false;
      _syncStatusController.add(SyncStatus(
        isOnline: isConnected,
        isSyncing: _isSyncing,
      ));
    }
  }
  
  // Force sync all completed but unsynced logs
  Future<Map<String, dynamic>> forceSyncCompletedLogs() async {
    if (_isSyncing) return {'success': false, 'message': 'Sync already in progress'};
    
    final isConnected = await _connectivityService.checkConnectivity();
    if (!isConnected) return {'success': false, 'message': 'No internet connection'};
    
    _isSyncing = true;
    _syncStatusController.add(SyncStatus(
      isOnline: isConnected,
      isSyncing: _isSyncing,
    ));
    
    try {
      print('DEBUG: Force syncing all completed logs');
      
      // Get all logs
      final allLogs = HiveService.getDriverLogs();
      
      // Find all completed but unsynced logs
      final completedLogs = allLogs.where((log) => 
        log.status == 'selesai' && 
        !log.isSynced && 
        log.checkoutTime != null
      ).toList();
      
      print('DEBUG: Found ${completedLogs.length} completed unsynced logs');
      
      if (completedLogs.isEmpty) {
        return {
          'success': true,
          'message': 'No completed logs to sync',
          'timestamp': DateTime.now().toString(),
          'successCount': 0,
          'totalCount': 0,
        };
      }
      
      // Try to get all active trips from server
      final apiService = ApiService();
      final response = await apiService.getActiveTrip();
      
      if (response['success'] != true || response['data'] == null) {
        print('DEBUG: No active server logs found or API error');
        
        // Add all completed logs to offline_checkout queue
        for (var log in completedLogs) {
          print('DEBUG: Adding log ${log.id} to offline_checkout queue');
          
          final checkoutData = {
            'program_id': log.programId,
            'kenderaan_id': log.kenderaanId,
            'checkin_time': log.checkinTime?.toIso8601String() ?? DateTime.now().toIso8601String(),
            'jarak_perjalanan': log.jarakPerjalanan ?? 0,
            'bacaan_odometer': log.bacaanOdometerCheckout ?? 0,
            'lokasi_checkout': log.lokasiCheckout ?? 'Unknown location',
            'catatan': log.catatan,
            'gps_latitude': log.gpsLatitude,
            'gps_longitude': log.gpsLongitude,
            'is_offline_checkout': true,
          };
          
          if (log.odometerPhotoCheckout != null) {
            checkoutData['odometer_photo'] = log.odometerPhotoCheckout;
          }
          
          await apiService.addToSyncQueue(
            endpoint: '/logs/offline_checkout',
            method: 'POST',
            data: checkoutData,
          );
        }
      }
      
      // Trigger sync process
      final syncResult = await syncNow();
      return {
        ...syncResult,
        'message': 'Force sync completed',
        'completedLogsAdded': completedLogs.length,
      };
    } catch (e) {
      print('DEBUG: Force sync error: $e');
      return {
        'success': false,
        'message': 'Force sync failed: $e',
        'timestamp': DateTime.now().toString(),
      };
    } finally {
      _isSyncing = false;
      _syncStatusController.add(SyncStatus(
        isOnline: isConnected,
        isSyncing: _isSyncing,
      ));
    }
  }
  
  // Get current status
  Future<SyncStatus> getCurrentStatus() async {
    final isConnected = await _connectivityService.checkConnectivity();
    return SyncStatus(
      isOnline: isConnected,
      isSyncing: _isSyncing,
    );
  }
  
  // Dispose resources
  void dispose() {
    _syncTimer?.cancel();
    _syncStatusController.close();
  }
}

class SyncStatus {
  final bool isOnline;
  final bool isSyncing;
  
  SyncStatus({
    required this.isOnline,
    required this.isSyncing,
  });
} 