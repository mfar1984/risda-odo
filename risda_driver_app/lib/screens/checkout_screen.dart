import 'package:flutter/material.dart';
import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:geolocator/geolocator.dart';
import 'package:image_picker/image_picker.dart';
import 'package:intl/intl.dart';
import 'package:dio/dio.dart';
import 'dart:io' show File, Platform, Directory;
import 'dart:typed_data';
import 'package:provider/provider.dart';
import 'package:path_provider/path_provider.dart';
 
import '../theme/pastel_colors.dart';
import '../theme/text_styles.dart';
import '../core/api_client.dart';
import '../services/api_service.dart';
import '../services/connectivity_service.dart';
import '../services/hive_service.dart';
import '../models/journey_hive_model.dart';

class CheckOutScreen extends StatefulWidget {
  const CheckOutScreen({super.key});

  @override
  State<CheckOutScreen> createState() => _CheckOutScreenState();
}

class _CheckOutScreenState extends State<CheckOutScreen> {
  // API Service
  final ApiService _apiService = ApiService(ApiClient());
  
  // Controllers
  final TextEditingController odometerController = TextEditingController();
  final TextEditingController programNameController = TextEditingController();
  final TextEditingController vehicleController = TextEditingController();
  final TextEditingController locationController = TextEditingController();
  final TextEditingController estimationKmController = TextEditingController();
  final TextEditingController requestByController = TextEditingController();
  final TextEditingController notesController = TextEditingController();
  final TextEditingController currentDateTimeController = TextEditingController();
  final TextEditingController gpsLocationController = TextEditingController();
  final TextEditingController lokasiTamatController = TextEditingController();

  // Fuel controllers
  final TextEditingController fuelLitersController = TextEditingController();
  final TextEditingController fuelCostController = TextEditingController();
  final TextEditingController gasStationController = TextEditingController();
  final TextEditingController noResitController = TextEditingController();

  // State
  bool isLoading = true;
  bool isSubmitting = false;
  String? errorMessage;
  bool addFuelInfo = false;
  
  // Location data
  String currentLocation = 'Getting location...';
  double? gpsLatitude;
  double? gpsLongitude;

  // Photo data (using XFile for cross-platform support)
  XFile? odometerPhoto;
  XFile? fuelReceiptPhoto;
  final ImagePicker _picker = ImagePicker();
  
  // Active journey data from API
  bool hasActiveTrip = false;
  int? activeJourneyId;
  int startOdometer = 0;

  @override
  void initState() {
    super.initState();
    _setCurrentDateTime();
    _getCurrentLocation();
    _loadActiveTrip();
  }

  Future<void> _saveEndJourneyOffline({
    required int odometerMasuk,
    double? liter,
    double? kos,
    String? stesen,
  }) async {
    try {
      final journey = HiveService.getActiveJourney();
      if (journey == null) {
        setState(() => isSubmitting = false);
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('Tiada perjalanan aktif (offline).'), backgroundColor: Colors.orange),
          );
        }
        return;
      }

      // Persist photos locally if present
      String? fotoMasukLocal = journey.fotoOdometerMasukLocal;
      String? resitLocal = journey.resitMinyakLocal;
      final appDir = await getApplicationDocumentsDirectory();
      if (odometerPhoto != null) {
        final dir = Directory('${appDir.path}/journeys/end');
        await dir.create(recursive: true);
        final fileName = 'end_${journey.localId}_${DateTime.now().millisecondsSinceEpoch}.jpg';
        final file = File('${dir.path}/$fileName');
        final bytes = await odometerPhoto!.readAsBytes();
        await file.writeAsBytes(bytes);
        fotoMasukLocal = file.path;
      }
      if (fuelReceiptPhoto != null) {
        final dir = Directory('${appDir.path}/journeys/receipts');
        await dir.create(recursive: true);
        final fileName = 'receipt_${journey.localId}_${DateTime.now().millisecondsSinceEpoch}.jpg';
        final file = File('${dir.path}/$fileName');
        final bytes = await fuelReceiptPhoto!.readAsBytes();
        await file.writeAsBytes(bytes);
        resitLocal = file.path;
      }

      // Update local journey
      journey.odometerMasuk = odometerMasuk;
      // Also set masa_masuk for offline guard consistency
      try {
        final now = DateTime.now();
        journey.masaMasuk = DateFormat('HH:mm').format(now);
      } catch (_) {}
      if (journey.odometerKeluar <= odometerMasuk) {
        journey.jarak = odometerMasuk - journey.odometerKeluar;
      }
      journey.literMinyak = liter;
      journey.kosMinyak = kos;
      journey.stesenMinyak = stesen;
      journey.fotoOdometerMasukLocal = fotoMasukLocal;
      journey.resitMinyakLocal = resitLocal;
      journey.lokasiTamatPerjalanan = lokasiTamatController.text.isNotEmpty ? lokasiTamatController.text : journey.lokasiTamatPerjalanan;
      journey.noResit = noResitController.text.isNotEmpty ? noResitController.text : journey.noResit;
      journey.lokasiCheckoutLat = gpsLatitude;
      journey.lokasiCheckoutLong = gpsLongitude;
      journey.status = 'selesai';
      journey.isSynced = false;
      journey.updatedAt = DateTime.now();
      journey.syncError = null;
      await journey.save();
      await HiveService.setJourneyActive(false);

      if (mounted) {
        setState(() => isSubmitting = false);
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Perjalanan ditamatkan (offline). Akan sync bila online.'), backgroundColor: Colors.orange),
        );
        Navigator.pop(context, true);
      }
    } catch (e) {
      if (mounted) {
        setState(() => isSubmitting = false);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Gagal tamat offline: $e'), backgroundColor: Colors.red),
        );
      }
    }
  }

  void _setCurrentDateTime() {
    final now = DateTime.now();
    final formatter = DateFormat('dd/MM/yyyy HH:mm');
    currentDateTimeController.text = formatter.format(now);
  }

  Future<void> _loadActiveTrip() async {
      setState(() {
        isLoading = true;
        errorMessage = null;
      });

    try {
      // Connectivity-aware load
      final isOnline = await context.read<ConnectivityService>().checkConnection();
      Map<String, dynamic>? journey;
      if (isOnline) {
        final response = await _apiService.getActiveJourney();
        journey = response['data'];
      }

      if (journey != null) {
        // Pre-fill form with active trip data from API
        activeJourneyId = journey['id'] as int;
        startOdometer = journey['odometer_keluar'] ?? 0;
        
        final program = journey['program'];
        final kenderaan = journey['kenderaan'];
        
        if (program != null) {
          programNameController.text = program['nama_program'] ?? '';
          locationController.text = program['lokasi_program'] ?? '';
          
          // Set Estimation KM
          if (program['jarak_anggaran'] != null) {
            estimationKmController.text = '${program['jarak_anggaran']} km';
          }
          
          // Set Request By
          if (program['permohonan_dari'] != null) {
            final requestBy = program['permohonan_dari'];
            requestByController.text = requestBy['nama_penuh'] ?? '';
          }
        }
        
        if (kenderaan != null) {
          vehicleController.text = '${kenderaan['no_plat']} - ${kenderaan['jenama']} ${kenderaan['model']}';
        }

      setState(() {
        isLoading = false;
          hasActiveTrip = true;
        });
      } else {
        // Offline or API says none -> try local active journey from Hive
        final j = HiveService.getActiveJourney();
        if (j != null) {
          // Prefill from local cache
          startOdometer = j.odometerKeluar;
          // Program name & location from ProgramHive or cached detail
          try {
            final cached = HiveService.settingsBox.get('program_detail_${j.programId}');
            if (cached is Map) {
              final cm = Map<String, dynamic>.from(cached);
              programNameController.text = cm['nama_program']?.toString() ?? programNameController.text;
              locationController.text = cm['lokasi_program']?.toString() ?? locationController.text;
              if (cm['jarak_anggaran'] != null) {
                estimationKmController.text = '${cm['jarak_anggaran']} km';
              }
              final req = cm['permohonan_dari'];
              if (req is Map) {
                requestByController.text = req['nama_penuh']?.toString() ?? requestByController.text;
              }
              final kd = cm['kenderaan'];
              if (kd is Map) {
                vehicleController.text = '${kd['no_plat'] ?? ''} - ${kd['jenama'] ?? ''} ${kd['model'] ?? ''}'.trim();
              }
            }
          } catch (_) {}
          // Fallback to VehicleHive text if still empty
          if (vehicleController.text.isEmpty) {
            try {
              final vehicles = HiveService.getAllVehicles();
              final v = vehicles.firstWhere((v) => v.id == j.kenderaanId, orElse: () => vehicles.first);
              vehicleController.text = '${v.noPendaftaran} - ${v.jenisKenderaan} ${v.model}';
            } catch (_) {}
          }

          setState(() {
            isLoading = false;
            hasActiveTrip = true; // allow checkout UI offline
            errorMessage = null;
            activeJourneyId = j.id; // may be null; online end will be guarded elsewhere
          });
        } else {
          setState(() {
            isLoading = false;
            hasActiveTrip = false;
            errorMessage = 'Tiada perjalanan aktif. Sila mulakan perjalanan terlebih dahulu.';
          });
        }
      }
    } catch (e) {
      setState(() {
        isLoading = false;
        hasActiveTrip = false;
        errorMessage = 'Gagal memuatkan perjalanan aktif: $e';
      });
    }
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
        gpsLocationController.text = currentLocation;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() {
        // Keep concise user-facing message
        currentLocation = 'Error getting location';
        gpsLocationController.text = currentLocation;
      });
    }
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
                  _pickImageFromCamera('odometer');
                },
              ),
              ListTile(
                leading: Icon(Icons.photo_library, color: PastelColors.primary),
                title: const Text('Pilih dari Gallery'),
                subtitle: const Text('Pilih gambar sedia ada'),
                onTap: () {
                  Navigator.pop(context);
                  _pickImageFromGallery('odometer');
                },
              ),
            ],
          ),
        );
      },
    );
  }

  Future<void> _takeFuelReceiptPhoto() async {
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
                  _pickImageFromCamera('fuel');
                },
              ),
              ListTile(
                leading: Icon(Icons.photo_library, color: PastelColors.primary),
                title: const Text('Pilih dari Gallery'),
                subtitle: const Text('Pilih gambar sedia ada'),
                onTap: () {
                  Navigator.pop(context);
                  _pickImageFromGallery('fuel');
                },
              ),
            ],
          ),
        );
      },
    );
  }

  Future<void> _pickImageFromCamera(String type) async {
    try {
      final XFile? photo = await _picker.pickImage(
        source: ImageSource.camera, 
        imageQuality: 80,
      );
      
      if (photo != null) {
        setState(() { 
          if (type == 'odometer') {
            odometerPhoto = photo; // Store XFile directly
          } else if (type == 'fuel') {
            fuelReceiptPhoto = photo; // Store XFile directly
          }
        });
        
        if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
              content: Text('Gambar ${type == 'odometer' ? 'odometer' : 'resit bahan api'} berjaya diambil!'),
            backgroundColor: Colors.green,
              duration: const Duration(seconds: 2),
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

  Future<void> _pickImageFromGallery(String type) async {
    try {
      final XFile? photo = await _picker.pickImage(
        source: ImageSource.gallery, 
        imageQuality: 80,
      );
      
      if (photo != null) {
        setState(() { 
          if (type == 'odometer') {
            odometerPhoto = photo; // Store XFile directly
          } else if (type == 'fuel') {
            fuelReceiptPhoto = photo; // Store XFile directly
          }
        });
        
        if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
              content: Text('Gambar ${type == 'odometer' ? 'odometer' : 'resit bahan api'} berjaya dipilih!'),
            backgroundColor: Colors.green,
              duration: const Duration(seconds: 2),
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

  Future<void> _submitCheckOut() async {
    // Centralized TripState: must be active to allow end
    final hasAnyActive = HiveService.isJourneyActive();
    if (!hasAnyActive) {
      setState(() { errorMessage = 'Tiada perjalanan aktif untuk ditamatkan'; });
      return;
    }

    // Validation
    if (odometerController.text.isEmpty) {
      setState(() {
        errorMessage = 'Sila masukkan bacaan odometer';
      });
      return;
    }

    final currentOdometer = int.tryParse(odometerController.text);
    if (currentOdometer == null) {
      setState(() {
        errorMessage = 'Bacaan odometer tidak sah';
      });
      return;
    }

    if (currentOdometer < startOdometer) {
      setState(() {
        errorMessage = 'Bacaan odometer tidak boleh kurang dari bacaan mula ($startOdometer km)';
      });
      return;
    }

    // Fuel validation (if checkbox is checked)
    if (addFuelInfo) {
      if (fuelLitersController.text.isEmpty) {
      setState(() {
          errorMessage = 'Sila masukkan jumlah liter bahan api';
      });
      return;
    }
      if (fuelCostController.text.isEmpty) {
      setState(() {
          errorMessage = 'Sila masukkan kos bahan api';
      });
      return;
      }
      if (gasStationController.text.isEmpty) {
        setState(() {
          errorMessage = 'Sila masukkan nama stesen minyak';
        });
        return;
      }
    }

    setState(() {
      isSubmitting = true;
      errorMessage = null;
    });

    try {
      // If offline, immediately save to Hive
      final isOnline = await context.read<ConnectivityService>().checkConnection();
      if (!isOnline) {
        await _saveEndJourneyOffline(
          odometerMasuk: currentOdometer,
          liter: addFuelInfo && fuelLitersController.text.isNotEmpty ? double.tryParse(fuelLitersController.text) : null,
          kos: addFuelInfo && fuelCostController.text.isNotEmpty ? double.tryParse(fuelCostController.text) : null,
          stesen: addFuelInfo && gasStationController.text.isNotEmpty ? gasStationController.text : null,
        );
        await HiveService.setJourneyActive(false);
        return;
      }
      // Prepare fuel data (only if checkbox is checked)
      final double? fuelLiters = addFuelInfo && fuelLitersController.text.isNotEmpty
          ? double.tryParse(fuelLitersController.text)
          : null;
      final double? fuelCost = addFuelInfo && fuelCostController.text.isNotEmpty
          ? double.tryParse(fuelCostController.text)
          : null;
      final String? gasStation = addFuelInfo && gasStationController.text.isNotEmpty
          ? gasStationController.text
          : null;

      // Read image bytes (works for both web & mobile)
      List<int>? odometerBytes;
      String? odometerFilename;
      if (odometerPhoto != null) {
        odometerBytes = await odometerPhoto!.readAsBytes();
        odometerFilename = odometerPhoto!.name;
      }

      List<int>? fuelReceiptBytes;
      String? fuelReceiptFilename;
      if (fuelReceiptPhoto != null) {
        fuelReceiptBytes = await fuelReceiptPhoto!.readAsBytes();
        fuelReceiptFilename = fuelReceiptPhoto!.name;
      }

      // Call API to end journey
      final response = await _apiService.endJourney(
        logId: activeJourneyId!,
        odometerMasuk: currentOdometer,
        lokasiCheckinLat: gpsLatitude,
        lokasiCheckinLong: gpsLongitude,
        lokasiTamatPerjalanan: lokasiTamatController.text.trim().isNotEmpty ? lokasiTamatController.text.trim() : null,
        catatan: notesController.text.isNotEmpty ? notesController.text : null,
        literMinyak: fuelLiters,
        kosMinyak: fuelCost,
        stesenMinyak: gasStation,
        noResit: noResitController.text.isNotEmpty ? noResitController.text : null,
        fotoOdometerMasukBytes: odometerBytes,
        fotoOdometerMasukFilename: odometerFilename,
        resitMinyakBytes: fuelReceiptBytes,
        resitMinyakFilename: fuelReceiptFilename,
      );

      if (mounted) {
        setState(() {
          isSubmitting = false;
        });

      // Update local Hive active journey to selesai so offline TripState inference matches
      try {
        final journey = HiveService.getActiveJourney();
        if (journey != null) {
          journey.odometerMasuk = currentOdometer;
          try {
            final now = DateTime.now();
            journey.masaMasuk = DateFormat('HH:mm').format(now);
          } catch (_) {}
          if (journey.odometerKeluar <= currentOdometer) {
            journey.jarak = currentOdometer - journey.odometerKeluar;
          }
          journey.lokasiCheckoutLat = gpsLatitude;
          journey.lokasiCheckoutLong = gpsLongitude;
          journey.status = 'selesai';
          journey.isSynced = true;
          journey.updatedAt = DateTime.now();
          await journey.save();
        }
      } catch (_) {}

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
            content: Text(response['message'] ?? 'Perjalanan berjaya ditamatkan!'),
          backgroundColor: Colors.green,
        ),
      );

      // Clear TripState (no active)
      await HiveService.setJourneyActive(false);

      Navigator.pop(context, true);
      }
    } catch (e) {
      if (mounted) {
        await _saveEndJourneyOffline(
          odometerMasuk: int.tryParse(odometerController.text) ?? startOdometer,
          liter: addFuelInfo && fuelLitersController.text.isNotEmpty ? double.tryParse(fuelLitersController.text) : null,
          kos: addFuelInfo && fuelCostController.text.isNotEmpty ? double.tryParse(fuelCostController.text) : null,
          stesen: addFuelInfo && gasStationController.text.isNotEmpty ? gasStationController.text : null,
        );
        return;
      }
    }
  }

  @override
  void dispose() {
    odometerController.dispose();
    programNameController.dispose();
    vehicleController.dispose();
    locationController.dispose();
    estimationKmController.dispose();
    requestByController.dispose();
    notesController.dispose();
    currentDateTimeController.dispose();
    gpsLocationController.dispose();
    lokasiTamatController.dispose();
    fuelLitersController.dispose();
    fuelCostController.dispose();
    gasStationController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
      return Scaffold(
        appBar: AppBar(
          backgroundColor: PastelColors.primary,
        title: Text('End Journey', style: AppTextStyles.h2.copyWith(color: Colors.white)),
          iconTheme: const IconThemeData(color: Colors.white),
        ),
        backgroundColor: PastelColors.background,
      body: isLoading
          ? Center(child: CircularProgressIndicator(color: PastelColors.primary))
          : !hasActiveTrip
              ? Center(
                  child: Padding(
                    padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
                        Icon(
                          Icons.error_outline,
                          size: 80,
                          color: PastelColors.warning,
                        ),
                        const SizedBox(height: 20),
                        Text(
                          'Tiada Trip Aktif',
                          style: AppTextStyles.h1.copyWith(color: PastelColors.textPrimary),
                        ),
                        const SizedBox(height: 12),
                        Text(
                          'Anda perlu check-in terlebih dahulu sebelum boleh check-out.',
                          textAlign: TextAlign.center,
                          style: AppTextStyles.bodyLarge.copyWith(color: PastelColors.textSecondary),
                        ),
                        const SizedBox(height: 24),
              ElevatedButton(
                onPressed: () => Navigator.pop(context),
                          style: ElevatedButton.styleFrom(
                            backgroundColor: PastelColors.primary,
                            foregroundColor: Colors.white,
                            padding: const EdgeInsets.symmetric(horizontal: 30, vertical: 15),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(8),
                            ),
                          ),
                          child: const Text('Kembali'),
                        ),
                      ],
                    ),
                  ),
                )
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

                          // Active Trip Info Banner
                          Container(
                            width: double.infinity,
                            padding: const EdgeInsets.all(12),
                            margin: const EdgeInsets.only(bottom: 16),
                            decoration: BoxDecoration(
                              color: PastelColors.success.withOpacity(0.1),
                              border: Border.all(color: PastelColors.success),
                              borderRadius: BorderRadius.circular(4),
                            ),
                            child: Row(
              children: [
                                Icon(Icons.check_circle, color: PastelColors.success, size: 20),
                                const SizedBox(width: 8),
                                Expanded(
                                  child: Text(
                                    'Trip Aktif - Sedia untuk check-out',
                                    style: AppTextStyles.bodyMedium.copyWith(
                                      color: PastelColors.successText,
                                      fontWeight: FontWeight.w600,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          ),

                          // Program Name (Disabled)
                _fieldLabel('Program Name'),
                const SizedBox(height: 4),
                TextField(
                      controller: programNameController,
                  enabled: false,
                  decoration: InputDecoration(
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

                          // Vehicle (Disabled)
                _fieldLabel('Vehicle'),
                const SizedBox(height: 4),
                TextField(
                      controller: vehicleController,
                  enabled: false,
                  decoration: InputDecoration(
                              border: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(3),
                              ),
                    isDense: true,
                              contentPadding: const EdgeInsets.symmetric(
                                horizontal: 12,
                                vertical: 10,
                              ),
                    prefixIcon: Icon(Icons.directions_car, color: PastelColors.primary, size: 20),
                  ),
                  style: AppTextStyles.bodyLarge,
                ),
                          const SizedBox(height: 8),
                          Container(
                            padding: const EdgeInsets.all(8),
                            decoration: BoxDecoration(
                              color: PastelColors.info.withOpacity(0.1),
                              border: Border.all(color: PastelColors.info),
                              borderRadius: BorderRadius.circular(4),
                            ),
                            child: Text(
                              'Start Odometer: $startOdometer km',
                              style: AppTextStyles.bodySmall.copyWith(
                                color: PastelColors.infoText,
                              ),
                            ),
                ),
                const SizedBox(height: 16),

                          // Current Odometer (Editable)
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

                          // Location Program (Disabled)
                          _fieldLabel('Location Program'),
                const SizedBox(height: 4),
                TextField(
                      controller: locationController,
                  enabled: false,
                  decoration: InputDecoration(
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

                          // Estimation KM (Disabled)
                _fieldLabel('Estimation KM'),
                const SizedBox(height: 4),
                TextField(
                      controller: estimationKmController,
                  enabled: false,
                  decoration: InputDecoration(
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

                          // Request By (Disabled)
                _fieldLabel('Request By'),
                const SizedBox(height: 4),
                TextField(
                      controller: requestByController,
                  enabled: false,
                  decoration: InputDecoration(
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

                          // Notes (Editable)
                _fieldLabel('Notes'),
                const SizedBox(height: 4),
                TextField(
                      controller: notesController,
                            maxLines: 3,
                  decoration: InputDecoration(
                              hintText: 'Add checkout notes (optional)',
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

                          // GPS Location (Disabled)
                _fieldLabel('GPS Location'),
                const SizedBox(height: 4),
                TextField(
                      controller: gpsLocationController,
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
                const SizedBox(height: 12),
                // Lokasi Tamat Perjalanan (manual text)
                _fieldLabel('Lokasi Tamat Perjalanan'),
                const SizedBox(height: 4),
                TextField(
                  controller: lokasiTamatController,
                  decoration: InputDecoration(
                    hintText: 'Contoh: Dewan Suarah Sibu',
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(3),
                    ),
                    isDense: true,
                    contentPadding: const EdgeInsets.symmetric(
                      horizontal: 12,
                      vertical: 10,
                    ),
                    prefixIcon: Icon(Icons.edit_location_alt, color: PastelColors.primary, size: 20),
                  ),
                  style: AppTextStyles.bodyLarge,
                ),
                const SizedBox(height: 16),

                          // Current Date/Time (Disabled)
                          _fieldLabel('Current Date/Time'),
                const SizedBox(height: 4),
                TextField(
                      controller: currentDateTimeController,
                  enabled: false,
                  decoration: InputDecoration(
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

                          // Fuel Information Checkbox
                          Container(
                            padding: const EdgeInsets.all(12),
                            decoration: BoxDecoration(
                              color: PastelColors.background,
                              border: Border.all(color: PastelColors.border),
                              borderRadius: BorderRadius.circular(4),
                            ),
                            child: CheckboxListTile(
                              title: Text(
                                'Tambah Maklumat Bahan Api',
                                style: AppTextStyles.bodyMedium.copyWith(
                                  fontWeight: FontWeight.w600,
                                  color: PastelColors.textPrimary,
                                ),
                              ),
                              subtitle: Text(
                                'Klik jika anda mengisi minyak semasa perjalanan ini',
                                style: AppTextStyles.bodySmall.copyWith(
                                  color: PastelColors.textSecondary,
                                ),
                              ),
                              value: addFuelInfo,
                              onChanged: (bool? value) {
                                setState(() {
                                  addFuelInfo = value ?? false;
                                });
                              },
                              activeColor: PastelColors.primary,
                              contentPadding: EdgeInsets.zero,
                            ),
                          ),

                          // Fuel Fields (shown when checkbox is checked)
                          if (addFuelInfo) ...[
                            const SizedBox(height: 16),
                            Container(
                              padding: const EdgeInsets.all(16),
                              decoration: BoxDecoration(
                                color: PastelColors.info.withOpacity(0.05),
                                border: Border.all(color: PastelColors.info),
                                borderRadius: BorderRadius.circular(4),
                              ),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Row(
                                    children: [
                                      Icon(Icons.local_gas_station, color: PastelColors.info, size: 20),
                                      const SizedBox(width: 8),
                                      Text(
                                        'Maklumat Bahan Api',
                                        style: AppTextStyles.bodyLarge.copyWith(
                                          fontWeight: FontWeight.w600,
                                          color: PastelColors.infoText,
                                        ),
                                      ),
                                    ],
                                  ),
                                  const SizedBox(height: 16),
                                  
                                  // Fuel Liters
                                  _fieldLabel('Liter Minyak'),
                                  const SizedBox(height: 4),
                                  TextField(
                                    controller: fuelLitersController,
                                    keyboardType: const TextInputType.numberWithOptions(decimal: true),
                                    decoration: InputDecoration(
                                      hintText: 'Contoh: 45.5',
                                      border: OutlineInputBorder(
                                        borderRadius: BorderRadius.circular(3),
                                      ),
                                      filled: true,
                                      fillColor: Colors.white,
                                      isDense: true,
                                      contentPadding: const EdgeInsets.symmetric(
                                        horizontal: 12,
                                        vertical: 10,
                                      ),
                                      prefixIcon: Icon(Icons.local_gas_station, color: PastelColors.primary, size: 20),
                                      suffixText: 'L',
                                    ),
                                    style: AppTextStyles.bodyLarge,
                                  ),
                                  const SizedBox(height: 12),

                                  // Fuel Cost
                                  _fieldLabel('Kos Bahan Api (RM)'),
                                  const SizedBox(height: 4),
                                  TextField(
                                    controller: fuelCostController,
                                    keyboardType: const TextInputType.numberWithOptions(decimal: true),
                                    decoration: InputDecoration(
                                      hintText: 'Contoh: 150.00',
                                      border: OutlineInputBorder(
                                        borderRadius: BorderRadius.circular(3),
                                      ),
                                      filled: true,
                                      fillColor: Colors.white,
                                      isDense: true,
                                      contentPadding: const EdgeInsets.symmetric(
                                        horizontal: 12,
                                        vertical: 10,
                                      ),
                                      prefixIcon: Icon(Icons.attach_money, color: PastelColors.primary, size: 20),
                                      prefixText: 'RM ',
                                    ),
                                    style: AppTextStyles.bodyLarge,
                                  ),
                                  const SizedBox(height: 12),

                                  // Gas Station
                                  _fieldLabel('Stesen Minyak'),
                                  const SizedBox(height: 4),
                                  TextField(
                                    controller: gasStationController,
                                    decoration: InputDecoration(
                                      hintText: 'Contoh: Petronas Bangi',
                                      border: OutlineInputBorder(
                                        borderRadius: BorderRadius.circular(3),
                                      ),
                                      filled: true,
                                      fillColor: Colors.white,
                                      isDense: true,
                                      contentPadding: const EdgeInsets.symmetric(
                                        horizontal: 12,
                                        vertical: 10,
                                      ),
                                      prefixIcon: Icon(Icons.store, color: PastelColors.primary, size: 20),
                                    ),
                                    style: AppTextStyles.bodyLarge,
                                  ),
                                  const SizedBox(height: 12),

                                  // Fuel Receipt Photo
                                  _fieldLabel('Resit Minyak (Optional)'),
                                  const SizedBox(height: 4),
                                  Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      ElevatedButton.icon(
                                        onPressed: _takeFuelReceiptPhoto,
                                        icon: const Icon(Icons.receipt, size: 18),
                                        label: Text('Upload Resit', style: AppTextStyles.bodyLarge),
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
                                      if (fuelReceiptPhoto != null)
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
                                              future: fuelReceiptPhoto!.readAsBytes(),
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
                                      const SizedBox(height: 12),
                                      // No. Resit input just after Upload Resit
                                      _fieldLabel('No. Resit (Optional)'),
                                      const SizedBox(height: 4),
                                      TextField(
                                        controller: noResitController,
                                        decoration: InputDecoration(
                                          hintText: 'Contoh: RCPT-2025-0001',
                                          border: OutlineInputBorder(
                                            borderRadius: BorderRadius.circular(3),
                                          ),
                                          isDense: true,
                                          contentPadding: const EdgeInsets.symmetric(
                                            horizontal: 12,
                                            vertical: 10,
                                          ),
                                          prefixIcon: Icon(Icons.confirmation_number, color: PastelColors.primary, size: 20),
                                        ),
                                        style: AppTextStyles.bodyLarge,
                                      ),
                                    ],
                                  ),
                                ],
                              ),
                            ),
                          ],
                          const SizedBox(height: 24),

                          // Submit Button
                SizedBox(
                  width: double.infinity,
                            height: 44,
                  child: ElevatedButton(
                        onPressed: isSubmitting ? null : _submitCheckOut,
                    style: ElevatedButton.styleFrom(
                                backgroundColor: PastelColors.warning,
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
                                      'End Journey',
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