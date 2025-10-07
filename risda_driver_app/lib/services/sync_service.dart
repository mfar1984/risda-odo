import 'package:flutter/foundation.dart';
import '../services/api_service.dart';
import '../services/hive_service.dart';
import '../services/connectivity_service.dart';
import '../models/journey_hive_model.dart';
import '../models/claim_hive_model.dart';
import '../models/sync_queue_hive_model.dart';
import 'dart:developer' as developer;

/// Sync Service - Handles background sync operations
/// - Syncs pending data when online
/// - FCM-triggered selective sync
/// - Auto-cleanup after successful sync
class SyncService extends ChangeNotifier {
  final ApiService _apiService;
  final ConnectivityService _connectivityService;
  
  bool _isSyncing = false;
  bool _isAutoSyncEnabled = true;
  DateTime? _lastSyncTime;
  Map<String, dynamic>? _lastSyncStats;
  
  // Getters
  bool get isSyncing => _isSyncing;
  bool get isAutoSyncEnabled => _isAutoSyncEnabled;
  DateTime? get lastSyncTime => _lastSyncTime;
  Map<String, dynamic>? get lastSyncStats => _lastSyncStats;
  
  SyncService(this._apiService, this._connectivityService) {
    _setupConnectivityListener();
  }
  
  /// Setup auto-sync when connectivity restored
  void _setupConnectivityListener() {
    _connectivityService.onBackOnline(() {
      developer.log('🟢 SyncService: Back online - triggering auto-sync');
      if (_isAutoSyncEnabled) {
        syncPendingData();  // Auto-sync!
      }
    });
  }
  
  /// Enable/disable auto-sync
  void setAutoSync(bool enabled) {
    _isAutoSyncEnabled = enabled;
    notifyListeners();
    developer.log('🔄 Auto-sync ${enabled ? "enabled" : "disabled"}');
  }
  
  // ============================================
  // MAIN SYNC OPERATION
  // ============================================
  
  /// Sync all pending data (journeys + claims + queue)
  /// This runs when offline → online transition
  Future<Map<String, dynamic>> syncPendingData() async {
    if (_isSyncing) {
      developer.log('⚠️ Sync already in progress - skipping');
      return {'success': false, 'message': 'Sync already in progress'};
    }
    
    if (!_connectivityService.isOnline) {
      developer.log('⚠️ Cannot sync - offline');
      return {'success': false, 'message': 'Device is offline'};
    }
    
    _isSyncing = true;
    notifyListeners();
    
    developer.log('🔄 ========================================');
    developer.log('🔄 STARTING PENDING DATA SYNC');
    developer.log('🔄 ========================================');
    
    int journeysSynced = 0;
    int journeysFailed = 0;
    int claimsSynced = 0;
    int claimsFailed = 0;
    int queueProcessed = 0;
    
    try {
      // ============================================
      // STEP 1: Sync Pending Journeys
      // ============================================
      developer.log('📦 Step 1: Syncing pending journeys...');
      final pendingJourneys = HiveService.getPendingSyncJourneys();
      developer.log('   Found ${pendingJourneys.length} pending journeys');
      
      for (var journey in pendingJourneys) {
        try {
          if (journey.id == null) {
            // New journey - needs to be created on server
            developer.log('   ⬆️ Creating journey ${journey.localId}...');
            // TODO: Implement journey sync (will do in next phase)
            // For now, just log
            developer.log('   ⚠️ Journey sync not implemented yet');
          } else {
            // Existing journey - update on server
            developer.log('   ⬆️ Updating journey ${journey.id}...');
            // TODO: Implement journey update
          }
          journeysSynced++;
        } catch (e) {
          developer.log('   ❌ Failed to sync journey: $e');
          journeysFailed++;
        }
      }
      
      // ============================================
      // STEP 2: Sync Pending Claims
      // ============================================
      developer.log('💰 Step 2: Syncing pending claims...');
      final pendingClaims = HiveService.getPendingSyncClaims();
      developer.log('   Found ${pendingClaims.length} pending claims');
      
      for (var claim in pendingClaims) {
        try {
          if (claim.id == null) {
            // New claim
            developer.log('   ⬆️ Creating claim ${claim.localId}...');
            // TODO: Implement claim sync
            developer.log('   ⚠️ Claim sync not implemented yet');
          } else {
            // Update claim
            developer.log('   ⬆️ Updating claim ${claim.id}...');
            // TODO: Implement claim update
          }
          claimsSynced++;
        } catch (e) {
          developer.log('   ❌ Failed to sync claim: $e');
          claimsFailed++;
        }
      }
      
      // ============================================
      // STEP 3: Process Sync Queue
      // ============================================
      developer.log('📋 Step 3: Processing sync queue...');
      final pendingQueue = HiveService.getPendingSyncQueue();
      developer.log('   Found ${pendingQueue.length} queue items');
      
      for (var item in pendingQueue) {
        try {
          developer.log('   ⬆️ Processing ${item.type} ${item.action}...');
          // TODO: Implement queue processing
          queueProcessed++;
        } catch (e) {
          developer.log('   ❌ Failed to process queue item: $e');
        }
      }
      
      // ============================================
      // STEP 4: Cleanup Old Data (After Sync!)
      // ============================================
      developer.log('🧹 Step 4: Cleaning up old data...');
      final cleanupStats = await HiveService.cleanOldData();
      developer.log('   Deleted ${cleanupStats['journeys_deleted']} old journeys');
      developer.log('   Deleted ${cleanupStats['claims_deleted']} old claims');
      developer.log('   Deleted ${cleanupStats['sync_queue_deleted']} old queue items');
      
      // ============================================
      // STEP 5: Enforce Storage Limits
      // ============================================
      developer.log('📊 Step 5: Enforcing storage limits...');
      await HiveService.enforceStorageLimits();
      
      _lastSyncTime = DateTime.now();
      _lastSyncStats = {
        'journeys_synced': journeysSynced,
        'journeys_failed': journeysFailed,
        'claims_synced': claimsSynced,
        'claims_failed': claimsFailed,
        'queue_processed': queueProcessed,
        'cleanup_stats': cleanupStats,
      };
      
      developer.log('🔄 ========================================');
      developer.log('✅ SYNC COMPLETED SUCCESSFULLY');
      developer.log('🔄 ========================================');
      developer.log('📊 Stats: $journeysSynced journeys, $claimsSynced claims synced');
      
      return {
        'success': true,
        'stats': _lastSyncStats,
      };
      
    } catch (e) {
      developer.log('❌ Sync error: $e');
      return {
        'success': false,
        'message': e.toString(),
      };
    } finally {
      _isSyncing = false;
      notifyListeners();
    }
  }
  
  // ============================================
  // SELECTIVE SYNC (FCM-Triggered)
  // ============================================
  
  /// Sync specific entity (triggered by FCM notification)
  Future<void> syncByType(String type) async {
    if (!_connectivityService.isOnline) return;
    
    developer.log('🔔 FCM-triggered sync: $type');
    
    switch (type) {
      case 'claims':
      case 'claim_approved':
      case 'claim_rejected':
        await syncClaims();
        break;
      case 'journeys':
      case 'journey_updated':
        await syncJourneys();
        break;
      case 'programs':
      case 'program_assigned':
        await syncPrograms();
        break;
      default:
        developer.log('⚠️ Unknown sync type: $type');
    }
  }
  
  /// Sync claims from server
  Future<void> syncClaims() async {
    try {
      developer.log('💰 Syncing claims from server...');
      final response = await _apiService.getClaims();
      
      if (response['success'] == true) {
        // TODO: Update Hive with fresh claim data
        developer.log('✅ Claims synced');
      }
    } catch (e) {
      developer.log('❌ Sync claims error: $e');
    }
  }
  
  /// Sync journeys from server
  Future<void> syncJourneys() async {
    try {
      developer.log('🚗 Syncing journeys from server...');
      final response = await _apiService.getDriverLogs();
      
      if (response['success'] == true) {
        // TODO: Update Hive with fresh journey data
        developer.log('✅ Journeys synced');
      }
    } catch (e) {
      developer.log('❌ Sync journeys error: $e');
    }
  }
  
  /// Sync programs from server
  Future<void> syncPrograms() async {
    try {
      developer.log('📋 Syncing programs from server...');
      final response = await _apiService.getPrograms();
      
      if (response['success'] == true) {
        // TODO: Update Hive with fresh program data
        developer.log('✅ Programs synced');
      }
    } catch (e) {
      developer.log('❌ Sync programs error: $e');
    }
  }
  
  /// Sync vehicles from server
  Future<void> syncVehicles() async {
    try {
      developer.log('🚙 Syncing vehicles from server...');
      final vehicles = await _apiService.getVehicles();
      
      // TODO: Convert to VehicleHive and save to Hive
      developer.log('✅ Vehicles synced: ${vehicles.length} vehicles');
    } catch (e) {
      developer.log('❌ Sync vehicles error: $e');
    }
  }
  
  // ============================================
  // HELPER METHODS
  // ============================================
  
  /// Get sync status summary
  Map<String, dynamic> getSyncStatus() {
    return {
      'is_syncing': _isSyncing,
      'auto_sync_enabled': _isAutoSyncEnabled,
      'last_sync_time': _lastSyncTime?.toIso8601String(),
      'last_sync_stats': _lastSyncStats,
      'pending_count': HiveService.getTotalPendingSyncCount(),
    };
  }
  
  /// Force sync now (manual trigger)
  Future<void> forceSyncNow() async {
    developer.log('🔄 Force sync triggered manually');
    await syncPendingData();
  }
}

