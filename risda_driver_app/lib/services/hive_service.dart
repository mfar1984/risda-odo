import 'package:hive_flutter/hive_flutter.dart';
import 'package:path_provider/path_provider.dart';
import '../models/user_model.dart';
import '../models/auth_model.dart';
import '../models/program_model.dart';
import '../models/vehicle_model.dart';
import '../models/driver_log_model.dart';
import '../models/sync_queue_model.dart';

class HiveBoxes {
  static const String auth = 'auth';
  static const String user = 'user';
  static const String programs = 'programs';
  static const String vehicles = 'vehicles';
  static const String driverLogs = 'driver_logs';
  static const String syncQueue = 'sync_queue';
}

class HiveService {
  static Future<void> init() async {
    final appDocumentDir = await getApplicationDocumentsDirectory();
    await Hive.initFlutter(appDocumentDir.path);
    
    // Register adapters
    Hive.registerAdapter(UserAdapter());
    Hive.registerAdapter(AuthAdapter());
    Hive.registerAdapter(ProgramAdapter());
    Hive.registerAdapter(VehicleAdapter());
    Hive.registerAdapter(DriverLogAdapter());
    Hive.registerAdapter(SyncQueueItemAdapter());
    
    // Open boxes safely
    await _safelyOpenBoxes();
  }
  
  // Fungsi untuk membuka kotak dengan selamat
  static Future<void> _safelyOpenBoxes() async {
    try {
      // Buka kotak pengguna dengan penanganan ralat khusus
      await _safelyOpenUserBox();
      
      // Buka kotak lain seperti biasa
      await Hive.openBox<Auth>(HiveBoxes.auth);
      await Hive.openBox<Program>(HiveBoxes.programs);
      await Hive.openBox<Vehicle>(HiveBoxes.vehicles);
      await Hive.openBox<DriverLog>(HiveBoxes.driverLogs);
      await Hive.openBox<SyncQueueItem>(HiveBoxes.syncQueue);
    } catch (e) {
      print('DEBUG: Error opening boxes: $e');
      print('DEBUG: Error stack trace: ${StackTrace.current}');
      rethrow;
    }
  }
  
  // Fungsi khusus untuk membuka kotak pengguna dengan selamat
  static Future<void> _safelyOpenUserBox() async {
    try {
      // Cubaan pertama untuk membuka kotak pengguna
      await Hive.openBox<User>(HiveBoxes.user);
      print('DEBUG: User box opened successfully');
    } catch (e) {
      print('DEBUG: Error opening user box: $e');
      
      try {
        // Jika gagal, cuba buka kotak tanpa typechecking
        final rawBox = await Hive.openBox(HiveBoxes.user);
        print('DEBUG: Opened user box without type checking');
        
        // Periksa jika ada data pengguna semasa
        if (rawBox.containsKey('current_user')) {
          print('DEBUG: Found current_user key in box');
          
          try {
            // Simpan data pengguna sementara jika boleh
            final rawUserData = rawBox.get('current_user');
            print('DEBUG: Retrieved raw user data: ${rawUserData != null ? 'exists' : 'null'}');
            
            // Hapuskan kotak yang bermasalah
            await rawBox.clear();
            await rawBox.close();
            await Hive.deleteBoxFromDisk(HiveBoxes.user);
            print('DEBUG: Deleted problematic user box');
            
            // Buka semula kotak dengan type checking
            final typedBox = await Hive.openBox<User>(HiveBoxes.user);
            print('DEBUG: Reopened user box with type checking');
            
            // Jika ada data pengguna dan ia adalah Map, cuba simpan semula
            if (rawUserData != null && rawUserData is Map) {
              try {
                final newUser = User(
                  id: rawUserData['id'] ?? 0,
                  name: rawUserData['name'] ?? 'Unknown',
                  email: rawUserData['email'] ?? 'unknown@example.com',
                  role: rawUserData['role'],
                  bahagian: rawUserData['bahagian'],
                  stesen: rawUserData['stesen'],
                  staff: rawUserData['staff'],
                  createdAt: rawUserData['createdAt'],
                  lastLogin: rawUserData['lastLogin'],
                );
                await typedBox.put('current_user', newUser);
                print('DEBUG: Restored user data successfully');
              } catch (e) {
                print('DEBUG: Could not restore user data: $e');
              }
            }
          } catch (e) {
            print('DEBUG: Error handling user data: $e');
            
            // Jika semua gagal, buka kotak baru
            await Hive.deleteBoxFromDisk(HiveBoxes.user);
            await Hive.openBox<User>(HiveBoxes.user);
            print('DEBUG: Created new empty user box');
          }
        } else {
          // Jika tiada data pengguna, buka kotak baru
          await rawBox.close();
          await Hive.deleteBoxFromDisk(HiveBoxes.user);
          await Hive.openBox<User>(HiveBoxes.user);
          print('DEBUG: Created new empty user box (no current_user found)');
        }
      } catch (e) {
        print('DEBUG: Fatal error handling user box: $e');
        print('DEBUG: Error stack trace: ${StackTrace.current}');
        // Biarkan ralat ini dihantar ke atas untuk ditangani oleh try/catch di main.dart
        rethrow;
      }
    }
  }

  // Auth methods
  static Future<void> saveAuth(Auth auth) async {
    final box = Hive.box<Auth>(HiveBoxes.auth);
    await box.put('current_auth', auth);
  }

  static Auth? getAuth() {
    final box = Hive.box<Auth>(HiveBoxes.auth);
    return box.get('current_auth');
  }

  static Future<void> clearAuth() async {
    final box = Hive.box<Auth>(HiveBoxes.auth);
    await box.delete('current_auth');
  }

  static bool isLoggedIn() {
    final auth = getAuth();
    print('DEBUG: Checking login status...');
    print('DEBUG: Auth object: ${auth != null ? 'exists' : 'null'}');
    
    if (auth == null) {
      print('DEBUG: No auth object found');
      return false;
    }
    
    print('DEBUG: Auth isLoggedIn: ${auth.isLoggedIn}');
    print('DEBUG: Auth isExpired: ${auth.isExpired}');
    print('DEBUG: Token created: ${auth.createdAt}');
    print('DEBUG: Token expires in: ${auth.expiresIn} seconds');
    
    final isLoggedIn = auth.isLoggedIn && !auth.isExpired;
    print('DEBUG: Final login status: $isLoggedIn');
    
    return isLoggedIn;
  }

  // User methods
  static Future<void> saveUser(User user) async {
    final box = Hive.box<User>(HiveBoxes.user);
    await box.put('current_user', user);
  }

  static User? getUser() {
    final box = Hive.box<User>(HiveBoxes.user);
    return box.get('current_user');
  }

  static Future<void> clearUser() async {
    final box = Hive.box<User>(HiveBoxes.user);
    await box.delete('current_user');
  }

  // Program methods
  static Future<void> savePrograms(List<Program> programs) async {
    final box = Hive.box<Program>(HiveBoxes.programs);
    await box.clear();
    
    for (var program in programs) {
      await box.put(program.id.toString(), program);
    }
  }

  static List<Program> getPrograms() {
    final box = Hive.box<Program>(HiveBoxes.programs);
    return box.values.toList();
  }

  static Program? getProgram(int id) {
    final box = Hive.box<Program>(HiveBoxes.programs);
    return box.get(id.toString());
  }

  // Vehicle methods
  static Future<void> saveVehicles(List<Vehicle> vehicles) async {
    final box = Hive.box<Vehicle>(HiveBoxes.vehicles);
    await box.clear();
    
    for (var vehicle in vehicles) {
      await box.put(vehicle.id.toString(), vehicle);
    }
  }

  static List<Vehicle> getVehicles() {
    final box = Hive.box<Vehicle>(HiveBoxes.vehicles);
    return box.values.toList();
  }

  static Vehicle? getVehicle(int id) {
    final box = Hive.box<Vehicle>(HiveBoxes.vehicles);
    return box.get(id.toString());
  }

  // Driver log methods
  static Future<void> saveDriverLog(DriverLog log) async {
    print('DEBUG: saveDriverLog called');
    print('DEBUG: log.id: ${log.id}');
    print('DEBUG: log.programId: ${log.programId}');
    print('DEBUG: log.pemanduId: ${log.pemanduId}');
    print('DEBUG: log.kenderaanId: ${log.kenderaanId}');
    print('DEBUG: log.status: ${log.status}');
    print('DEBUG: log.isSynced: ${log.isSynced}');
    
    try {
    final box = Hive.box<DriverLog>(HiveBoxes.driverLogs);
    
    // Gunakan ID server jika ada, jika tidak gunakan kunci lokal
    final key = log.id?.toString() ?? 'local_${DateTime.now().millisecondsSinceEpoch}';
      print('DEBUG: Using key: $key');
      
    // Periksa jika ada log lokal yang sepadan dengan log server
    if (log.id != null) {
      // Cari dan hapuskan log lokal yang sepadan dengan program dan kenderaan yang sama
      final allKeys = box.keys.toList();
      print('DEBUG: Checking ${allKeys.length} keys for matching local logs');
      
      for (var existingKey in allKeys) {
        if (existingKey.toString().startsWith('local_')) {
          final existingLog = box.get(existingKey);
          if (existingLog != null && 
              existingLog.programId == log.programId && 
              existingLog.kenderaanId == log.kenderaanId) {
            
            // Jika log server aktif dan log lokal aktif, atau keduanya selesai
            if ((log.isActive && existingLog.isActive) || 
                (!log.isActive && !existingLog.isActive)) {
              // Hapuskan log lokal yang sepadan
              await box.delete(existingKey);
              print('DEBUG: Removed local log with key $existingKey that matches server log ${log.id}');
              print('DEBUG: Removed log status: ${existingLog.status}, isActive: ${existingLog.isActive}');
            }
          }
        }
      }
    }
      
    await box.put(key, log);
      print('DEBUG: DriverLog saved successfully with key: $key');
      
      // Verify it was saved
      final savedLog = box.get(key);
      print('DEBUG: Verification - saved log exists: ${savedLog != null}');
      if (savedLog != null) {
        print('DEBUG: Verification - saved log ID: ${savedLog.id}');
        print('DEBUG: Verification - saved log status: ${savedLog.status}');
      }
    } catch (e) {
      print('DEBUG: ERROR saving DriverLog: $e');
      print('DEBUG: Error stack trace: ${StackTrace.current}');
      rethrow;
    }
  }

  static Future<void> updateDriverLog(DriverLog log, [String? explicitKey]) async {
    try {
      final box = Hive.box<DriverLog>(HiveBoxes.driverLogs);
      final key = explicitKey ?? log.id?.toString() ?? 'local_${log.createdAt.millisecondsSinceEpoch}';
      
      print('DEBUG: updateDriverLog - Updating log with key: $key');
      print('DEBUG: updateDriverLog - Log ID: ${log.id}');
      print('DEBUG: updateDriverLog - Before update - Status: ${log.status}');
      print('DEBUG: updateDriverLog - Before update - isActive: ${log.isActive}');
      print('DEBUG: updateDriverLog - Before update - isCompleted: ${log.isCompleted}');
      
      // Get existing log for comparison
      final existingLog = box.get(key);
      if (existingLog != null) {
        print('DEBUG: updateDriverLog - Existing log status: ${existingLog.status}');
        print('DEBUG: updateDriverLog - Existing log isActive: ${existingLog.isActive}');
      }
      
      // Save the updated log
      await box.put(key, log);
      print('DEBUG: updateDriverLog - Log saved to Hive');
      
      // Verify the update
      final savedLog = box.get(key);
      if (savedLog != null) {
        print('DEBUG: updateDriverLog - Verification - Saved log status: ${savedLog.status}');
        print('DEBUG: updateDriverLog - Verification - Saved log isActive: ${savedLog.isActive}');
        print('DEBUG: updateDriverLog - Verification - Saved log isCompleted: ${savedLog.isCompleted}');
        print('DEBUG: updateDriverLog - Verification - Checkout time: ${savedLog.checkoutTime}');
        print('DEBUG: updateDriverLog - Verification - Jarak perjalanan: ${savedLog.jarakPerjalanan}');
      } else {
        print('DEBUG: updateDriverLog - ERROR - Could not retrieve saved log!');
      }
      
      // Check if there are any remaining active trips
      final allLogs = box.values.toList();
      int activeCount = 0;
      for (var driverLog in allLogs) {
        if (driverLog.isActive) {
          activeCount++;
          print('DEBUG: updateDriverLog - Remaining active log: ID ${driverLog.id}, Status: ${driverLog.status}');
        }
      }
      print('DEBUG: updateDriverLog - Total remaining active trips: $activeCount');
      
    } catch (e) {
      print('DEBUG: updateDriverLog - ERROR: $e');
      print('DEBUG: updateDriverLog - Error stack trace: ${StackTrace.current}');
      rethrow;
    }
  }

  static List<DriverLog> getDriverLogs() {
    final box = Hive.box<DriverLog>(HiveBoxes.driverLogs);
    return box.values.toList();
  }

  static DriverLog? getDriverLog(dynamic key) {
    try {
    final box = Hive.box<DriverLog>(HiveBoxes.driverLogs);
      final log = box.get(key);
      
      if (log != null) {
        print('DEBUG: getDriverLog - Found log with key: $key');
        print('DEBUG: getDriverLog - Log ID: ${log.id}, Status: ${log.status}');
        print('DEBUG: getDriverLog - Log isActive: ${log.isActive}, isCompleted: ${log.isCompleted}');
      } else {
        print('DEBUG: getDriverLog - No log found with key: $key');
      }
      
      return log;
    } catch (e) {
      print('DEBUG: getDriverLog - ERROR: $e');
      return null;
    }
  }

  static DriverLog? getActiveDriverLog() {
    final box = Hive.box<DriverLog>(HiveBoxes.driverLogs);
    final logs = box.values.toList();
    
    print('DEBUG: getActiveDriverLog - Total logs: ${logs.length}');
    
    for (var log in logs) {
      print('DEBUG: Log ID: ${log.id}, Status: ${log.status}, isActive: ${log.isActive}');
      print('DEBUG: Checkin: ${log.checkinTime}, Checkout: ${log.checkoutTime}');
    }
    
    try {
      final activeLog = logs.firstWhere((log) => log.isActive);
      print('DEBUG: Found active log: ID ${activeLog.id}, Status: ${activeLog.status}');
      return activeLog;
    } catch (e) {
      print('DEBUG: No active log found');
      return null;
    }
  }

  // Get active driver logs
  static List<DriverLog> getActiveDriverLogs() {
    try {
      print('DEBUG: Getting active driver logs');
      final box = Hive.box<DriverLog>(HiveBoxes.driverLogs);
      final logs = box.values.where((log) => log.isActive).toList();
      print('DEBUG: Found ${logs.length} active logs');
      return logs;
    } catch (e) {
      print('DEBUG: Error getting active driver logs: $e');
      return [];
    }
  }

  static List<DriverLog> getUnsyncedDriverLogs() {
    final box = Hive.box<DriverLog>(HiveBoxes.driverLogs);
    return box.values.where((log) => !log.isSynced).toList();
  }

  // Clear active trip (for conflict resolution)
  static Future<void> clearActiveTrip() async {
    final box = Hive.box<DriverLog>(HiveBoxes.driverLogs);
    final logs = box.values.toList();
    
    print('DEBUG: clearActiveTrip - Total logs: ${logs.length}');
    
    for (var log in logs) {
      if (log.isActive) {
        print('DEBUG: Clearing active log: ID ${log.id}, Status: ${log.status}');
        await box.delete(log.id?.toString() ?? 'local_${log.createdAt.millisecondsSinceEpoch}');
      }
    }
    
    print('DEBUG: Active trip cleared');
  }

  // Force complete all active trips
  static Future<void> forceCompleteAllActiveTrips() async {
    final box = Hive.box<DriverLog>(HiveBoxes.driverLogs);
    final logs = box.values.toList();
    
    print('DEBUG: forceCompleteAllActiveTrips - Total logs: ${logs.length}');
    
    // Count active trips
    int activeCount = 0;
    for (var log in logs) {
      if (log.isActive) {
        activeCount++;
        print('DEBUG: Found active log: ID ${log.id}, Status: ${log.status}, Checkin: ${log.checkinTime}');
      }
    }
    print('DEBUG: Total active trips to complete: $activeCount');
    
    // Complete all active trips
    for (var log in logs) {
      if (log.isActive) {
        print('DEBUG: Force completing log: ID ${log.id}, Current status: ${log.status}');
        
        // Create updated log with completed status
        final updatedLog = DriverLog(
          id: log.id,
          programId: log.programId,
          pemanduId: log.pemanduId,
          kenderaanId: log.kenderaanId,
          namaLog: log.namaLog,
          checkinTime: log.checkinTime,
          checkoutTime: DateTime.now(), // Force checkout time
          jarakPerjalanan: log.jarakPerjalanan ?? 0,
          bacaanOdometer: log.bacaanOdometer,
          odometerPhoto: log.odometerPhoto,
          lokasiCheckin: log.lokasiCheckin,
          gpsLatitude: log.gpsLatitude,
          gpsLongitude: log.gpsLongitude,
          lokasiCheckout: log.lokasiCheckout ?? 'Auto-completed',
          catatan: log.catatan,
          status: 'selesai', // Force complete status
          createdBy: log.createdBy,
          isSynced: false, // Mark for sync
          createdAt: log.createdAt,
          updatedAt: DateTime.now(),
          bacaanOdometerCheckout: log.bacaanOdometerCheckout,
          odometerPhotoCheckout: log.odometerPhotoCheckout,
          programNama: log.programNama,
          programLokasi: log.programLokasi,
          kenderaanNoPlat: log.kenderaanNoPlat,
          kenderaanJenama: log.kenderaanJenama,
          kenderaanModel: log.kenderaanModel,
        );
        
        final key = log.id?.toString() ?? 'local_${log.createdAt.millisecondsSinceEpoch}';
        print('DEBUG: Saving updated log with key: $key, New status: ${updatedLog.status}');
        await box.put(key, updatedLog);
        
        // Verify the update
        final savedLog = box.get(key);
        print('DEBUG: Verification - saved log status: ${savedLog?.status}, isActive: ${savedLog?.isActive}');
      }
    }
    
    print('DEBUG: All active trips force completed');
    
    // Final check
    final finalLogs = box.values.toList();
    print('DEBUG: Final check - Total logs: ${finalLogs.length}');
    int remainingActive = 0;
    for (var log in finalLogs) {
      if (log.isActive) {
        remainingActive++;
        print('DEBUG: WARNING - Still active log: ID ${log.id}, Status: ${log.status}');
      }
    }
    print('DEBUG: Remaining active trips: $remainingActive');
  }

  // Clear all active trips (nuclear option)
  static Future<void> clearAllActiveTrips() async {
    final box = Hive.box<DriverLog>(HiveBoxes.driverLogs);
    final logs = box.values.toList();
    
    print('DEBUG: clearAllActiveTrips - Total logs: ${logs.length}');
    
    // Count active trips
    int activeCount = 0;
    for (var log in logs) {
      if (log.isActive) {
        activeCount++;
        print('DEBUG: Found active log to delete: ID ${log.id}, Status: ${log.status}');
      }
    }
    print('DEBUG: Total active trips to delete: $activeCount');
    
    // Delete all active trips
    for (var log in logs) {
      if (log.isActive) {
        final key = log.id?.toString() ?? 'local_${log.createdAt.millisecondsSinceEpoch}';
        print('DEBUG: Deleting active log with key: $key');
        await box.delete(key);
      }
    }
    
    print('DEBUG: All active trips deleted');
    
    // Final check
    final finalLogs = box.values.toList();
    print('DEBUG: Final check - Total logs: ${finalLogs.length}');
    int remainingActive = 0;
    for (var log in finalLogs) {
      if (log.isActive) {
        remainingActive++;
        print('DEBUG: WARNING - Still active log: ID ${log.id}, Status: ${log.status}');
      }
    }
    print('DEBUG: Remaining active trips: $remainingActive');
  }

  // Nuclear option - Clear ALL active trips with force delete
  static Future<void> nuclearClearAllActiveTrips() async {
    final box = Hive.box<DriverLog>(HiveBoxes.driverLogs);
    
    print('DEBUG: nuclearClearAllActiveTrips - Starting nuclear cleanup');
    
    // Get all keys
    final allKeys = box.keys.toList();
    print('DEBUG: Total keys in box: ${allKeys.length}');
    
    // Find all active trip keys
    List<dynamic> activeKeys = [];
    for (var key in allKeys) {
      final log = box.get(key);
      if (log != null && log.isActive) {
        activeKeys.add(key);
        print('DEBUG: Found active key: $key, Status: ${log.status}');
      }
    }
    
    print('DEBUG: Active keys to delete: ${activeKeys.length}');
    
    // Force delete all active keys
    for (var key in activeKeys) {
      print('DEBUG: Nuclear deleting key: $key');
      await box.delete(key);
    }
    
    print('DEBUG: Nuclear deletion completed');
    
    // Verify cleanup
    final remainingLogs = box.values.toList();
    print('DEBUG: Remaining logs after nuclear: ${remainingLogs.length}');
    
    int remainingActive = 0;
    for (var log in remainingLogs) {
      if (log.isActive) {
        remainingActive++;
        print('DEBUG: ERROR - Still active after nuclear: ID ${log.id}, Status: ${log.status}');
      }
    }
    
    print('DEBUG: Remaining active trips after nuclear: $remainingActive');
    
    if (remainingActive > 0) {
      print('DEBUG: WARNING - Nuclear option failed! Still have active trips');
    } else {
      print('DEBUG: SUCCESS - Nuclear option completed! No active trips remaining');
    }
  }

  // Ultimate nuclear option - Clear entire box and recreate
  static Future<void> ultimateNuclearClear() async {
    final box = Hive.box<DriverLog>(HiveBoxes.driverLogs);
    
    print('DEBUG: ultimateNuclearClear - Starting ultimate cleanup');
    
    // Get all completed trips first
    final allLogs = box.values.toList();
    final completedTrips = allLogs.where((log) => !log.isActive).toList();
    
    print('DEBUG: Saving ${completedTrips.length} completed trips');
    
    // Clear entire box
    await box.clear();
    print('DEBUG: Entire box cleared');
    
    // Restore only completed trips
    for (var trip in completedTrips) {
      final key = trip.id?.toString() ?? 'local_${trip.createdAt.millisecondsSinceEpoch}';
      await box.put(key, trip);
      print('DEBUG: Restored completed trip: $key');
    }
    
    print('DEBUG: Ultimate nuclear clear completed');
    print('DEBUG: Final box count: ${box.length}');
    
    // Final verification
    final finalLogs = box.values.toList();
    int activeCount = 0;
    for (var log in finalLogs) {
      if (log.isActive) {
        activeCount++;
        print('DEBUG: ERROR - Active trip after ultimate nuclear: ${log.id}');
      }
    }
    
    print('DEBUG: Active trips after ultimate nuclear: $activeCount');
  }

  // Sync queue methods
  static Future<void> addToSyncQueue(SyncQueueItem item) async {
    print('DEBUG: addToSyncQueue called');
    print('DEBUG: Item ID: ${item.id}');
    print('DEBUG: Item endpoint: ${item.endpoint}');
    print('DEBUG: Item method: ${item.method}');
    print('DEBUG: Item data: ${item.data}');
    
    final box = Hive.box<SyncQueueItem>(HiveBoxes.syncQueue);
    await box.put(item.id, item);
    
    // Verify it was added
    final savedItem = box.get(item.id);
    print('DEBUG: Verification - item saved: ${savedItem != null}');
    if (savedItem != null) {
      print('DEBUG: Verification - saved item ID: ${savedItem.id}');
      print('DEBUG: Verification - saved item endpoint: ${savedItem.endpoint}');
    }
    
    // Show current queue count
    final currentQueue = box.values.toList();
    print('DEBUG: Current sync queue count: ${currentQueue.length}');
  }

  static List<SyncQueueItem> getSyncQueue() {
    final box = Hive.box<SyncQueueItem>(HiveBoxes.syncQueue);
    final queue = box.values.toList();
    print('DEBUG: getSyncQueue - returning ${queue.length} items');
    for (var item in queue) {
      print('DEBUG: Queue item: ID ${item.id}, ${item.method} ${item.endpoint}');
    }
    return queue;
  }

  static Future<void> removeSyncQueueItem(String id) async {
    final box = Hive.box<SyncQueueItem>(HiveBoxes.syncQueue);
    await box.delete(id);
  }

  static Future<void> updateSyncQueueItem(SyncQueueItem item) async {
    final box = Hive.box<SyncQueueItem>(HiveBoxes.syncQueue);
    await box.put(item.id, item);
  }

  // Clean up inconsistent active logs
  static Future<void> cleanupInconsistentActiveLogs() async {
    print('DEBUG: Starting cleanup of inconsistent active logs');
    try {
      final box = Hive.box<DriverLog>(HiveBoxes.driverLogs);
      final allLogs = box.values.toList();
      
      // Group logs by program_id and kenderaan_id
      Map<String, List<DriverLog>> groupedLogs = {};
      for (var log in allLogs) {
        final key = '${log.programId}_${log.kenderaanId}';
        if (!groupedLogs.containsKey(key)) {
          groupedLogs[key] = [];
        }
        groupedLogs[key]!.add(log);
      }
      
      int fixedCount = 0;
      
      // For each group, check if there are both active and completed logs
      for (var key in groupedLogs.keys) {
        final logs = groupedLogs[key]!;
        final activeLogs = logs.where((log) => log.isActive).toList();
        final completedLogs = logs.where((log) => log.isCompleted).toList();
        
        if (activeLogs.isNotEmpty && completedLogs.isNotEmpty) {
          print('DEBUG: Found inconsistent logs for key $key: ${activeLogs.length} active, ${completedLogs.length} completed');
          
          // Sort by creation time to get the most recent
          completedLogs.sort((a, b) => b.createdAt.compareTo(a.createdAt));
          activeLogs.sort((a, b) => b.createdAt.compareTo(a.createdAt));
          
          final mostRecentCompleted = completedLogs.first;
          
          // Update all active logs to match the completed one
          for (var activeLog in activeLogs) {
            print('DEBUG: Fixing active log ${activeLog.id} with completed log data');
            activeLog.completeLog(
              checkoutTime: mostRecentCompleted.checkoutTime ?? DateTime.now(),
              jarakPerjalanan: mostRecentCompleted.jarakPerjalanan ?? 0,
              bacaanOdometer: mostRecentCompleted.bacaanOdometerCheckout ?? 0,
              lokasiCheckout: mostRecentCompleted.lokasiCheckout ?? 'Auto-fixed',
              catatan: mostRecentCompleted.catatan,
              gpsLatitude: mostRecentCompleted.gpsLatitude,
              gpsLongitude: mostRecentCompleted.gpsLongitude,
            );
            
            final logKey = activeLog.id?.toString() ?? 'local_${activeLog.createdAt.millisecondsSinceEpoch}';
            await box.put(logKey, activeLog);
            fixedCount++;
          }
        }
      }
      
      print('DEBUG: Cleanup completed. Fixed $fixedCount inconsistent logs');
      
      // Final check
      final finalLogs = box.values.toList();
      final finalActiveLogs = finalLogs.where((log) => log.isActive).toList();
      print('DEBUG: After cleanup: ${finalActiveLogs.length} active logs remain');
      
    } catch (e) {
      print('DEBUG: Error during cleanup: $e');
    }
  }

  // Clear all data
  static Future<void> clearAll() async {
    await Hive.box<Auth>(HiveBoxes.auth).clear();
    await Hive.box<User>(HiveBoxes.user).clear();
    await Hive.box<Program>(HiveBoxes.programs).clear();
    await Hive.box<Vehicle>(HiveBoxes.vehicles).clear();
    await Hive.box<DriverLog>(HiveBoxes.driverLogs).clear();
    await Hive.box<SyncQueueItem>(HiveBoxes.syncQueue).clear();
  }

  // Clear synced completed logs
  static Future<int> clearSyncedCompletedLogs() async {
    print('DEBUG: Clearing synced completed logs');
    try {
      final box = Hive.box<DriverLog>(HiveBoxes.driverLogs);
      final allLogs = box.values.toList();
      
      // Find logs that are completed and synced
      final syncedCompletedLogs = allLogs.where((log) => 
        log.status == 'selesai' && 
        log.isSynced == true &&
        log.checkoutTime != null
      ).toList();
      
      print('DEBUG: Found ${syncedCompletedLogs.length} synced completed logs to clear');
      
      // Get all keys
      final allKeys = box.keys.toList();
      int removedCount = 0;
      
      // Delete synced completed logs
      for (var key in allKeys) {
        final log = box.get(key);
        if (log != null && 
            log.status == 'selesai' && 
            log.isSynced == true &&
            log.checkoutTime != null) {
          await box.delete(key);
          removedCount++;
          print('DEBUG: Deleted synced completed log with key: $key');
        }
      }
      
      print('DEBUG: Removed $removedCount synced completed logs');
      return removedCount;
    } catch (e) {
      print('DEBUG: Error clearing synced completed logs: $e');
      return 0;
    }
  }
  
  // Audit and fix all logs - comprehensive solution
  static Future<Map<String, dynamic>> auditAndFixAllLogs() async {
    print('DEBUG: Starting comprehensive audit and fix of all logs');
    try {
      final box = Hive.box<DriverLog>(HiveBoxes.driverLogs);
      final allLogs = box.values.toList();
      final allKeys = box.keys.toList();
      
      print('DEBUG: Total logs in Hive: ${allLogs.length}');
      
      // Results tracking
      int fixedActiveCount = 0;
      int removedDuplicateCount = 0;
      int fixedInconsistentCount = 0;
      List<String> fixedLogIds = [];
      
      // STEP 1: Group logs by program_id and kenderaan_id
      Map<String, List<DriverLog>> groupedLogs = {};
      Map<String, List<dynamic>> groupedKeys = {};
      
      for (int i = 0; i < allLogs.length; i++) {
        final log = allLogs[i];
        final key = allKeys[i];
        final groupKey = '${log.programId}_${log.kenderaanId}';
        
        if (!groupedLogs.containsKey(groupKey)) {
          groupedLogs[groupKey] = [];
          groupedKeys[groupKey] = [];
        }
        groupedLogs[groupKey]!.add(log);
        groupedKeys[groupKey]!.add(key);
      }
      
      print('DEBUG: Grouped logs into ${groupedLogs.length} program/vehicle combinations');
      
      // STEP 2: Process each group
      for (var groupKey in groupedLogs.keys) {
        final logs = groupedLogs[groupKey]!;
        final keys = groupedKeys[groupKey]!;
        
        print('DEBUG: Processing group $groupKey with ${logs.length} logs');
        
        // 2.1: Check for active logs
        final activeLogs = logs.where((log) => log.isActive).toList();
        
        if (activeLogs.length > 1) {
          print('DEBUG: Found ${activeLogs.length} active logs in group $groupKey');
          
          // Keep only the most recent active log
          activeLogs.sort((a, b) => b.createdAt.compareTo(a.createdAt));
          final mostRecentActive = activeLogs.first;
          
          // Fix all other active logs
          for (int i = 1; i < activeLogs.length; i++) {
            final log = activeLogs[i];
            final index = logs.indexOf(log);
            final key = keys[index];
            
            // Mark as completed
            log.status = 'selesai';
            log.checkoutTime = DateTime.now();
            await box.put(key, log);
            
            fixedActiveCount++;
            fixedLogIds.add(key.toString());
            print('DEBUG: Fixed duplicate active log with key: $key');
          }
        }
        
        // 2.2: Check for server logs and local logs
        final serverLogs = logs.where((log) => log.id != null).toList();
        final localLogs = logs.where((log) => log.id == null).toList();
        
        // If we have both server and local logs
        if (serverLogs.isNotEmpty && localLogs.isNotEmpty) {
          print('DEBUG: Found ${serverLogs.length} server logs and ${localLogs.length} local logs in group $groupKey');
          
          // For each server log, find and remove duplicate local logs
          for (var serverLog in serverLogs) {
            for (var localLog in localLogs) {
              // If they have the same status or similar check-in times
              if ((serverLog.status == localLog.status) || 
                  (serverLog.checkinTime != null && localLog.checkinTime != null &&
                  (serverLog.checkinTime!.difference(localLog.checkinTime!).inMinutes.abs() < 30))) {
                
                final index = logs.indexOf(localLog);
                final key = keys[index];
                
                // Delete the local log
                await box.delete(key);
                
                removedDuplicateCount++;
                fixedLogIds.add(key.toString());
                print('DEBUG: Removed duplicate local log with key: $key that matches server log ${serverLog.id}');
              }
            }
          }
        }
        
        // 2.3: Check for inconsistent status
        final remainingLogs = box.values.where((log) => 
          log.programId.toString() == groupKey.split('_')[0] && 
          log.kenderaanId.toString() == groupKey.split('_')[1]
        ).toList();
        
        for (var log in remainingLogs) {
          // Fix inconsistent status
          if (log.status == 'aktif' && log.checkoutTime != null) {
            final key = log.id?.toString() ?? 'local_${log.createdAt.millisecondsSinceEpoch}';
            log.status = 'selesai';
            await box.put(key, log);
            
            fixedInconsistentCount++;
            fixedLogIds.add(key);
            print('DEBUG: Fixed inconsistent status for log with key: $key');
          }
        }
      }
      
      // STEP 3: Final verification
      final finalLogs = box.values.toList();
      final finalActiveLogs = finalLogs.where((log) => log.isActive).toList();
      
      print('DEBUG: Audit and fix completed');
      print('DEBUG: Fixed $fixedActiveCount duplicate active logs');
      print('DEBUG: Removed $removedDuplicateCount duplicate local logs');
      print('DEBUG: Fixed $fixedInconsistentCount logs with inconsistent status');
      print('DEBUG: Remaining active logs: ${finalActiveLogs.length}');
      
      return {
        'success': true,
        'fixedActiveCount': fixedActiveCount,
        'removedDuplicateCount': removedDuplicateCount,
        'fixedInconsistentCount': fixedInconsistentCount,
        'remainingActiveCount': finalActiveLogs.length,
        'fixedLogIds': fixedLogIds,
      };
    } catch (e) {
      print('DEBUG: Error during audit and fix: $e');
      print('DEBUG: Error stack trace: ${StackTrace.current}');
      return {
        'success': false,
        'error': e.toString(),
      };
    }
  }
} 