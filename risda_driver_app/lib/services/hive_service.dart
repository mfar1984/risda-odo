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
}

