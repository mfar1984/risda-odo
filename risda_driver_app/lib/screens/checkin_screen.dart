import 'package:flutter/material.dart';
import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:geolocator/geolocator.dart';
import 'package:image_picker/image_picker.dart';
import 'package:intl/intl.dart';
import 'dart:io' show File, Platform, Directory;
import 'dart:typed_data';
import 'package:provider/provider.dart';
import 'package:path_provider/path_provider.dart';
import 'package:uuid/uuid.dart';
import '../theme/pastel_colors.dart';
import '../theme/text_styles.dart';
import '../core/api_client.dart';
import '../services/api_service.dart';
import '../services/connectivity_service.dart';
import '../services/auth_service.dart';
import '../services/hive_service.dart';
import '../models/journey_hive_model.dart';

class CheckInScreen extends StatefulWidget {
  const CheckInScreen({super.key});

  @override
  State<CheckInScreen> createState() => _CheckInScreenState();
}

class _CheckInScreenState extends State<CheckInScreen> {
  // API Service
  final ApiService _apiService = ApiService(ApiClient());
  
  // Controllers
  final TextEditingController odometerController = TextEditingController();
  final TextEditingController locationController = TextEditingController();
  final TextEditingController estimationKmController = TextEditingController();
  final TextEditingController requestByController = TextEditingController();
  final TextEditingController notesController = TextEditingController();
  final TextEditingController currentDateTimeController = TextEditingController();
  
  // API data
  int? selectedProgramId;
  int? selectedVehicleId;
  String? selectedProgramName;
  String? selectedVehicleName;
  int? latestOdometer; // Latest odometer from vehicle's last journey
  
  List<Map<String, dynamic>> availablePrograms = [];

  // State
  bool isLoading = false;
  bool isSubmitting = false;
  String? errorMessage;
  bool hasActiveJourney = false; // Check if user has active journey
  
  // Location data
  String currentLocation = 'Getting location...';
  double? gpsLatitude;
  double? gpsLongitude;
  
  // Photo data
  XFile? odometerPhoto;
  final ImagePicker _picker = ImagePicker();

  @override
  void initState() {
    super.initState();
    _setCurrentDateTime();
    _getCurrentLocation();
    _loadUserData();
    // Offline-first guards: only call network when online
    WidgetsBinding.instance.addPostFrameCallback((_) async {
      // Fresh connectivity check to avoid stale status
      final isOnline = await context.read<ConnectivityService>().checkConnection();
      // ALWAYS reconcile TripState first (offline-aware)
      await _checkActiveJourney();
      if (isOnline) {
        await _loadPrograms();
      } else {
        // When offline, don't call API. Use cached programs assigned to current driver (user/staf) and active only.
        final auth = context.read<AuthService>();
        final now = DateTime.now();
        // Collect identifiers that may be used for assignment (user id, staf id)
        final List<String> driverIds = [];
        if (auth.userId != null) driverIds.add(auth.userId!.toString());
        try {
          final user = auth.currentUser;
          final stafId = user['user']?['staf']?['id'] ?? user['staf']?['id'];
          if (stafId != null) driverIds.add(stafId.toString());
        } catch (_) {}

        final allPrograms = HiveService.getAllPrograms();
        // Filter: assigned to current driver AND active (status label from cached detail has priority)
        final filtered = allPrograms.where((p) {
          // Assigned check: p.pemanduId is comma-separated
          bool assigned = false;
          if (p.pemanduId != null && p.pemanduId!.trim().isNotEmpty && driverIds.isNotEmpty) {
            final assignedIds = p.pemanduId!.split(',').map((e) => e.trim()).where((e) => e.isNotEmpty).toSet();
            assigned = driverIds.any((id) => assignedIds.contains(id));
          }
          if (!assigned) return false;
          // Active check: prefer cached detail's status if available
          try {
            final cached = HiveService.settingsBox.get('program_detail_${p.id}');
            if (cached is Map && (cached['status_label'] is String)) {
              final label = (cached['status_label'] as String).toLowerCase();
              if (label.contains('aktif')) return true;
              if (label.contains('selesai')) return false;
            }
          } catch (_) {}
          // Fallback to window + ProgramHive.status
          final withinTime = (p.tarikhMula == null || !now.isBefore(p.tarikhMula!)) &&
              (p.tarikhTamat == null || !now.isAfter(p.tarikhTamat!));
          final statusActive = p.status == 'sedang_berlangsung';
          return statusActive || withinTime;
        }).toList();

        // Build program list with vehicle enrichment from Hive + cached program detail
        final vehicles = {for (final v in HiveService.getAllVehicles()) v.id: v};
        final journeys = HiveService.getAllJourneys();
        final Map<int, Map<String, dynamic>> uniqueById = {};
        for (final p in filtered) {
          Map<String, dynamic>? veh;
          int? resolvedVid;
          try {
            // Prefer cached program detail if available
            final cached = HiveService.settingsBox.get('program_detail_${p.id}');
            if (cached is Map && cached['kenderaan'] is Map) {
              final km = Map<String, dynamic>.from(cached['kenderaan']);
              resolvedVid = (km['id'] is int) ? km['id'] as int : int.tryParse('${km['id']}');
              if (resolvedVid != null) {
                veh = {
                  'id': resolvedVid,
                  'no_plat': km['no_plat'],
                  'jenama': km['jenama'],
                  'model': km['model'],
                  if (km['latest_odometer'] != null) 'latest_odometer': km['latest_odometer'],
                };
              }
            }
          } catch (_) {}

          // Fallback to ProgramHive.kenderaanId string (comma-separated)
          if (resolvedVid == null && p.kenderaanId != null && p.kenderaanId!.isNotEmpty) {
            final ids = p.kenderaanId!
                .split(',')
                .map((e) => int.tryParse(e.trim()))
                .whereType<int>()
                .toList();
            if (ids.isNotEmpty) resolvedVid = ids.first;
          }

          // Enrich using VehicleHive + local journeys (program-agnostik)
          if (resolvedVid != null) {
            int? latest;
            if (vehicles.containsKey(resolvedVid)) {
              final v = vehicles[resolvedVid]!;
              veh ??= {
                'id': v.id,
                'no_plat': v.noPendaftaran,
                'jenama': v.jenisKenderaan,
                'model': v.model,
              };
              latest = v.bacanOdometerSemasaTerkini;
            }
            // Derive from local journeys regardless of vehicles cache
            for (final j in journeys) {
              if (j.kenderaanId == resolvedVid) {
                final candidate = j.odometerMasuk ?? j.odometerKeluar;
                if (latest == null || candidate > latest) latest = candidate;
              }
            }
            // Prefer maximum between any cached latest_odometer and derived latest
            final cachedLatest = (veh?['latest_odometer'] is int)
                ? veh!['latest_odometer'] as int
                : int.tryParse('${veh?['latest_odometer']}');
            if (cachedLatest != null) {
              if (latest == null || cachedLatest > latest) latest = cachedLatest;
            }
            if (latest != null) {
              (veh ??= {'id': resolvedVid})['latest_odometer'] = latest;
            }
          }

          uniqueById[p.id] = {
            'id': p.id,
            'nama_program': p.namaProgram,
            'lokasi_program': p.lokasi,
            'jarak_anggaran': p.jarakAnggaran,
            if (veh != null) 'kenderaan': veh,
          };
        }

        setState(() {
          availablePrograms = uniqueById.values.toList();
          if (selectedProgramId != null &&
              !availablePrograms.any((e) => e['id'] == selectedProgramId)) {
            selectedProgramId = null;
          }
          isLoading = false;
          errorMessage = null;
        });
      }
    });
  }

  Future<void> _saveJourneyOffline({required int pemanduId, required int odometerKeluar}) async {
    // Prevent multiple active journeys locally
    final activeLocal = HiveService.getActiveJourney();
    if (activeLocal != null) {
      if (mounted) {
        setState(() => isSubmitting = false);
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Masih ada perjalanan aktif. Sila tamatkan dahulu.'), backgroundColor: Colors.orange),
        );
      }
      return;
    }

    try {
      // Persist photo locally (if any)
      String? localPhotoPath;
      if (odometerPhoto != null) {
        final appDir = await getApplicationDocumentsDirectory();
        final dir = Directory('${appDir.path}/journeys/start');
        await dir.create(recursive: true);
        final localIdForName = const Uuid().v4();
        final fileName = 'start_${localIdForName}_${DateTime.now().millisecondsSinceEpoch}.jpg';
        final localFile = File('${dir.path}/$fileName');
        final bytes = await odometerPhoto!.readAsBytes();
        await localFile.writeAsBytes(bytes);
        localPhotoPath = localFile.path;
      }

      final now = DateTime.now();
      final localId = const Uuid().v4();
      final journey = JourneyHive(
        id: null,
        pemanduId: pemanduId,
        kenderaanId: selectedVehicleId!,
        programId: selectedProgramId,
        tarikhPerjalanan: now,
        masaKeluar: DateFormat('HH:mm').format(now),
        masaMasuk: null,
        destinasi: locationController.text.isNotEmpty ? locationController.text : 'N/A',
        catatan: notesController.text.isNotEmpty ? notesController.text : null,
        odometerKeluar: odometerKeluar,
        odometerMasuk: null,
        jarak: null,
        literMinyak: null,
        kosMinyak: null,
        stesenMinyak: null,
        resitMinyak: null,
        fotoOdometerKeluar: null,
        fotoOdometerMasuk: null,
        status: 'dalam_perjalanan',
        jenisOrganisasi: 'semua',
        organisasiId: null,
        diciptaOleh: pemanduId,
        dikemaskiniOleh: null,
        lokasiCheckinLat: gpsLatitude,
        lokasiCheckinLong: gpsLongitude,
        lokasiCheckoutLat: null,
        lokasiCheckoutLong: null,
        createdAt: now,
        updatedAt: now,
        localId: localId,
        isSynced: false,
        lastSyncAttempt: null,
        syncRetries: 0,
        syncError: null,
        fotoOdometerKeluarLocal: localPhotoPath,
        fotoOdometerMasukLocal: null,
        resitMinyakLocal: null,
      );

      await HiveService.saveJourney(journey);

      if (mounted) {
        setState(() => isSubmitting = false);
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Perjalanan disimpan offline. Akan sync bila online.'), backgroundColor: Colors.orange),
        );
        Navigator.pop(context, true);
      }
    } catch (e) {
      if (mounted) {
        setState(() => isSubmitting = false);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Gagal simpan offline: $e'), backgroundColor: Colors.red),
        );
      }
    }
  }
  
  Future<void> _checkActiveJourney() async {
    try {
      // Always prefer centralized TripState (offline-first authority)
      final connectivity = context.read<ConnectivityService>();
      final localActiveFlag = HiveService.isJourneyActive();
      setState(() { hasActiveJourney = localActiveFlag; });

      // If offline, stop here
      if (!connectivity.isOnline) {
        return;
      }

      // If online, only promote to active if local flag is true missing (e.g., first open after login)
      try {
        final response = await _apiService.getActiveJourney();
        final serverActive = response['success'] == true && response['data'] != null;
        if (localActiveFlag == false && serverActive) {
          // Cache server active for future, but DO NOT override if local said false due to an offline end just performed
          try {
            const uuid = Uuid();
            final jh = JourneyHive.fromJson(Map<String, dynamic>.from(response['data']), localId: uuid.v4());
            jh.isSynced = true; jh.status = 'dalam_perjalanan';
            await HiveService.saveJourney(jh);
          } catch (_) {}
          // Do not force UI to active if local decided false; we keep offline-first authority
        }
      } catch (_) {}
    } catch (e) {
      // No active journey or error - allow start journey
      setState(() { hasActiveJourney = HiveService.isJourneyActive(); });
    }
  }
  
  Future<void> _loadPrograms() async {
    setState(() {
      isLoading = true;
      errorMessage = null;
      selectedProgramId = null; // Reset selection
    });

    try {
      // Load Current + Ongoing programs (for starting journey)
      final currentResponse = await _apiService.getPrograms(status: 'current');
      final ongoingResponse = await _apiService.getPrograms(status: 'ongoing');
      
      final List<dynamic> currentPrograms = currentResponse['data'] ?? [];
      final List<dynamic> ongoingPrograms = ongoingResponse['data'] ?? [];
      
      // Combine and convert to Map
      final List<Map<String, dynamic>> combined = [
        ...currentPrograms.map((p) => Map<String, dynamic>.from(p)),
        ...ongoingPrograms.map((p) => Map<String, dynamic>.from(p)),
      ];
      
      // Remove duplicates by ID (using Map to ensure unique IDs)
      final Map<int, Map<String, dynamic>> uniquePrograms = {};
      for (var program in combined) {
        final id = program['id'] as int;
        uniquePrograms[id] = program;
      }
      
      setState(() {
        availablePrograms = uniquePrograms.values.toList();
        isLoading = false;
      });
    } catch (e) {
      // Fallback to offline cache when API fails
      try {
        final auth = context.read<AuthService>();
        final now = DateTime.now();
        final List<String> driverIds = [];
        if (auth.userId != null) driverIds.add(auth.userId!.toString());
        try {
          final user = auth.currentUser;
          final stafId = user['user']?['staf']?['id'] ?? user['staf']?['id'];
          if (stafId != null) driverIds.add(stafId.toString());
        } catch (_) {}

        final allPrograms = HiveService.getAllPrograms();
        final filtered = allPrograms.where((p) {
          bool assigned = false;
          if (p.pemanduId != null && p.pemanduId!.trim().isNotEmpty && driverIds.isNotEmpty) {
            final assignedIds = p.pemanduId!.split(',').map((e) => e.trim()).where((e) => e.isNotEmpty).toSet();
            assigned = driverIds.any((id) => assignedIds.contains(id));
          }
          if (!assigned) return false;
          final withinTime = (p.tarikhMula == null || !now.isBefore(p.tarikhMula!)) &&
              (p.tarikhTamat == null || !now.isAfter(p.tarikhTamat!));
          final statusActive = p.status == 'sedang_berlangsung';
          return statusActive || withinTime;
        }).toList();

        final vehicles = {for (final v in HiveService.getAllVehicles()) v.id: v};
        final Map<int, Map<String, dynamic>> uniqueById = {};
        for (final p in filtered) {
          Map<String, dynamic>? veh;
          try {
            int? vid;
            if (p.kenderaanId != null && p.kenderaanId!.isNotEmpty) {
              final ids = p.kenderaanId!
                  .split(',')
                  .map((e) => int.tryParse(e.trim()))
                  .whereType<int>()
                  .toList();
              if (ids.isNotEmpty) vid = ids.first;
            }
            if (vid != null && vehicles.containsKey(vid)) {
              final v = vehicles[vid]!;
              veh = {
                'id': v.id,
                'no_plat': v.noPendaftaran,
                'jenama': v.jenisKenderaan,
                'model': v.model,
                'latest_odometer': v.bacanOdometerSemasaTerkini,
              };
            }
          } catch (_) {}

          uniqueById[p.id] = {
            'id': p.id,
            'nama_program': p.namaProgram,
            'lokasi_program': p.lokasi,
            'jarak_anggaran': p.jarakAnggaran,
            if (veh != null) 'kenderaan': veh,
          };
        }

        setState(() {
          availablePrograms = uniqueById.values.toList();
          isLoading = false;
          errorMessage = null; // show offline data silently
        });
      } catch (_) {
      setState(() {
        isLoading = false;
        errorMessage = 'Gagal memuatkan program: $e';
      });
      }
    }
  }

  void _setCurrentDateTime() {
    final now = DateTime.now();
    final formatter = DateFormat('dd/MM/yyyy HH:mm');
    currentDateTimeController.text = formatter.format(now);
  }

  void _loadUserData() {
    // 🎨 DUMMY MODE - Load user data
    requestByController.text = 'Muhammad Faizan';
  }

  Future<void> _getCurrentLocation() async {
    try {
      // Check if location services are enabled
      bool serviceEnabled = await Geolocator.isLocationServiceEnabled();
      if (!serviceEnabled) {
        setState(() {
          currentLocation = 'Location services disabled';
        });
        return;
      }

      // Check location permission
      LocationPermission permission = await Geolocator.checkPermission();
      if (permission == LocationPermission.denied) {
        permission = await Geolocator.requestPermission();
        if (permission == LocationPermission.denied) {
          setState(() {
            currentLocation = 'Location permission denied';
          });
          return;
        }
      }

      if (permission == LocationPermission.deniedForever) {
        setState(() {
          currentLocation = 'Location permissions permanently denied';
        });
        return;
      }

      // Get current position with timeout
      Position position = await Geolocator.getCurrentPosition(
        desiredAccuracy: LocationAccuracy.high,
        timeLimit: const Duration(seconds: 10), // Add timeout
      );

      if (!mounted) return;
      setState(() {
        gpsLatitude = position.latitude;
        gpsLongitude = position.longitude;
        currentLocation = '${position.latitude.toStringAsFixed(4)}, ${position.longitude.toStringAsFixed(4)}';
      });
    } catch (e) {
      if (!mounted) return;
      setState(() {
        // Keep message concise for users
        currentLocation = 'Error getting location';
      });
    }
  }

  void _onProgramChanged(int? programId) {
    setState(() {
      selectedProgramId = programId;
      
      // Auto-fill based on selected program from API
      if (programId != null) {
        final program = availablePrograms.firstWhere(
          (p) => p['id'] == programId,
          orElse: () => {},
        );
        
        if (program.isNotEmpty) {
          selectedProgramName = program['nama_program'] ?? '';
          locationController.text = program['lokasi_program'] ?? '';
          estimationKmController.text = (program['jarak_anggaran'] ?? '').toString();
          notesController.text = program['penerangan'] ?? '';
          
          // Assign vehicle strictly from program assignment (no guessing)
          final kenderaan = program['kenderaan'];
          if (kenderaan != null && kenderaan['id'] != null) {
            selectedVehicleId = kenderaan['id'] as int;
            selectedVehicleName = '${kenderaan['no_plat']} - ${kenderaan['jenama']} ${kenderaan['model']}';
            latestOdometer = kenderaan['latest_odometer'] as int?;
            // If missing, fallback to VehicleHive & local journeys to compute latest
            if (latestOdometer == null) {
              try {
                final vehicles = HiveService.getAllVehicles();
                final v = vehicles.firstWhere((x) => x.id == selectedVehicleId, orElse: () => vehicles.first);
                int? latest = v.bacanOdometerSemasaTerkini;
                // Consider ALL local journeys for this vehicle (program-agnostic)
                final journeys = HiveService.getAllJourneys()
                    .where((j) => j.kenderaanId == v.id)
                    .toList()
                  ..sort((a, b) => (b.createdAt ?? DateTime.fromMillisecondsSinceEpoch(0))
                      .compareTo(a.createdAt ?? DateTime.fromMillisecondsSinceEpoch(0)));
                for (final j in journeys) {
                  final endOdo = j.odometerMasuk ?? j.odometerKeluar;
                  final startOdo = j.odometerKeluar;
                  final candidate = (endOdo >= startOdo) ? endOdo : startOdo;
                  if (latest == null || candidate > latest) latest = candidate;
                }
                latestOdometer = latest;
              } catch (_) {}
            }
          } else {
            selectedVehicleId = null;
            selectedVehicleName = null;
            latestOdometer = null;
          }
          
          // Set requestor name
          final requestor = program['permohonan_dari'];
          if (requestor != null && requestor['nama_penuh'] != null) {
            requestByController.text = requestor['nama_penuh'];
          }
        }
      } else {
        selectedProgramName = null;
        selectedVehicleId = null;
        selectedVehicleName = null;
        latestOdometer = null;
        locationController.clear();
        estimationKmController.clear();
        notesController.clear();
      }
    });
  }

  int? _getSuggestedStartOdometer(int vehicleId) {
    int? suggested = latestOdometer;
    try {
      // From Vehicle cache
      final vehicles = HiveService.getAllVehicles();
      for (final v in vehicles) {
        if (v.id == vehicleId) {
          if (v.bacanOdometerSemasaTerkini != null) {
            suggested = suggested == null
                ? v.bacanOdometerSemasaTerkini
                : (v.bacanOdometerSemasaTerkini! > suggested ? v.bacanOdometerSemasaTerkini : suggested);
          }
          break;
        }
      }
      // From local journeys (max odometerMasuk)
      final journeys = HiveService.getAllJourneys();
      for (final j in journeys) {
        if (j.kenderaanId == vehicleId && j.odometerMasuk != null) {
          final endOdo = j.odometerMasuk!;
          if (suggested == null || endOdo > suggested) suggested = endOdo;
        }
      }
    } catch (_) {}
    return suggested;
  }

  Future<void> _takeOdometerPhoto() async {
    showModalBottomSheet(
      context: context,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(16)),
      ),
      builder: (BuildContext context) {
        return Container(
          padding: const EdgeInsets.all(16),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              ListTile(
                leading: Icon(Icons.camera_alt, color: PastelColors.primary),
                title: const Text('Ambil Gambar'),
                subtitle: const Text('Gunakan camera'),
                onTap: () {
                  Navigator.pop(context);
                  _pickImageFromCamera();
                },
              ),
              ListTile(
                leading: Icon(Icons.photo_library, color: PastelColors.primary),
                title: const Text('Pilih dari Gallery'),
                subtitle: const Text('Pilih gambar sedia ada'),
                onTap: () {
                  Navigator.pop(context);
                  _pickImageFromGallery();
                },
              ),
            ],
          ),
        );
      },
    );
  }

  Future<void> _pickImageFromCamera() async {
    try {
      final XFile? photo = await _picker.pickImage(
        source: ImageSource.camera,
        imageQuality: 80,
      );
      
      if (photo != null) {
        setState(() {
          odometerPhoto = photo; // Store XFile directly
        });
        
        if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
            content: Text('Gambar odometer berjaya diambil!'),
            backgroundColor: Colors.green,
            duration: Duration(seconds: 2),
          ),
        );
        }
      }
    } catch (e) {
      if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
            content: Text('Camera error: $e'),
          backgroundColor: Colors.orange,
            duration: const Duration(seconds: 3),
        ),
      );
      }
    }
  }

  Future<void> _pickImageFromGallery() async {
    try {
      final XFile? photo = await _picker.pickImage(
        source: ImageSource.gallery,
        imageQuality: 80,
      );
      
      if (photo != null) {
        setState(() {
          odometerPhoto = photo; // Store XFile directly
        });
        
        if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Gambar odometer berjaya dipilih!'),
            backgroundColor: Colors.green,
            duration: Duration(seconds: 2),
          ),
        );
        }
      }
    } catch (e) {
      if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
            content: Text('Gallery error: $e'),
          backgroundColor: Colors.orange,
            duration: const Duration(seconds: 3),
          ),
        );
      }
    }
  }

  Future<void> _submitCheckIn() async {
    // Hard block if there is an active journey (offline/online parity)
    try {
      final connectivity = context.read<ConnectivityService>();
      final isOnlineNow = await connectivity.checkConnection();
      if (!isOnlineNow) {
        // Offline-first: rely solely on centralized TripState
        if (HiveService.isJourneyActive()) {
          setState(() { hasActiveJourney = true; });
          return;
        }
      } else {
        try {
          final resp = await _apiService.getActiveJourney();
          if (resp['success'] == true && resp['data'] != null) {
            // Cache to Hive for offline gating and block start
            try {
              const uuid = Uuid();
              final jh = JourneyHive.fromJson(Map<String, dynamic>.from(resp['data']), localId: uuid.v4());
              jh.isSynced = true; jh.status = 'dalam_perjalanan';
              await HiveService.saveJourney(jh);
            } catch (_) {}
            setState(() { hasActiveJourney = true; });
            return;
          }
        } catch (_) {}
      }
    } catch (_) {}

    // Validation
    if (selectedProgramId == null) {
      setState(() {
        errorMessage = 'Sila pilih program';
      });
      return;
    }

    if (selectedVehicleId == null) {
      setState(() {
        errorMessage = 'Sila pilih kenderaan';
      });
      return;
    }

    if (odometerController.text.isEmpty) {
      setState(() {
        errorMessage = 'Sila masukkan bacaan odometer';
      });
      return;
    }

    // Validate odometer is numeric
    final odometer = int.tryParse(odometerController.text);
    if (odometer == null) {
      setState(() {
        errorMessage = 'Bacaan odometer tidak sah';
      });
      return;
    }

    // Validate odometer monotonic (must not be below suggested)
    if (selectedVehicleId != null) {
      final suggested = _getSuggestedStartOdometer(selectedVehicleId!);
      if (suggested != null && odometer < suggested) {
        setState(() {
          errorMessage = 'Odometer mesti ≥ $suggested (bacaan terkini)';
        });
        return;
      }
    }

    setState(() {
      isSubmitting = true;
      errorMessage = null;
    });

    try {
      // Decide path based on fresh connectivity
      final isOnline = await context.read<ConnectivityService>().checkConnection();
      if (!isOnline) {
        final auth = context.read<AuthService>();
        if (auth.userId != null) {
          await _saveJourneyOffline(pemanduId: auth.userId!, odometerKeluar: odometer);
          await HiveService.setJourneyActive(true);
          return;
        }
      }
      // Read image bytes (works for both web & mobile)
      List<int>? odometerBytes;
      String? odometerFilename;
      if (odometerPhoto != null) {
        odometerBytes = await odometerPhoto!.readAsBytes();
        odometerFilename = odometerPhoto!.name;
      }

      // Call API to start journey
      final response = await _apiService.startJourney(
        programId: selectedProgramId!,
        kenderaanId: selectedVehicleId!,
        odometerKeluar: odometer,
        lokasiKeluarLat: gpsLatitude,
        lokasiKeluarLong: gpsLongitude,
        catatan: notesController.text.isNotEmpty ? notesController.text : null,
        fotoOdometerKeluarBytes: odometerBytes,
        fotoOdometerKeluarFilename: odometerFilename,
      );

      if (mounted) {
        setState(() {
          isSubmitting = false;
        });

        // Persist active journey to Hive so End Journey works offline
        try {
          if (response is Map && response['success'] == true && response['data'] is Map) {
            final data = Map<String, dynamic>.from(response['data']);
            const uuid = Uuid();
            final jh = JourneyHive.fromJson(data, localId: uuid.v4());
            jh.isSynced = true;
            jh.status = 'dalam_perjalanan';
            await HiveService.saveJourney(jh);
            await HiveService.setJourneyActive(true);
          }
        } catch (_) {}

        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(response['message'] ?? 'Perjalanan dimulakan!'),
            backgroundColor: Colors.green,
          ),
        );

        Navigator.pop(context, true);
      }
    } catch (e) {
      // Fallback: save offline on failure
      final auth = context.read<AuthService>();
      if (auth.userId != null) {
        await _saveJourneyOffline(pemanduId: auth.userId!, odometerKeluar: int.parse(odometerController.text));
        await HiveService.setJourneyActive(true);
        return;
      }
      if (mounted) {
        setState(() => isSubmitting = false);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Gagal memulakan perjalanan: $e'), backgroundColor: Colors.red),
        );
      }
    }
  }

  @override
  void dispose() {
    odometerController.dispose();
    locationController.dispose();
    estimationKmController.dispose();
    requestByController.dispose();
    notesController.dispose();
    currentDateTimeController.dispose();
    super.dispose();
  }

  Widget _buildActiveJourneyWarning(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(24.0),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              Icons.warning_amber_rounded,
              size: 80,
              color: PastelColors.warning,
            ),
            const SizedBox(height: 24),
            Text(
              'Trip Aktif',
              style: AppTextStyles.h2.copyWith(
                color: PastelColors.textPrimary,
              ),
            ),
            const SizedBox(height: 12),
            Text(
              'Anda perlu End Journey terlebih dahulu sebelum boleh Start Journey lagi.',
              textAlign: TextAlign.center,
              style: AppTextStyles.bodyLarge.copyWith(
                color: PastelColors.textSecondary,
              ),
            ),
            const SizedBox(height: 32),
            ElevatedButton(
              onPressed: () => Navigator.pop(context),
              style: ElevatedButton.styleFrom(
                backgroundColor: PastelColors.primary,
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(horizontal: 32, vertical: 12),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(3),
                ),
              ),
              child: const Text('Kembali'),
            ),
          ],
        ),
      ),
    );
  }
  
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: PastelColors.primary,
        title: Text('Start Journey', style: AppTextStyles.h2.copyWith(color: Colors.white)),
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      backgroundColor: PastelColors.background,
      body: isLoading 
        ? Center(child: CircularProgressIndicator(color: PastelColors.primary))
        : hasActiveJourney
          ? _buildActiveJourneyWarning(context)
          : SingleChildScrollView(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 24),
        child: Card(
          color: PastelColors.cardBackground,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(8),
                  side: BorderSide(color: PastelColors.border),
                ),
          elevation: 0,
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 24),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                      // Error Message
                    if (errorMessage != null)
                      Container(
                        width: double.infinity,
                          padding: const EdgeInsets.all(12),
                          margin: const EdgeInsets.only(bottom: 16),
                        decoration: BoxDecoration(
                          color: PastelColors.error.withOpacity(0.1),
                          border: Border.all(color: PastelColors.error),
                          borderRadius: BorderRadius.circular(4),
                        ),
                          child: Text(
                              errorMessage!,
                            style: AppTextStyles.bodyMedium.copyWith(
                              color: PastelColors.errorText,
                            ),
                          ),
                        ),

                      // Program Name
                _fieldLabel('Program Name'),
                const SizedBox(height: 4),
                      isLoading
                        ? const Center(child: CircularProgressIndicator())
                        : errorMessage != null
                          ? Container(
                              padding: const EdgeInsets.all(12),
                              decoration: BoxDecoration(
                                color: Colors.red.shade50,
                                border: Border.all(color: Colors.red.shade300),
                                borderRadius: BorderRadius.circular(3),
                              ),
                              child: Row(
                                children: [
                                  Icon(Icons.error_outline, color: Colors.red.shade700, size: 20),
                                  const SizedBox(width: 8),
                                  Expanded(
                                    child: Text(
                                      errorMessage!,
                                      style: TextStyle(color: Colors.red.shade700, fontSize: 12),
                                    ),
                                  ),
                                  TextButton(
                                    onPressed: _loadPrograms,
                                    child: const Text('Cuba Lagi'),
                                  ),
                                ],
                              ),
                            )
                          : availablePrograms.isEmpty
                            ? Container(
                                padding: const EdgeInsets.all(12),
                                decoration: BoxDecoration(
                                  color: Colors.orange.shade50,
                                  border: Border.all(color: Colors.orange.shade300),
                                  borderRadius: BorderRadius.circular(3),
                                ),
                                child: Row(
                                  children: [
                                    Icon(Icons.info_outline, color: Colors.orange.shade700, size: 20),
                                    const SizedBox(width: 8),
                                    const Expanded(
                                      child: Text(
                                        'Tiada program tersedia untuk dimulakan',
                                        style: TextStyle(fontSize: 12),
                                      ),
                                    ),
                                  ],
                                ),
                              )
                            : DropdownButtonFormField<int>(
                                value: selectedProgramId,
                                items: availablePrograms
                                    .map((p) => DropdownMenuItem<int>(
                                          value: p['id'] as int,
                                          child: Text(p['nama_program'] ?? ''),
                                        ))
                                    .toList(),
                                onChanged: _onProgramChanged,
                                decoration: InputDecoration(
                                  hintText: 'Pilih Program',
                                  border: OutlineInputBorder(
                                    borderRadius: BorderRadius.circular(3),
                                  ),
                                  isDense: true,
                                  contentPadding: const EdgeInsets.symmetric(
                                    horizontal: 12,
                                    vertical: 10,
                                  ),
                                  prefixIcon: Icon(Icons.event, color: PastelColors.primary, size: 20),
                                ),
                                style: AppTextStyles.bodyLarge,
                              ),
                const SizedBox(height: 16),

                      // Vehicle (Auto-populated from program)
                _fieldLabel('Vehicle'),
                const SizedBox(height: 4),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 14),
                  decoration: BoxDecoration(
                    border: Border.all(color: Colors.grey.shade400),
                    borderRadius: BorderRadius.circular(3),
                    color: Colors.grey.shade100,
                  ),
                  child: Row(
                    children: [
                      Icon(Icons.directions_car, color: PastelColors.primary, size: 20),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Text(
                          selectedVehicleName ?? 'Tiada kenderaan ditetapkan untuk program ini',
                          style: AppTextStyles.bodyLarge.copyWith(
                            color: selectedVehicleName != null ? Colors.black87 : Colors.grey.shade600,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
                    if (selectedVehicleName != null) ...[
                      const SizedBox(height: 8),
                      Container(
                          padding: const EdgeInsets.all(8),
                        decoration: BoxDecoration(
                          color: PastelColors.info.withOpacity(0.1),
                          border: Border.all(color: PastelColors.info),
                          borderRadius: BorderRadius.circular(4),
                        ),
                        child: Text(
                            latestOdometer != null 
                              ? 'Current Vehicle Odometer: $latestOdometer km'
                              : 'Current Vehicle Odometer: Not Available (open Program Details online once to cache)',
                            style: AppTextStyles.bodySmall.copyWith(
                              color: PastelColors.infoText,
                            ),
                        ),
                      ),
                    ],
                const SizedBox(height: 16),

                      // Current Odometer
                _fieldLabel('Current Odometer'),
                const SizedBox(height: 4),
                TextField(
                  controller: odometerController,
                  keyboardType: TextInputType.number,
                  decoration: InputDecoration(
                    hintText: 'Enter current odometer',
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(3),
                          ),
                    isDense: true,
                          contentPadding: const EdgeInsets.symmetric(
                            horizontal: 12,
                            vertical: 10,
                          ),
                    prefixIcon: Icon(Icons.speed, color: PastelColors.primary, size: 20),
                  ),
                  style: AppTextStyles.bodyLarge,
                ),
                const SizedBox(height: 16),

                      // Odometer Photo
                _fieldLabel('Odometer Photo'),
                const SizedBox(height: 4),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        ElevatedButton.icon(
                              onPressed: _takeOdometerPhoto,
                            icon: const Icon(Icons.camera_alt, size: 18),
                              label: Text('Take Photo', style: AppTextStyles.bodyLarge),
                          style: ElevatedButton.styleFrom(
                            backgroundColor: PastelColors.primary,
                            foregroundColor: Colors.white,
                              padding: const EdgeInsets.symmetric(
                                horizontal: 16,
                                vertical: 10,
                              ),
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(3),
                              ),
                            ),
                    ),
                    const SizedBox(height: 8),
                        if (odometerPhoto != null)
                          Container(
                            width: 120,
                            height: 120,
                            decoration: BoxDecoration(
                              border: Border.all(color: PastelColors.border),
                              borderRadius: BorderRadius.circular(6),
                            ),
                            child: ClipRRect(
                              borderRadius: BorderRadius.circular(6),
                              child: FutureBuilder<Uint8List>(
                                future: odometerPhoto!.readAsBytes(),
                                builder: (context, snapshot) {
                                  if (snapshot.hasData) {
                                    return Image.memory(
                                      snapshot.data!,
                                      fit: BoxFit.cover,
                                    );
                                  } else {
                                    return Container(
                                      color: Colors.grey.shade300,
                                      child: const Center(
                                        child: CircularProgressIndicator(),
                                      ),
                                    );
                                  }
                                },
                              ),
                            ),
                          ),
                  ],
                ),
                const SizedBox(height: 16),

                      // Location Program
                _fieldLabel('Location Program'),
                const SizedBox(height: 4),
                TextField(
                      controller: locationController,
                  enabled: false,
                  decoration: InputDecoration(
                        hintText: 'Program location',
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(3),
                          ),
                    isDense: true,
                          contentPadding: const EdgeInsets.symmetric(
                            horizontal: 12,
                            vertical: 10,
                          ),
                    prefixIcon: Icon(Icons.location_on, color: PastelColors.primary, size: 20),
                  ),
                  style: AppTextStyles.bodyLarge,
                ),
                const SizedBox(height: 16),

                      // Estimation KM
                _fieldLabel('Estimation KM'),
                const SizedBox(height: 4),
                TextField(
                      controller: estimationKmController,
                  enabled: false,
                  decoration: InputDecoration(
                        hintText: 'Estimated kilometers',
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(3),
                          ),
                    isDense: true,
                          contentPadding: const EdgeInsets.symmetric(
                            horizontal: 12,
                            vertical: 10,
                          ),
                    prefixIcon: Icon(Icons.straighten, color: PastelColors.primary, size: 20),
                  ),
                  style: AppTextStyles.bodyLarge,
                ),
                const SizedBox(height: 16),

                      // Request By
                _fieldLabel('Request By'),
                const SizedBox(height: 4),
                TextField(
                      controller: requestByController,
                  enabled: false,
                  decoration: InputDecoration(
                        hintText: 'Requested by',
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(3),
                          ),
                    isDense: true,
                          contentPadding: const EdgeInsets.symmetric(
                            horizontal: 12,
                            vertical: 10,
                          ),
                    prefixIcon: Icon(Icons.person, color: PastelColors.primary, size: 20),
                  ),
                  style: AppTextStyles.bodyLarge,
                ),
                const SizedBox(height: 16),

                      // Notes
                _fieldLabel('Notes'),
                const SizedBox(height: 4),
                TextField(
                      controller: notesController,
                  enabled: false,
                  maxLines: 2,
                  decoration: InputDecoration(
                        hintText: 'Program notes',
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(3),
                          ),
                    isDense: true,
                          contentPadding: const EdgeInsets.symmetric(
                            horizontal: 12,
                            vertical: 10,
                          ),
                    prefixIcon: Icon(Icons.note_alt, color: PastelColors.primary, size: 20),
                  ),
                  style: AppTextStyles.bodyLarge,
                ),
                const SizedBox(height: 16),

                      // GPS Location
                _fieldLabel('GPS Location'),
                const SizedBox(height: 4),
                TextField(
                  enabled: false,
                  decoration: InputDecoration(
                        hintText: currentLocation,
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(3),
                          ),
                    isDense: true,
                          contentPadding: const EdgeInsets.symmetric(
                            horizontal: 12,
                            vertical: 10,
                          ),
                    prefixIcon: Icon(Icons.gps_fixed, color: PastelColors.primary, size: 20),
                  ),
                  style: AppTextStyles.bodyLarge,
                ),
                const SizedBox(height: 16),

                      // Current Date/Time
                      _fieldLabel('Current Date/Time'),
                const SizedBox(height: 4),
                TextField(
                      controller: currentDateTimeController,
                  enabled: false,
                  decoration: InputDecoration(
                        hintText: 'Current date and time',
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(3),
                          ),
                    isDense: true,
                          contentPadding: const EdgeInsets.symmetric(
                            horizontal: 12,
                            vertical: 10,
                          ),
                          prefixIcon: Icon(Icons.calendar_today, color: PastelColors.primary, size: 20),
                  ),
                  style: AppTextStyles.bodyLarge,
                ),
                      const SizedBox(height: 24),

                      // Submit Button
                SizedBox(
                  width: double.infinity,
                        height: 44,
                  child: ElevatedButton(
                        onPressed: isSubmitting ? null : _submitCheckIn,
                    style: ElevatedButton.styleFrom(
                            backgroundColor: PastelColors.success,
                      foregroundColor: Colors.white,
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(3),
                            ),
                    ),
                        child: isSubmitting
                              ? const SizedBox(
                                  height: 20,
                                  width: 20,
                                  child: CircularProgressIndicator(
                                    strokeWidth: 2,
                                    valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                                  ),
                                )
                              : Text(
                                  'Check In',
                                  style: AppTextStyles.h3.copyWith(color: Colors.white),
                                ),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _fieldLabel(String label) {
    return Text(
      label,
      style: AppTextStyles.bodyMedium.copyWith(
        fontWeight: FontWeight.w600,
        color: PastelColors.textPrimary,
      ),
    );
  }
} 