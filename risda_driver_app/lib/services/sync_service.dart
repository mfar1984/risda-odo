import 'package:flutter/foundation.dart';
import 'package:flutter/widgets.dart';
import '../services/api_service.dart';
import '../services/hive_service.dart';
import '../services/connectivity_service.dart';
import '../models/journey_hive_model.dart';
import '../models/claim_hive_model.dart';
import '../models/sync_queue_hive_model.dart';
import '../models/program_hive_model.dart';
import '../models/vehicle_hive_model.dart';
import 'package:uuid/uuid.dart';
import 'dart:io';
 

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
      if (_isAutoSyncEnabled) {
        syncPendingData();  // Auto-sync!
      }
    });
  }
  
  /// Enable/disable auto-sync
  void setAutoSync(bool enabled) {
    _isAutoSyncEnabled = enabled;
    notifyListeners();
    
  }
  
  // ============================================
  // MAIN SYNC OPERATION
  // ============================================
  
  /// Sync all pending data (journeys + claims + queue)
  /// This runs when offline → online transition
  Future<Map<String, dynamic>> syncPendingData() async {
    if (_isSyncing) {
      return {'success': false, 'message': 'Sync already in progress'};
    }
    
    if (!_connectivityService.isOnline) {
      return {'success': false, 'message': 'Device is offline'};
    }
    
    _isSyncing = true;
    // Defer notification to the end of current frame to avoid build-phase updates
    WidgetsBinding.instance.addPostFrameCallback((_) {
      notifyListeners();
    });
    
    
    
    int journeysSynced = 0;
    int journeysFailed = 0;
    int claimsSynced = 0;
    int claimsFailed = 0;
    int queueProcessed = 0;
    
    try {
      // ============================================
      // STEP 1: Sync Pending Journeys
      // ============================================

      final pendingJourneys = HiveService.getPendingSyncJourneys();

      for (var journey in pendingJourneys) {
        try {
          // Helper: read local file bytes
          Future<List<int>?> readBytes(String? path) async {
            if (path == null || path.isEmpty) return null;
            final f = File(path);
            if (!await f.exists()) return null;
            return await f.readAsBytes();
          }

          // CREATE (start) if no server id yet
          if (journey.id == null) {
            // Require necessary fields
            if (journey.programId == null || journey.kenderaanId == 0) {
              journey.syncError = 'Data tidak lengkap (program/kenderaan)';
              journey.syncRetries += 1;
              journey.lastSyncAttempt = DateTime.now();
              await journey.save();
              journeysFailed++;
              continue;
            }

            final fotoStartBytes = await readBytes(journey.fotoOdometerKeluarLocal);
            final respStart = await _apiService.startJourney(
              programId: journey.programId!,
              kenderaanId: journey.kenderaanId,
              odometerKeluar: journey.odometerKeluar,
              lokasiKeluarLat: journey.lokasiCheckinLat,
              lokasiKeluarLong: journey.lokasiCheckinLong,
              lokasiMulaPerjalanan: journey.lokasiMulaPerjalanan,
              catatan: journey.catatan,
              fotoOdometerKeluarBytes: fotoStartBytes,
              fotoOdometerKeluarFilename: journey.fotoOdometerKeluarLocal?.split(Platform.pathSeparator).last,
            );

            if (respStart['success'] == true) {
              final data = Map<String, dynamic>.from(respStart['data'] ?? {});
              journey.id = data['id'] ?? journey.id;
              journey.isSynced = journey.status != 'selesai';
              journey.syncError = null;
              journey.syncRetries = 0;
              journey.lastSyncAttempt = DateTime.now();
              await journey.save();
              // We created (or confirmed) an active journey on server; mark TripState active
              if (journey.status == 'dalam_perjalanan') {
                await HiveService.setJourneyActive(true);
              }
            } else {
              journey.syncError = respStart['message']?.toString();
              journey.syncRetries += 1;
              journey.lastSyncAttempt = DateTime.now();
              await journey.save();
              journeysFailed++;
              continue;
            }
          }

          // UPDATE (end) if journey completed locally
          if (journey.status == 'selesai' && journey.id != null) {
            final fotoEndBytes = await readBytes(journey.fotoOdometerMasukLocal);
            final resitBytes = await readBytes(journey.resitMinyakLocal);

            final respEnd = await _apiService.endJourney(
              logId: journey.id!,
              odometerMasuk: journey.odometerMasuk ?? journey.odometerKeluar,
              lokasiCheckinLat: journey.lokasiCheckoutLat ?? journey.lokasiCheckinLat,
              lokasiCheckinLong: journey.lokasiCheckoutLong ?? journey.lokasiCheckinLong,
              lokasiTamatPerjalanan: journey.lokasiTamatPerjalanan,
              catatan: journey.catatan,
              literMinyak: journey.literMinyak,
              kosMinyak: journey.kosMinyak,
              stesenMinyak: journey.stesenMinyak,
              noResit: journey.noResit,
              fotoOdometerMasukBytes: fotoEndBytes,
              fotoOdometerMasukFilename: journey.fotoOdometerMasukLocal?.split(Platform.pathSeparator).last,
              resitMinyakBytes: resitBytes,
              resitMinyakFilename: journey.resitMinyakLocal?.split(Platform.pathSeparator).last,
            );

            if (respEnd['success'] == true) {
              final data = Map<String, dynamic>.from(respEnd['data'] ?? {});
              // Update fields that may be normalized by server
              journey.jarak = data['jarak'] ?? journey.jarak;
              journey.isSynced = true;
              journey.syncError = null;
              journey.syncRetries = 0;
              journey.lastSyncAttempt = DateTime.now();
              await journey.save();
              // Journey ended on server -> mark TripState inactive
              await HiveService.setJourneyActive(false);
            // Update vehicle cached odometer for next suggestions
            try {
              final vehicles = HiveService.getAllVehicles();
              for (final v in vehicles) {
                if (v.id == journey.kenderaanId) {
                  final endOdo = journey.odometerMasuk ?? journey.odometerKeluar;
                  if (endOdo >= (v.bacanOdometerSemasaTerkini ?? 0)) {
                    v.bacanOdometerSemasaTerkini = endOdo;
                    await v.save();
                  }
                  break;
                }
              }
            } catch (_) {}
              journeysSynced++;
              continue;
            } else {
              journey.syncError = respEnd['message']?.toString();
              journey.syncRetries += 1;
              journey.lastSyncAttempt = DateTime.now();
              await journey.save();
              journeysFailed++;
              continue;
            }
          }

          // If reached here: start synced and still active → mark synced
          if (journey.status == 'dalam_perjalanan') {
            journey.isSynced = true;
            journey.syncError = null;
            journey.syncRetries = 0;
            journey.lastSyncAttempt = DateTime.now();
            await journey.save();
            await HiveService.setJourneyActive(true);
          }

          journeysSynced++;
        } catch (e) {
          journey.syncRetries += 1;
          journey.syncError = e.toString();
          journey.lastSyncAttempt = DateTime.now();
          try { await journey.save(); } catch (_) {}
          journeysFailed++;
        }
      }
      // After processing all pending, recompute TripState from Hive as source of truth
      await HiveService.recomputeAndSetJourneyActive();
      
      // ============================================
      // STEP 2: Sync Pending Claims
      // ============================================
      
      final pendingClaims = HiveService.getPendingSyncClaims();
      
      
      for (var claim in pendingClaims) {
        try {
          // Prepare optional receipt upload if local exists
          List<int>? resitBytes;
          String? resitFilename;
          if (claim.resitLocal != null && claim.resitLocal!.isNotEmpty) {
            final file = File(claim.resitLocal!);
            if (await file.exists()) {
              resitBytes = await file.readAsBytes();
              resitFilename = file.path.split(Platform.pathSeparator).last;
            }
          }

          if (claim.id == null) {
            // New claim → create on server
            
            // Resolve log id if missing (offline-created claim)
            int resolvedLogId = claim.logPemanduId ?? 0;
            if (resolvedLogId == 0) {
              try {
                // Prefer the most recent completed, synced journey around claim.createdAt
                final journeys = HiveService.getAllJourneys()
                    .where((j) => j.status == 'selesai' && j.id != null)
                    .toList()
                  ..sort((a, b) {
                    final aTime = a.createdAt ?? DateTime.fromMillisecondsSinceEpoch(0);
                    final bTime = b.createdAt ?? DateTime.fromMillisecondsSinceEpoch(0);
                    return (claim.createdAt ?? DateTime.now()).difference(aTime).abs().compareTo(
                           (claim.createdAt ?? DateTime.now()).difference(bTime).abs());
                  });
                if (journeys.isNotEmpty) {
                  resolvedLogId = journeys.first.id!;
                }
              } catch (_) {}
            }
            
            final resp = await _apiService.createClaim(
              logPemanduId: resolvedLogId,
              kategori: claim.kategori,
              jumlah: claim.jumlah,
              keterangan: claim.catatan,
              noResit: claim.noResit,
              resitBytes: resitBytes,
              resitFilename: resitFilename,
            );

            if (resp['success'] == true) {
              final data = Map<String, dynamic>.from(resp['data'] ?? {});
              // Update local record with server data
              claim.id = data['id'] ?? claim.id;
              // Normalize resit which might be a String or Map
              final r = data['resit'];
              if (r != null) {
                if (r is String) {
                  claim.resit = r;
                } else if (r is Map) {
                  claim.resit = r['url'] ?? r['path'] ?? r['storage_path'] ?? r['file_path'] ?? r['download_url'] ?? claim.resit;
                }
              }
              claim.status = data['status'] ?? claim.status;
              if (data['created_at'] != null) {
                try { claim.createdAt = DateTime.parse(data['created_at']); } catch (_) {}
              }
              if (data['tarikh_diproses'] != null) {
                try { claim.tarikhDiproses = DateTime.parse(data['tarikh_diproses']); } catch (_) {}
              }
              claim.isSynced = true;
              claim.syncError = null;
              claim.syncRetries = 0;
              claim.lastSyncAttempt = DateTime.now();
              await claim.save();
              claimsSynced++;
            } else {
              // Server rejected - keep offline and record error
              claim.syncRetries += 1;
              claim.syncError = resp['message']?.toString();
              claim.lastSyncAttempt = DateTime.now();
              await claim.save();
              claimsFailed++;
              
            }
          } else {
            // Existing claim edited offline → update on server
            
            final resp = await _apiService.updateClaim(
              id: claim.id!,
              kategori: claim.kategori,
              jumlah: claim.jumlah,
              keterangan: claim.catatan,
              noResit: claim.noResit,
              resitBytes: resitBytes,
              resitFilename: resitFilename,
            );

            if (resp['success'] == true) {
              final data = Map<String, dynamic>.from(resp['data'] ?? {});
              final r = data['resit'];
              if (r != null) {
                if (r is String) {
                  claim.resit = r;
                } else if (r is Map) {
                  claim.resit = r['url'] ?? r['path'] ?? r['storage_path'] ?? r['file_path'] ?? r['download_url'] ?? claim.resit;
                }
              }
              claim.status = data['status'] ?? claim.status;
              claim.isSynced = true;
              claim.syncError = null;
              claim.syncRetries = 0;
              claim.lastSyncAttempt = DateTime.now();
              await claim.save();
              claimsSynced++;
            } else {
              claim.syncRetries += 1;
              claim.syncError = resp['message']?.toString();
              claim.lastSyncAttempt = DateTime.now();
              await claim.save();
              claimsFailed++;
              
            }
          }
        } catch (e) {
          
          try {
            claim.syncRetries += 1;
            claim.syncError = e.toString();
            claim.lastSyncAttempt = DateTime.now();
            await claim.save();
          } catch (_) {}
          claimsFailed++;
        }
      }
      
      // ============================================
      // STEP 3: Process Sync Queue
      // ============================================
      
      final pendingQueue = HiveService.getPendingSyncQueue();
      
      
      for (var item in pendingQueue) {
        try {
          
          // TODO: Implement queue processing
          queueProcessed++;
        } catch (e) {
          
        }
      }
      
      // ============================================
      // STEP 4: Cleanup Old Data (After Sync!)
      // ============================================
      
      final cleanupStats = await HiveService.cleanOldData();
      
      
      // ============================================
      // STEP 5: Enforce Storage Limits
      // ============================================
      
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
      
      

      // Refresh claims from server to reflect latest server state/IDs
      try {
        await syncClaims();
      } catch (_) {}
      
      return {
        'success': true,
        'stats': _lastSyncStats,
      };
      
    } catch (e) {
      return {
        'success': false,
        'message': e.toString(),
      };
    } finally {
      _isSyncing = false;
      WidgetsBinding.instance.addPostFrameCallback((_) {
        notifyListeners();
      });
    }
  }
  
  // ============================================
  // SELECTIVE SYNC (FCM-Triggered)
  // ============================================
  
  /// Sync specific entity (triggered by FCM notification)
  Future<void> syncByType(String type) async {
    if (!_connectivityService.isOnline) return;
    
    
    
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
        
    }
  }
  
  /// Sync programs from server and save to Hive
  Future<void> syncPrograms() async {
    try {
      
      
      // Fetch all programs (current + ongoing + past)
      final currentResponse = await _apiService.getPrograms(status: 'current');
      final ongoingResponse = await _apiService.getPrograms(status: 'ongoing');
      final pastResponse = await _apiService.getPrograms(status: 'past');
      
      List<ProgramHive> allPrograms = [];
      int skippedPrograms = 0;
      final List<int> currentIds = [];
      final List<int> ongoingIds = [];
      final List<int> pastIds = [];
      
      // Convert to ProgramHive (skip bad records)
      if (currentResponse['success'] == true) {
        final programs = List<Map<String, dynamic>>.from(currentResponse['data'] ?? []);
        for (var p in programs) {
          try {
            final ph = ProgramHive.fromJson(p);
            allPrograms.add(ph);
            currentIds.add(ph.id);
          } catch (e) {
            skippedPrograms++;
          }
        }
      }
      
      if (ongoingResponse['success'] == true) {
        final programs = List<Map<String, dynamic>>.from(ongoingResponse['data'] ?? []);
        for (var p in programs) {
          try {
            final ph = ProgramHive.fromJson(p);
            allPrograms.add(ph);
            ongoingIds.add(ph.id);
          } catch (e) {
            skippedPrograms++;
          }
        }
      }
      
      if (pastResponse['success'] == true) {
        final programs = List<Map<String, dynamic>>.from(pastResponse['data'] ?? []);
        for (var p in programs) {
          try {
            final ph = ProgramHive.fromJson(p);
            allPrograms.add(ph);
            pastIds.add(ph.id);
          } catch (e) {
            skippedPrograms++;
          }
        }
      }
      
      if (skippedPrograms > 0) {
        
      }
      
      // Save to Hive (replace all)
      await HiveService.savePrograms(allPrograms);
      // Persist offline buckets for Do tab
      try {
        await HiveService.saveSetting('program_bucket_current', currentIds);
        await HiveService.saveSetting('program_bucket_ongoing', ongoingIds);
        await HiveService.saveSetting('program_bucket_past', pastIds);
      } catch (_) {}

      // Cache detailed program payloads for offline Program Details and Start Journey vehicle odometer
      // Only cache for programs that belong to the driver; backend already filters.
      for (final p in allPrograms) {
        try {
          final resp = await _apiService.getProgramDetail(p.id);
          if (resp['success'] == true && resp['data'] is Map) {
            await HiveService.saveSetting('program_detail_${p.id}', resp['data']);
          }
        } catch (_) {}
      }
      
      
    } catch (e) {
      
    }
  }
  
  /// Sync vehicles from server and save to Hive
  Future<void> syncVehicles() async {
    try {
      
      final vehicles = await _apiService.getVehicles();
      
      // If backend endpoint not available, keep existing cache (do nothing)
      if (vehicles.isEmpty) {
        return;
      }

      // Convert Vehicle to VehicleHive (manual mapping since Vehicle has no toJson)
      final vehicleHives = vehicles.map((v) => VehicleHive(
        id: v.id,
        noPendaftaran: v.noPlat,
        jenisKenderaan: v.jenama ?? 'N/A',
        model: v.model,
        tahun: v.tahun,
        warna: null,
        bacanOdometerSemasaTerkini: null,
        status: v.status,
        organisasiId: null,
        jenisOrganisasi: 'semua',
        diciptaOleh: 1,
        dikemaskiniOleh: null,
        createdAt: DateTime.now(),
        updatedAt: DateTime.now(),
        lastSync: DateTime.now(),
      )).toList();
      
      // Save to Hive (replace all)
      await HiveService.saveVehicles(vehicleHives);
      
      
    } catch (e) {
      
      // Don't fail entire sync - vehicles optional for claims
    }
  }
  
  /// Sync journeys from server and save to Hive (last 60 days only)
  Future<void> syncJourneys() async {
    try {
      
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

        // Update VehicleHive latest odometer based on downloaded journeys
        try {
          final byVehicle = <int, int>{};
          for (final j in journeyHives) {
            final endOdo = j.odometerMasuk ?? j.odometerKeluar;
            if (!byVehicle.containsKey(j.kenderaanId) || endOdo > (byVehicle[j.kenderaanId] ?? 0)) {
              byVehicle[j.kenderaanId] = endOdo;
            }
          }
          final vehicles = HiveService.getAllVehicles();
          for (final v in vehicles) {
            final latest = byVehicle[v.id];
            if (latest != null && (v.bacanOdometerSemasaTerkini == null || latest > v.bacanOdometerSemasaTerkini!)) {
              v.bacanOdometerSemasaTerkini = latest;
              await v.save();
            }
          }
        } catch (_) {}
        
        
      }
    } catch (e) {
      
    }
  }
  
  /// Sync claims from server and save to Hive
  Future<void> syncClaims() async {
    try {
      
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
          }
        }
        
        if (skippedClaims > 0) {
          
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
        
        
      }
    } catch (e) {
      
    }
  }
  
  /// Sync ALL master data (programs + vehicles + journeys + claims)
  /// Call this on app startup and after login
  Future<void> syncAllMasterData() async {
    if (!_connectivityService.isOnline) {
      return;
    }
    
    
    // Sync sequentially to avoid issues
    await syncPrograms();
    await syncVehicles();
    await syncJourneys();
    await syncClaims();
    
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
  
  /// Reconcile centralized TripState (journey_active) using server if online, otherwise Hive
  Future<bool> reconcileTripState() async {
    if (_connectivityService.isOnline) {
      try {
        final resp = await _apiService.getActiveJourney();
        final active = resp['success'] == true && resp['data'] != null;
        await HiveService.setJourneyActive(active);
        return active;
      } catch (e) {
        return await HiveService.recomputeAndSetJourneyActive();
      }
    } else {
      return await HiveService.recomputeAndSetJourneyActive();
    }
  }

  /// Ensure VehicleHive.bacanOdometerSemasaTerkini is populated from local journeys
  /// Call this after master sync or on login so offline Start can show latest odometer
  Future<void> ensureVehicleOdometerCache() async {
    try {
      final journeys = HiveService.getAllJourneys();
      if (journeys.isEmpty) return;
      final byVehicle = <int, int>{};
      for (final j in journeys) {
        final endOdo = j.odometerMasuk ?? j.odometerKeluar;
        final current = byVehicle[j.kenderaanId];
        if (current == null || endOdo > current) {
          byVehicle[j.kenderaanId] = endOdo;
        }
      }
      final vehicles = HiveService.getAllVehicles();
      for (final v in vehicles) {
        final latest = byVehicle[v.id];
        if (latest != null && (v.bacanOdometerSemasaTerkini == null || latest > v.bacanOdometerSemasaTerkini!)) {
          v.bacanOdometerSemasaTerkini = latest;
          await v.save();
        }
      }
    } catch (_) {}
  }

  /// Force sync now (manual trigger)
  Future<void> forceSyncNow() async {
    
    await syncPendingData();
  }

  /// Sync a single claim by localId (for retry from UI)
  Future<Map<String, dynamic>> syncSingleClaimByLocalId(String localId) async {
    if (!_connectivityService.isOnline) {
      return {'success': false, 'message': 'Device is offline'};
    }

    final claim = HiveService.getClaimByLocalId(localId);
    if (claim == null) {
      return {'success': false, 'message': 'Claim not found'};
    }

    try {
      // Prepare file if exists
      List<int>? resitBytes;
      String? resitFilename;
      if (claim.resitLocal != null && claim.resitLocal!.isNotEmpty) {
        final f = File(claim.resitLocal!);
        if (await f.exists()) {
          resitBytes = await f.readAsBytes();
          resitFilename = f.path.split(Platform.pathSeparator).last;
        }
      }

      Map<String, dynamic> resp;
      if (claim.id == null) {
        resp = await _apiService.createClaim(
          logPemanduId: claim.logPemanduId ?? 0,
          kategori: claim.kategori,
          jumlah: claim.jumlah,
          keterangan: claim.catatan,
          resitBytes: resitBytes,
          resitFilename: resitFilename,
        );
      } else {
        resp = await _apiService.updateClaim(
          id: claim.id!,
          kategori: claim.kategori,
          jumlah: claim.jumlah,
          keterangan: claim.catatan,
          resitBytes: resitBytes,
          resitFilename: resitFilename,
        );
      }

      if (resp['success'] == true) {
        final data = Map<String, dynamic>.from(resp['data'] ?? {});
        claim.id = data['id'] ?? claim.id;
        final r = data['resit'];
        if (r != null) {
          if (r is String) {
            claim.resit = r;
          } else if (r is Map) {
            claim.resit = r['url'] ?? r['path'] ?? r['storage_path'] ?? r['file_path'] ?? r['download_url'] ?? claim.resit;
          }
        }
        claim.status = data['status'] ?? claim.status;
        claim.isSynced = true;
        claim.syncError = null;
        claim.syncRetries = 0;
        claim.lastSyncAttempt = DateTime.now();
        await claim.save();
        return {'success': true};
      }

      claim.syncRetries += 1;
      claim.syncError = resp['message']?.toString();
      claim.lastSyncAttempt = DateTime.now();
      await claim.save();
      return {'success': false, 'message': claim.syncError};
    } catch (e) {
      claim.syncRetries += 1;
      claim.syncError = e.toString();
      claim.lastSyncAttempt = DateTime.now();
      await claim.save();
      return {'success': false, 'message': e.toString()};
    }
  }
}


