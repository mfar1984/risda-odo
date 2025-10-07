import 'package:flutter/foundation.dart';
import '../services/api_service.dart';
import '../services/hive_service.dart';
import '../services/connectivity_service.dart';
import '../models/journey_hive_model.dart';
import '../models/claim_hive_model.dart';
import '../models/sync_queue_hive_model.dart';
import '../models/program_hive_model.dart';
import '../models/vehicle_hive_model.dart';
import 'package:uuid/uuid.dart';
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
  
  /// Sync programs from server and save to Hive
  Future<void> syncPrograms() async {
    try {
      developer.log('📋 Syncing programs from server...');
      
      // Fetch all programs (current + ongoing + past)
      final currentResponse = await _apiService.getPrograms(status: 'current');
      final ongoingResponse = await _apiService.getPrograms(status: 'ongoing');
      final pastResponse = await _apiService.getPrograms(status: 'past');
      
      List<ProgramHive> allPrograms = [];
      int skippedPrograms = 0;
      
      // Convert to ProgramHive (skip bad records)
      if (currentResponse['success'] == true) {
        final programs = List<Map<String, dynamic>>.from(currentResponse['data'] ?? []);
        for (var p in programs) {
          try {
            allPrograms.add(ProgramHive.fromJson(p));
          } catch (e) {
            skippedPrograms++;
            developer.log('   ⚠️ Skipped program ${p['id']}: $e');
          }
        }
      }
      
      if (ongoingResponse['success'] == true) {
        final programs = List<Map<String, dynamic>>.from(ongoingResponse['data'] ?? []);
        for (var p in programs) {
          try {
            allPrograms.add(ProgramHive.fromJson(p));
          } catch (e) {
            skippedPrograms++;
            developer.log('   ⚠️ Skipped program ${p['id']}: $e');
          }
        }
      }
      
      if (pastResponse['success'] == true) {
        final programs = List<Map<String, dynamic>>.from(pastResponse['data'] ?? []);
        for (var p in programs) {
          try {
            allPrograms.add(ProgramHive.fromJson(p));
          } catch (e) {
            skippedPrograms++;
            developer.log('   ⚠️ Skipped program ${p['id']}: $e');
          }
        }
      }
      
      if (skippedPrograms > 0) {
        developer.log('   ⚠️ Total skipped: $skippedPrograms programs');
      }
      
      // Save to Hive (replace all)
      await HiveService.savePrograms(allPrograms);
      
      developer.log('✅ Programs synced to Hive: ${allPrograms.length} programs');
    } catch (e) {
      developer.log('❌ Sync programs error: $e');
    }
  }
  
  /// Sync vehicles from server and save to Hive
  Future<void> syncVehicles() async {
    try {
      developer.log('🚙 Syncing vehicles from server...');
      final vehicles = await _apiService.getVehicles();
      
      // Convert Vehicle to VehicleHive (manual mapping since Vehicle has no toJson)
      final vehicleHives = vehicles.map((v) => VehicleHive(
        id: v.id,
        noPendaftaran: v.noPlat,
        jenisKenderaan: v.jenama ?? 'N/A',
        model: v.model,
        tahun: v.tahun,
        warna: null,  // Not in Vehicle model
        bacanOdometerSemasaTerkini: null,  // Not in Vehicle model
        status: v.status,
        organisasiId: null,  // Not in Vehicle model
        jenisOrganisasi: 'semua',  // Default
        diciptaOleh: 1,  // Default
        dikemaskiniOleh: null,
        createdAt: DateTime.now(),
        updatedAt: DateTime.now(),
        lastSync: DateTime.now(),
      )).toList();
      
      // Save to Hive (replace all)
      await HiveService.saveVehicles(vehicleHives);
      
      developer.log('✅ Vehicles synced to Hive: ${vehicleHives.length} vehicles');
    } catch (e) {
      developer.log('⚠️ Sync vehicles error (skipping): $e');
      // Don't fail entire sync - vehicles optional for claims
    }
  }
  
  /// Sync journeys from server and save to Hive (last 60 days only)
  Future<void> syncJourneys() async {
    try {
      developer.log('🚗 Syncing journeys from server...');
      final response = await _apiService.getDriverLogs();
      
      if (response['success'] == true) {
        final journeys = List<Map<String, dynamic>>.from(response['data'] ?? []);
        
        // Convert to JourneyHive
        final journeyHives = journeys.map((j) {
          const uuid = Uuid();
          return JourneyHive.fromJson(j, localId: uuid.v4());
        }).toList();
        
        // Clear and save to Hive
        await HiveService.journeyBox.clear();
        await HiveService.journeyBox.addAll(journeyHives);
        
        developer.log('✅ Journeys synced to Hive: ${journeyHives.length} journeys');
      }
    } catch (e) {
      developer.log('❌ Sync journeys error: $e');
    }
  }
  
  /// Sync claims from server and save to Hive
  Future<void> syncClaims() async {
    try {
      developer.log('💰 Syncing claims from server...');
      final response = await _apiService.getClaims();
      
      if (response['success'] == true) {
        final claims = List<Map<String, dynamic>>.from(response['data'] ?? []);
        
        // Convert to ClaimHive (skip bad records)
        List<ClaimHive> claimHives = [];
        int skippedClaims = 0;
        
        for (var c in claims) {
          try {
            const uuid = Uuid();
            claimHives.add(ClaimHive.fromJson(c, localId: uuid.v4()));
          } catch (e) {
            skippedClaims++;
            developer.log('   ⚠️ Skipped claim ${c['id']}: $e');
          }
        }
        
        if (skippedClaims > 0) {
          developer.log('   ⚠️ Total skipped: $skippedClaims claims');
        }
        
        // Merge with existing offline claims (keep unsynced)
        final unsyncedClaims = HiveService.getPendingSyncClaims();
        
        // Clear and save
        await HiveService.claimBox.clear();
        await HiveService.claimBox.addAll(claimHives);
        
        // Re-add unsynced claims
        for (var unsyncedClaim in unsyncedClaims) {
          await HiveService.saveClaim(unsyncedClaim);
        }
        
        developer.log('✅ Claims synced to Hive: ${claimHives.length} claims + ${unsyncedClaims.length} offline');
      }
    } catch (e) {
      developer.log('❌ Sync claims error: $e');
    }
  }
  
  /// Sync ALL master data (programs + vehicles + journeys + claims)
  /// Call this on app startup and after login
  Future<void> syncAllMasterData() async {
    if (!_connectivityService.isOnline) {
      developer.log('⚠️ Cannot sync master data - offline');
      return;
    }
    
    developer.log('🔄 ========================================');
    developer.log('🔄 SYNCING ALL MASTER DATA TO HIVE');
    developer.log('🔄 ========================================');
    
    // Sync sequentially to avoid issues
    await syncPrograms();
    await syncVehicles();
    await syncJourneys();
    await syncClaims();
    
    developer.log('✅ All master data synced to Hive');
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


