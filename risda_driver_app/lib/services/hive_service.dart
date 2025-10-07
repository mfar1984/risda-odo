import 'package:hive_flutter/hive_flutter.dart';
import '../models/auth_hive_model.dart';
import '../models/journey_hive_model.dart';
import '../models/program_hive_model.dart';
import '../models/vehicle_hive_model.dart';
import '../models/claim_hive_model.dart';
import '../models/sync_queue_hive_model.dart';

class HiveService {
  // Box names
  static const String authBoxName = 'auth';
  static const String journeyBoxName = 'journeys';
  static const String programBoxName = 'programs';
  static const String vehicleBoxName = 'vehicles';
  static const String claimBoxName = 'claims';
  static const String syncQueueBoxName = 'sync_queue';
  static const String settingsBoxName = 'settings';

  /// Initialize Hive with all boxes
  static Future<void> init() async {
    // Initialize Hive Flutter
    await Hive.initFlutter();

    // Register all adapters
    Hive.registerAdapter(AuthHiveAdapter());         // TypeId: 5
    Hive.registerAdapter(JourneyHiveAdapter());      // TypeId: 0
    Hive.registerAdapter(ProgramHiveAdapter());      // TypeId: 1
    Hive.registerAdapter(VehicleHiveAdapter());      // TypeId: 2
    Hive.registerAdapter(ClaimHiveAdapter());        // TypeId: 3
    Hive.registerAdapter(SyncQueueHiveAdapter());    // TypeId: 4

    // Open all boxes
    await Hive.openBox<AuthHive>(authBoxName);
    await Hive.openBox<JourneyHive>(journeyBoxName);
    await Hive.openBox<ProgramHive>(programBoxName);
    await Hive.openBox<VehicleHive>(vehicleBoxName);
    await Hive.openBox<ClaimHive>(claimBoxName);
    await Hive.openBox<SyncQueueHive>(syncQueueBoxName);
    await Hive.openBox(settingsBoxName); // Dynamic box
  }

  /// Quick access to boxes
  static Box<AuthHive> get authBox => Hive.box<AuthHive>(authBoxName);
  static Box<JourneyHive> get journeyBox => Hive.box<JourneyHive>(journeyBoxName);
  static Box<ProgramHive> get programBox => Hive.box<ProgramHive>(programBoxName);
  static Box<VehicleHive> get vehicleBox => Hive.box<VehicleHive>(vehicleBoxName);
  static Box<ClaimHive> get claimBox => Hive.box<ClaimHive>(claimBoxName);
  static Box<SyncQueueHive> get syncQueueBox => Hive.box<SyncQueueHive>(syncQueueBoxName);
  static Box get settingsBox => Hive.box(settingsBoxName);

  // ===== AUTH OPERATIONS =====
  
  /// Get current auth
  static AuthHive? getCurrentAuth() {
    if (authBox.isEmpty) return null;
    return authBox.getAt(0);
  }

  /// Save auth (login)
  static Future<void> saveAuth(AuthHive auth) async {
    await authBox.clear(); // Only one auth at a time
    await authBox.add(auth);
  }

  /// Clear auth (logout)
  static Future<void> clearAuth() async {
    await authBox.clear();
  }

  // ===== JOURNEY OPERATIONS =====
  
  /// Get active journey (dalam_perjalanan)
  static JourneyHive? getActiveJourney() {
    try {
      return journeyBox.values
          .cast<JourneyHive?>()
          .firstWhere(
            (j) => j?.status == 'dalam_perjalanan',
            orElse: () => null,
          );
    } catch (e) {
      return null;
    }
  }

  /// Get all journeys
  static List<JourneyHive> getAllJourneys() {
    return journeyBox.values.toList();
  }

  /// Get pending sync journeys
  static List<JourneyHive> getPendingSyncJourneys() {
    return journeyBox.values.where((j) => !j.isSynced).toList();
  }

  /// Save journey
  static Future<void> saveJourney(JourneyHive journey) async {
    await journeyBox.add(journey);
  }

  /// Update journey
  static Future<void> updateJourney(JourneyHive journey) async {
    await journey.save(); // HiveObject method
  }

  /// Watch journeys for live updates
  static Stream<BoxEvent> watchJourneys() {
    return journeyBox.watch();
  }

  // ===== PROGRAM OPERATIONS =====
  
  /// Get all programs
  static List<ProgramHive> getAllPrograms() {
    return programBox.values.toList();
  }

  /// Get programs for current user (by pemandu_id)
  static List<ProgramHive> getProgramsForUser(int userId) {
    return programBox.values.where((p) {
      // Check if user ID is in pemandu_id field (comma-separated)
      if (p.pemanduId == null) return false;
      return p.pemanduId!.contains(userId.toString());
    }).toList();
  }

  /// Save programs (bulk)
  static Future<void> savePrograms(List<ProgramHive> programs) async {
    await programBox.clear();
    await programBox.addAll(programs);
  }

  /// Watch programs for live updates
  static Stream<BoxEvent> watchPrograms() {
    return programBox.watch();
  }

  // ===== VEHICLE OPERATIONS =====
  
  /// Get all vehicles
  static List<VehicleHive> getAllVehicles() {
    return vehicleBox.values.toList();
  }

  /// Get available vehicles (status = 'aktif')
  static List<VehicleHive> getAvailableVehicles() {
    return vehicleBox.values.where((v) => v.status == 'aktif').toList();
  }

  /// Save vehicles (bulk)
  static Future<void> saveVehicles(List<VehicleHive> vehicles) async {
    await vehicleBox.clear();
    await vehicleBox.addAll(vehicles);
  }

  /// Watch vehicles for live updates
  static Stream<BoxEvent> watchVehicles() {
    return vehicleBox.watch();
  }

  // ===== CLAIM OPERATIONS =====
  
  /// Get all claims
  static List<ClaimHive> getAllClaims() {
    return claimBox.values.toList();
  }

  /// Get pending sync claims
  static List<ClaimHive> getPendingSyncClaims() {
    return claimBox.values.where((c) => !c.isSynced).toList();
  }

  /// Save claim
  static Future<void> saveClaim(ClaimHive claim) async {
    await claimBox.add(claim);
  }

  /// Update claim (persist existing HiveObject)
  static Future<void> updateClaim(ClaimHive claim) async {
    await claim.save();
  }

  /// Find claim by localId
  static ClaimHive? getClaimByLocalId(String localId) {
    try {
      return claimBox.values.firstWhere((c) => c.localId == localId);
    } catch (_) {
      return null;
    }
  }

  /// Watch claims for live updates
  static Stream<BoxEvent> watchClaims() {
    return claimBox.watch();
  }

  // ===== SYNC QUEUE OPERATIONS =====
  
  /// Get all pending sync items
  static List<SyncQueueHive> getPendingSyncQueue() {
    return syncQueueBox.values.toList();
  }

  /// Get count of pending sync items
  static int getPendingSyncCount() {
    return syncQueueBox.length;
  }

  /// Add to sync queue
  static Future<void> addToSyncQueue(SyncQueueHive item) async {
    await syncQueueBox.add(item);
  }

  /// Remove from sync queue
  static Future<void> removeFromSyncQueue(String id) async {
    final item = syncQueueBox.values.firstWhere((q) => q.id == id);
    await item.delete();
  }

  /// Watch sync queue for live updates
  static Stream<BoxEvent> watchSyncQueue() {
    return syncQueueBox.watch();
  }

  // ===== SETTINGS OPERATIONS =====
  
  /// Get setting
  static dynamic getSetting(String key, {dynamic defaultValue}) {
    return settingsBox.get(key, defaultValue: defaultValue);
  }

  /// Save setting
  static Future<void> saveSetting(String key, dynamic value) async {
    await settingsBox.put(key, value);
  }

  // ===== UTILITY OPERATIONS =====
  
  /// Clear all data (for logout or reset)
  static Future<void> clearAllData() async {
    await authBox.clear();
    await journeyBox.clear();
    await programBox.clear();
    await vehicleBox.clear();
    await claimBox.clear();
    await syncQueueBox.clear();
    await settingsBox.clear();
  }

  /// Get total pending sync count (journeys + claims)
  static int getTotalPendingSyncCount() {
    final journeyCount = getPendingSyncJourneys().length;
    final claimCount = getPendingSyncClaims().length;
    final queueCount = getPendingSyncCount();
    return journeyCount + claimCount + queueCount;
  }

  // ===== DATA RETENTION & CLEANUP =====
  
  /// Data retention settings (60 days policy)
  static const int dataRetentionDays = 60;
  static const int maxJourneysToKeep = 150;  // Safety limit
  static const int maxClaimsToKeep = 150;    // Safety limit
  
  /// Clean old data (older than 60 days, already synced)
  /// Call this after successful sync or on app startup
  static Future<Map<String, int>> cleanOldData() async {
    final cutoffDate = DateTime.now().subtract(Duration(days: dataRetentionDays));
    int journeysDeleted = 0;
    int claimsDeleted = 0;
    int syncQueueDeleted = 0;
    
    // ============================================
    // CLEAN OLD JOURNEYS
    // ============================================
    final oldJourneys = journeyBox.values.where((j) => 
      j.isSynced == true &&                      // Only delete synced items
      j.status != 'dalam_perjalanan' &&          // Keep active journey
      j.createdAt != null &&
      j.createdAt!.isBefore(cutoffDate)          // Older than 60 days
    ).toList();
    
    for (var journey in oldJourneys) {
      await journey.delete();
      journeysDeleted++;
    }
    
    // ============================================
    // CLEAN OLD CLAIMS
    // ============================================
    final oldClaims = claimBox.values.where((c) => 
      c.isSynced == true &&                      // Only delete synced items
      c.status != 'pending' &&                   // Keep pending claims
      c.createdAt != null &&
      c.createdAt!.isBefore(cutoffDate)          // Older than 60 days
    ).toList();
    
    for (var claim in oldClaims) {
      await claim.delete();
      claimsDeleted++;
    }
    
    // ============================================
    // CLEAN FAILED SYNC QUEUE (older than 7 days, retries >= 3)
    // ============================================
    final oldQueueDate = DateTime.now().subtract(Duration(days: 7));
    final failedQueue = syncQueueBox.values.where((q) => 
      q.retries >= 3 &&
      q.createdAt.isBefore(oldQueueDate)
    ).toList();
    
    for (var item in failedQueue) {
      await item.delete();
      syncQueueDeleted++;
    }
    
    // Update last cleanup timestamp
    await settingsBox.put('last_cleanup', DateTime.now().toIso8601String());
    
    final stats = {
      'journeys_deleted': journeysDeleted,
      'claims_deleted': claimsDeleted,
      'sync_queue_deleted': syncQueueDeleted,
    };
    
    return stats;
  }
  
  /// Enforce storage limits (if exceeds max records)
  static Future<void> enforceStorageLimits() async {
    // Check journey count
    if (journeyBox.length > maxJourneysToKeep) {
      final excess = journeyBox.length - maxJourneysToKeep;
      
      // Delete oldest synced journeys
      final oldestSynced = journeyBox.values
          .where((j) => j.isSynced == true && j.status != 'dalam_perjalanan')
          .toList()
        ..sort((a, b) => (a.createdAt ?? DateTime.now()).compareTo(b.createdAt ?? DateTime.now()));
      
      for (var i = 0; i < excess && i < oldestSynced.length; i++) {
        await oldestSynced[i].delete();
      }
    }
    
    // Check claim count
    if (claimBox.length > maxClaimsToKeep) {
      final excess = claimBox.length - maxClaimsToKeep;
      
      // Delete oldest synced claims
      final oldestSynced = claimBox.values
          .where((c) => c.isSynced == true && c.status != 'pending')
          .toList()
        ..sort((a, b) => (a.createdAt ?? DateTime.now()).compareTo(b.createdAt ?? DateTime.now()));
      
      for (var i = 0; i < excess && i < oldestSynced.length; i++) {
        await oldestSynced[i].delete();
      }
    }
  }
  
  /// Get storage statistics
  static Map<String, dynamic> getStorageStats() {
    final stats = {
      'boxes': {
        'auth': authBox.length,
        'journeys': journeyBox.length,
        'programs': programBox.length,
        'vehicles': vehicleBox.length,
        'claims': claimBox.length,
        'sync_queue': syncQueueBox.length,
      },
      'pending_sync': {
        'journeys': getPendingSyncJourneys().length,
        'claims': getPendingSyncClaims().length,
        'queue': getPendingSyncCount(),
        'total': getTotalPendingSyncCount(),
      },
      'estimated_size_kb': _estimateStorageSize(),
      'last_cleanup': settingsBox.get('last_cleanup'),
    };
    return stats;
  }
  
  /// Estimate total storage size (rough calculation)
  static int _estimateStorageSize() {
    return (journeyBox.length * 1) +           // 1 KB per journey
           (claimBox.length * 0.6).toInt() +       // 0.6 KB per claim
           (programBox.length * 0.8).toInt() +     // 0.8 KB per program
           (vehicleBox.length * 0.4).toInt() +     // 0.4 KB per vehicle
           (syncQueueBox.length * 0.5).toInt();    // 0.5 KB per queue item
  }
  
  /// Check if cleanup is needed (weekly check)
  static bool shouldRunCleanup() {
    final lastCleanup = settingsBox.get('last_cleanup');
    if (lastCleanup == null) return true;
    
    try {
      final lastCleanupDate = DateTime.parse(lastCleanup);
      final daysSinceCleanup = DateTime.now().difference(lastCleanupDate).inDays;
      return daysSinceCleanup >= 7;  // Weekly cleanup
    } catch (e) {
      return true;
    }
  }
}

