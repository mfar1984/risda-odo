import 'package:flutter/material.dart';
import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:geolocator/geolocator.dart';
import 'package:image_picker/image_picker.dart';
import 'package:intl/intl.dart';
import 'dart:io' show File, Platform;
import 'dart:typed_data';
import '../theme/pastel_colors.dart';
import '../theme/text_styles.dart';
import '../core/api_client.dart';
import '../services/api_service.dart';

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
    _checkActiveJourney(); // Check active journey first
    _loadPrograms();
  }
  
  Future<void> _checkActiveJourney() async {
    try {
      final response = await _apiService.getActiveJourney();
      
      if (response['success'] == true && response['data'] != null) {
        // User has active journey - should not start new one
        setState(() {
          hasActiveJourney = true;
        });
      } else {
        setState(() {
          hasActiveJourney = false;
        });
      }
    } catch (e) {
      // No active journey or error - allow start journey
      setState(() {
        hasActiveJourney = false;
      });
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
      setState(() {
        isLoading = false;
        errorMessage = 'Gagal memuatkan program: $e';
      });
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

      // Get current position
      Position position = await Geolocator.getCurrentPosition(
        desiredAccuracy: LocationAccuracy.high,
      );

      setState(() {
        gpsLatitude = position.latitude;
        gpsLongitude = position.longitude;
        currentLocation = '${position.latitude.toStringAsFixed(4)}, ${position.longitude.toStringAsFixed(4)}';
      });
    } catch (e) {
      setState(() {
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
          
          // Auto-select vehicle from program
          final kenderaan = program['kenderaan'];
          if (kenderaan != null && kenderaan['id'] != null) {
            selectedVehicleId = kenderaan['id'] as int;
            selectedVehicleName = '${kenderaan['no_plat']} - ${kenderaan['jenama']} ${kenderaan['model']}';
            latestOdometer = kenderaan['latest_odometer'] as int?; // Get latest odometer from API
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

    setState(() {
      isSubmitting = true;
      errorMessage = null;
    });

    try {
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

        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(response['message'] ?? 'Perjalanan dimulakan!'),
            backgroundColor: Colors.green,
          ),
        );

        Navigator.pop(context, true);
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          isSubmitting = false;
          errorMessage = 'Gagal memulakan perjalanan: ${e.toString()}';
        });

        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Error: ${e.toString()}'),
            backgroundColor: Colors.red,
          ),
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
                          selectedVehicleName ?? 'Pilih program untuk auto-pilih kenderaan',
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
                              : 'Current Vehicle Odometer: Not Available',
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