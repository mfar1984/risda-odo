import 'dart:io';
import '../services/api_service.dart';
import '../services/hive_service.dart';
import '../services/connectivity_service.dart';
import '../models/driver_log_model.dart';
import 'package:dio/dio.dart';

class DriverLogRepository {
  final ApiService _apiService;
  final ConnectivityService _connectivityService;

  DriverLogRepository({
    required ApiService apiService,
    required ConnectivityService connectivityService,
  })  : _apiService = apiService,
        _connectivityService = connectivityService;

  // Get active trip
  Future<DriverLog?> getActiveTrip() async {
    // First check local storage
    DriverLog? activeLog = HiveService.getActiveDriverLog();
    
    // If found in local storage, return it
    if (activeLog != null) {
      return activeLog;
    }
    
    // If not found locally, try to fetch from API if online
    try {
      final isConnected = await _connectivityService.checkConnectivity();
      
      if (isConnected) {
        final response = await _apiService.getActiveTrip();
        
        if (response['success'] == true && response['data'] != null) {
          final logData = response['data'];
          final log = DriverLog.fromJson(logData);
          
          // Save to local storage
          await HiveService.saveDriverLog(log);
          
          return log;
        }
      }
      
      return null;
    } catch (e) {
      return null;
    }
  }

  // Start a new trip
  Future<DriverLog?> startTrip({
    required int programId,
    required int kenderaanId,
    required String lokasiMula,
    String? notaMula,
    double? gpsLatitude,
    double? gpsLongitude,
    required int bacaanOdometerMula,
    File? odometerPhoto,
  }) async {
    try {
      print('DEBUG: startTrip called with:');
      print('DEBUG: programId: $programId');
      print('DEBUG: kenderaanId: $kenderaanId');
      print('DEBUG: lokasiMula: $lokasiMula');
      print('DEBUG: notaMula: $notaMula');
      print('DEBUG: gpsLatitude: $gpsLatitude');
      print('DEBUG: gpsLongitude: $gpsLongitude');
      
      final userId = HiveService.getUser()?.id;
      print('DEBUG: userId: $userId');
      if (userId == null) {
        print('DEBUG: ERROR - User ID is null!');
        return null;
      }
      
      final isConnected = await _connectivityService.checkConnectivity();
      print('DEBUG: isConnected: $isConnected');
      
      // Tambah baik pengesahan sebelum check-in
      if (isConnected) {
        // Paksa sinkronisasi terlebih dahulu
        print('DEBUG: Force syncing before checking for active trips');
        await _apiService.processSyncQueue(forceSync: true);
        
        // Bersihkan log yang tidak konsisten
        print('DEBUG: Cleaning up inconsistent logs before check-in');
        await HiveService.cleanupInconsistentActiveLogs();
      }
      
      // Check if there's an active trip before starting a new one
      // Only check locally for offline mode, check both local and backend for online mode
      if (isConnected) {
        // Online mode - check both local and backend
        final activeTrip = await getActiveTrip();
        if (activeTrip != null) {
          print('DEBUG: ERROR - Cannot start new trip while active trip exists!');
          print('DEBUG: Active trip ID: ${activeTrip.id}');
          print('DEBUG: Active trip status: ${activeTrip.status}');
          print('DEBUG: Active trip check-in time: ${activeTrip.checkinTime}');
          throw Exception('Anda masih mempunyai log aktif yang belum ditamatkan. Sila tamatkan log aktif terlebih dahulu.');
        }
        print('DEBUG: No active trip found (online check), proceeding with new check-in...');
      } else {
        // Offline mode - only check local storage
        final localActiveTrip = HiveService.getActiveDriverLog();
        if (localActiveTrip != null) {
          print('DEBUG: ERROR - Cannot start new trip while local active trip exists!');
          print('DEBUG: Local active trip ID: ${localActiveTrip.id}');
          print('DEBUG: Local active trip status: ${localActiveTrip.status}');
          print('DEBUG: Local active trip check-in time: ${localActiveTrip.checkinTime}');
          throw Exception('Anda masih mempunyai log aktif yang belum ditamatkan. Sila tamatkan log aktif terlebih dahulu.');
        }
        print('DEBUG: No local active trip found (offline check), proceeding with new check-in...');
      }
      
      // Create log data
      final Map<String, dynamic> data = {
        'program_id': programId,
        'kenderaan_id': kenderaanId,
        'lokasi_mula': lokasiMula,
        'nota_mula': notaMula,
        'bacaan_odometer_mula': bacaanOdometerMula,
      };
      
      if (gpsLatitude != null && gpsLongitude != null) {
        data['gps_latitude'] = gpsLatitude;
        data['gps_longitude'] = gpsLongitude;
      }
      
      // Store the exact checkin time
      final exactCheckinTime = DateTime.now();
      data['checkin_time'] = exactCheckinTime.toIso8601String();
      
      // Add photo if available
      if (odometerPhoto != null) {
        print('DEBUG: Adding odometer photo to request: ${odometerPhoto.path}');
        data['odometer_photo'] = odometerPhoto.path;
      } else {
        print('DEBUG: No odometer photo provided');
      }
      
      print('DEBUG: Data to send: $data');
      
      if (isConnected) {
        print('DEBUG: Online mode - sending to API');
        // Online mode - send to API
        final response = await _apiService.startTrip(data);
        print('DEBUG: API response: $response');
        
        if (response['success'] == true) {
          final logData = response['data'];
          print('DEBUG: Log data from API: $logData');
          final log = DriverLog.fromJson(logData);
          print('DEBUG: DriverLog created from API: ${log.id}');
          
          // Save to local storage
          await HiveService.saveDriverLog(log);
          print('DEBUG: DriverLog saved to Hive');
          
          return log;
        } else {
          print('DEBUG: API returned success: false');
          return null;
        }
      } else {
        print('DEBUG: Offline mode - saving locally with complete data');
        // Offline mode - save locally with complete program and vehicle details
        final now = DateTime.now();
        print('DEBUG: Creating DriverLog with now: $now');
        
        // Get program and vehicle details from Hive for complete data
        final program = HiveService.getProgram(programId);
        final vehicle = HiveService.getVehicle(kenderaanId);
        
        print('DEBUG: Program from Hive: ${program?.namaProgram}');
        print('DEBUG: Vehicle from Hive: ${vehicle?.noPlat} - ${vehicle?.jenama} ${vehicle?.model}');
        
        final log = DriverLog(
          programId: programId,
          pemanduId: userId,
          kenderaanId: kenderaanId,
          namaLog: 'Log ${program?.namaProgram ?? 'Program'} - Driver',
          checkinTime: exactCheckinTime, // Use the same timestamp as online mode
          lokasiCheckin: lokasiMula,
          gpsLatitude: gpsLatitude,
          gpsLongitude: gpsLongitude,
          catatan: notaMula,
          status: 'aktif',
          createdBy: userId,
          createdAt: exactCheckinTime, // Use the same timestamp for consistency
          isSynced: false,
          // Program details
          programNama: program?.namaProgram,
          programLokasi: program?.lokasiProgram,
          // Vehicle details
          kenderaanNoPlat: vehicle?.noPlat,
          kenderaanJenama: vehicle?.jenama,
          kenderaanModel: vehicle?.model,
        );
        
        print('DEBUG: DriverLog created locally: ${log.id}');
        print('DEBUG: DriverLog status: ${log.status}');
        print('DEBUG: DriverLog isSynced: ${log.isSynced}');
        print('DEBUG: DriverLog Program Nama: ${log.programNama}');
        print('DEBUG: DriverLog Program Lokasi: ${log.programLokasi}');
        print('DEBUG: DriverLog Kenderaan No Plat: ${log.kenderaanNoPlat}');
        print('DEBUG: DriverLog Kenderaan Jenama: ${log.kenderaanJenama}');
        print('DEBUG: DriverLog Kenderaan Model: ${log.kenderaanModel}');
        
        // Save to local storage
        await HiveService.saveDriverLog(log);
        print('DEBUG: DriverLog saved to Hive successfully');
        
        // Add to sync queue
        print('DEBUG: Adding check-in to sync queue (offline mode)');
        await _apiService.addToSyncQueue(
          endpoint: '/logs/start',
          method: 'POST',
          data: data,
        );
        print('DEBUG: Check-in added to sync queue successfully');
        
        return log;
      }
    } catch (e) {
      print('DEBUG: startTrip ERROR: $e');
      print('DEBUG: Error stack trace: ${StackTrace.current}');
      return null;
    }
  }

  // End a trip
  Future<bool> endTrip({
    required DriverLog log,
    required int jarakPerjalanan,
    required int bacaanOdometer,
    required String lokasiCheckout,
    String? catatan,
    double? gpsLatitude,
    double? gpsLongitude,
    File? odometerPhoto,
  }) async {
    try {
      final isConnected = await _connectivityService.checkConnectivity();
      
      // Create log data
      final Map<String, dynamic> data = {
        'jarak_perjalanan': jarakPerjalanan,
        'bacaan_odometer': bacaanOdometer,
        'lokasi_checkout': lokasiCheckout,
        'catatan': catatan,
      };
      
      // Include the exact checkout time from the log
      if (log.checkoutTime != null) {
        data['checkout_time'] = log.checkoutTime!.toIso8601String();
      }
      
      if (gpsLatitude != null && gpsLongitude != null) {
        data['gps_latitude'] = gpsLatitude;
        data['gps_longitude'] = gpsLongitude;
      }
      
      // Add photo if available
      if (odometerPhoto != null) {
        print('DEBUG: Adding odometer photo to checkout request: ${odometerPhoto.path}');
        data['odometer_photo'] = odometerPhoto.path;
      } else {
        print('DEBUG: No odometer photo provided for checkout');
      }
      
      print('DEBUG: Updating local log with checkout data...');
      print('DEBUG: Before update - Status: ${log.status}, isActive: ${log.isActive}');
      
      // Update local log
      log.completeLog(
        checkoutTime: DateTime.now(),
        jarakPerjalanan: jarakPerjalanan,
        bacaanOdometer: bacaanOdometer,
        lokasiCheckout: lokasiCheckout,
        catatan: catatan,
        gpsLatitude: gpsLatitude,
        gpsLongitude: gpsLongitude,
      );
      
      print('DEBUG: After completeLog - Status: ${log.status}, isActive: ${log.isActive}');
      print('DEBUG: Checkout time: ${log.checkoutTime}');
      print('DEBUG: Jarak perjalanan: ${log.jarakPerjalanan}');
      print('DEBUG: Bacaan odometer: ${log.bacaanOdometer}');
      
      // CRITICAL: First check if there's a server ID for this log
      String? logKey;
      if (log.id != null) {
        logKey = log.id.toString();
        print('DEBUG: Using server ID as key: $logKey');
      } else {
        // For offline logs, use the local key
        logKey = 'local_${log.createdAt.millisecondsSinceEpoch}';
        print('DEBUG: Using local key: $logKey');
      }
      
      // Save to Hive with the correct key
      await HiveService.updateDriverLog(log, logKey);
      print('DEBUG: DriverLog updated in Hive successfully with key: $logKey');
      
      // CRITICAL: Also update any server-synced version of this log if it exists
      if (log.id == null) {
        // This is a local log, check if there's a server version
        final allLogs = HiveService.getDriverLogs();
        for (var serverLog in allLogs) {
          if (serverLog.id != null && 
              serverLog.programId == log.programId && 
              serverLog.kenderaanId == log.kenderaanId &&
              serverLog.status == 'aktif') {
            // Found a server version of this log, update it too
            print('DEBUG: Found server version of this log with ID: ${serverLog.id}');
            serverLog.completeLog(
              checkoutTime: log.checkoutTime ?? DateTime.now(),
              jarakPerjalanan: log.jarakPerjalanan ?? 0,
              bacaanOdometer: log.bacaanOdometerCheckout ?? 0,
              lokasiCheckout: log.lokasiCheckout ?? 'Unknown location',
              catatan: log.catatan,
              gpsLatitude: log.gpsLatitude,
              gpsLongitude: log.gpsLongitude,
            );
            await HiveService.updateDriverLog(serverLog, serverLog.id.toString());
            print('DEBUG: Updated server log with ID: ${serverLog.id}');
          }
        }
      }
      
      // Verify the update
      final updatedLog = HiveService.getDriverLog(logKey);
      if (updatedLog != null) {
        print('DEBUG: Verification - Updated log status: ${updatedLog.status}');
        print('DEBUG: Verification - Updated log isActive: ${updatedLog.isActive}');
        print('DEBUG: Verification - Updated log isCompleted: ${updatedLog.isCompleted}');
      } else {
        print('DEBUG: ERROR - Could not retrieve updated log from Hive!');
        print('DEBUG: Trying to get log with key: $logKey');
        
        // Try alternative key format
        final alternativeKey = 'local_${log.createdAt.millisecondsSinceEpoch}';
        final altLog = HiveService.getDriverLog(alternativeKey);
        if (altLog != null) {
          print('DEBUG: Found log with alternative key: $alternativeKey');
          print('DEBUG: Alternative log status: ${altLog.status}');
        } else {
          print('DEBUG: No log found with alternative key either');
        }
      }
      
      if (isConnected && log.id != null) {
        print('DEBUG: Online mode - sending to API');
        try {
          // Online mode - send to API
          final response = await _apiService.endTrip(log.id!, data);
          print('DEBUG: API response: $response');
        
          if (response['success'] == true) {
            print('DEBUG: API call successful');
            // Mark as synced
            log.isSynced = true;
            await HiveService.updateDriverLog(log, logKey);
            print('DEBUG: Log marked as synced');
          } else {
            print('DEBUG: API returned success: false: ${response['message']}');
            // If the error is because the log is not active, we should still consider it synced
            // This can happen if the log was already ended on the server side
            if (response['message'] == 'Log ini tidak aktif') {
              print('DEBUG: Log was already ended on server, marking as synced');
              log.isSynced = true;
              await HiveService.updateDriverLog(log, logKey);
            } else {
              throw Exception('API returned success: false - ${response['message']}');
            }
          }
        } catch (apiError) {
          print('DEBUG: API call failed: $apiError');
          // Check if this is a 400 error with "Log ini tidak aktif" message
          if (apiError is DioException && 
              apiError.response?.statusCode == 400 &&
              apiError.response?.data is Map &&
              apiError.response?.data['message'] == 'Log ini tidak aktif') {
            print('DEBUG: Log was already ended on server, marking as synced');
            log.isSynced = true;
            await HiveService.updateDriverLog(log, logKey);
          } else {
            // Add to sync queue for later retry
            print('DEBUG: Adding failed check-out to sync queue for retry');
            await _apiService.addToSyncQueue(
              endpoint: '/logs/${log.id}/end',
              method: 'POST',
              data: data,
            );
            print('DEBUG: Failed check-out added to sync queue');
          }
          // Don't throw error, return true since local update was successful
        }
      } else {
        // Offline mode - add to sync queue
        if (log.id != null) {
          print('DEBUG: Adding check-out to sync queue (offline mode)');
          await _apiService.addToSyncQueue(
            endpoint: '/logs/${log.id}/end',
            method: 'POST',
            data: data,
          );
          print('DEBUG: Check-out added to sync queue successfully');
        } else {
          // IMPROVED OFFLINE HANDLING: For offline logs without ID
          print('DEBUG: Offline checkout - log ID is null, creating special sync record');
          
          // Store checkout data with program and vehicle info for later matching
          final offlineCheckoutData = Map<String, dynamic>.from(data);
          offlineCheckoutData['program_id'] = log.programId;
          offlineCheckoutData['kenderaan_id'] = log.kenderaanId;
          offlineCheckoutData['checkin_time'] = log.checkinTime?.toIso8601String() ?? DateTime.now().toIso8601String();
          offlineCheckoutData['checkout_time'] = log.checkoutTime?.toIso8601String();
          offlineCheckoutData['is_offline_checkout'] = true;
          
          // Add to sync queue with special endpoint
          await _apiService.addToSyncQueue(
            endpoint: '/logs/offline_checkout',
            method: 'POST',
            data: offlineCheckoutData,
          );
          
          print('DEBUG: Offline checkout data saved with program/vehicle info for later matching');
          
          // Store checkout data for later sync
          log.isSynced = false;
          await HiveService.updateDriverLog(log, logKey);
          print('DEBUG: Offline checkout marked for later sync');
        }
      }
      
      // CRITICAL: Verify that there are no active trips left in Hive
      final allLogs = HiveService.getDriverLogs();
      final activeTrips = allLogs.where((l) => l.isActive).toList();
      print('DEBUG: Active trips remaining in Hive after checkout: ${activeTrips.length}');
      if (activeTrips.isNotEmpty) {
        print('DEBUG: WARNING - There are still active trips in Hive:');
        for (var trip in activeTrips) {
          print('DEBUG: Active trip ID: ${trip.id}, Program: ${trip.programId}, Vehicle: ${trip.kenderaanId}');
        }
      }
      
      return true;
    } catch (e) {
      print('DEBUG: endTrip ERROR: $e');
      print('DEBUG: Error stack trace: ${StackTrace.current}');
      return false;
    }
  }

  // Get all driver logs
  List<DriverLog> getAllLogs() {
    return HiveService.getDriverLogs();
  }

  // Sync unsynced logs
  Future<void> syncUnsyncedLogs() async {
    try {
      final isConnected = await _connectivityService.checkConnectivity();
      print('DEBUG: Sync attempt - Connected: $isConnected');
      
      if (isConnected) {
        // First check for completed logs that need syncing
        await _checkCompletedLogsForSync();
        
        // Then process the sync queue
        print('DEBUG: Processing sync queue...');
        await _apiService.processSyncQueue();
        print('DEBUG: Sync queue processed successfully');
      } else {
        print('DEBUG: Cannot sync - no internet connection');
      }
    } catch (e) {
      print('DEBUG: Sync error: $e');
      // Ignore sync errors
    }
  }
  
  // Check for completed logs that need syncing
  Future<void> _checkCompletedLogsForSync() async {
    print('DEBUG: Checking for completed logs that need syncing');
    
    try {
      // Get all completed but unsynced logs
      final allLogs = HiveService.getDriverLogs();
      final completedLogs = allLogs.where((log) => 
        log.status == 'selesai' && 
        !log.isSynced && 
        log.checkoutTime != null
      ).toList();
      
      print('DEBUG: Found ${completedLogs.length} completed logs that need syncing');
      
      if (completedLogs.isEmpty) {
        return;
      }
      
      // Try to match with server logs
      final response = await _apiService.getActiveTrip();
      print('DEBUG: Active trip response: $response');
      
      if (response['success'] == true && response['data'] != null) {
        final activeServerLog = response['data'];
        final serverLogId = activeServerLog['id'];
        final serverProgramId = activeServerLog['program_id'];
        final serverVehicleId = activeServerLog['kenderaan_id'];
        
        print('DEBUG: Found active server log: ID $serverLogId, Program $serverProgramId, Vehicle $serverVehicleId');
        
        // Find matching completed logs
        final matchingLogs = completedLogs.where((log) => 
          log.programId == serverProgramId && 
          log.kenderaanId == serverVehicleId
        ).toList();
        
        if (matchingLogs.isNotEmpty) {
          print('DEBUG: Found ${matchingLogs.length} matching completed logs');
          
          // Sort by creation time to get the most recent
          matchingLogs.sort((a, b) => b.createdAt.compareTo(a.createdAt));
          final mostRecentLog = matchingLogs.first;
          
          print('DEBUG: Most recent matching log: Created ${mostRecentLog.createdAt}');
          print('DEBUG: Most recent matching log: Checkout ${mostRecentLog.checkoutTime}');
          
          // Create checkout data
          final checkoutData = {
            'jarak_perjalanan': mostRecentLog.jarakPerjalanan ?? 0,
            'bacaan_odometer': mostRecentLog.bacaanOdometerCheckout ?? 0,
            'lokasi_checkout': mostRecentLog.lokasiCheckout ?? 'Unknown location',
            'catatan': mostRecentLog.catatan,
            'gps_latitude': mostRecentLog.gpsLatitude,
            'gps_longitude': mostRecentLog.gpsLongitude,
          };
          
          // Include the checkout time if available
          if (mostRecentLog.checkoutTime != null) {
            checkoutData['checkout_time'] = mostRecentLog.checkoutTime!.toIso8601String();
          }
          
          if (mostRecentLog.odometerPhotoCheckout != null) {
            checkoutData['odometer_photo'] = mostRecentLog.odometerPhotoCheckout;
          }
          
          // Add to sync queue
          print('DEBUG: Adding checkout data to sync queue for server log ID: $serverLogId');
          await _apiService.addToSyncQueue(
            endpoint: '/logs/$serverLogId/end',
            method: 'POST',
            data: checkoutData,
          );
          
          // Mark the local log as synced
          mostRecentLog.isSynced = true;
          await HiveService.updateDriverLog(mostRecentLog);
          print('DEBUG: Local log marked as synced');
        } else {
          print('DEBUG: No matching completed logs found for active server log');
        }
      } else {
        print('DEBUG: No active server log found or API error');
      }
    } catch (e) {
      print('DEBUG: Error checking completed logs: $e');
    }
  }
} 