import 'package:flutter/material.dart';
import 'package:geolocator/geolocator.dart';
import 'package:image_picker/image_picker.dart';
import 'package:intl/intl.dart';
import 'dart:io';
import '../theme/pastel_colors.dart';
import '../theme/text_styles.dart';

class CheckInScreen extends StatefulWidget {
  const CheckInScreen({super.key});

  @override
  State<CheckInScreen> createState() => _CheckInScreenState();
}

class _CheckInScreenState extends State<CheckInScreen> {
  // Controllers
  final TextEditingController odometerController = TextEditingController();
  final TextEditingController locationController = TextEditingController();
  final TextEditingController estimationKmController = TextEditingController();
  final TextEditingController requestByController = TextEditingController();
  final TextEditingController notesController = TextEditingController();
  final TextEditingController currentDateTimeController = TextEditingController();

  // Dummy data - will be replaced with API data later
  String? selectedProgram;
  String? selectedVehicle;
  
  final List<String> availablePrograms = [
    'Program Jelajah Madani',
    'Program Inovasi Desa',
    'Program Komuniti Hijau',
  ];
  
  final List<String> availableVehicles = [
    'QAA1001 - Toyota Hilux',
    'QAA1002 - Nissan Navara',
    'QAA1003 - Ford Ranger',
  ];

  // State
  bool isLoading = false;
  bool isSubmitting = false;
  String? errorMessage;
  
  // Location data
  String currentLocation = 'Getting location...';
  double? gpsLatitude;
  double? gpsLongitude;
  
  // Photo data
  File? odometerPhoto;
  final ImagePicker _picker = ImagePicker();

  @override
  void initState() {
    super.initState();
    _setCurrentDateTime();
    _getCurrentLocation();
    _loadUserData();
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

  void _onProgramChanged(String? program) {
    setState(() {
      selectedProgram = program;
      selectedVehicle = null; // Reset vehicle selection
      
      // 🎨 DUMMY MODE - Auto-fill based on selected program
      if (program != null) {
        if (program.contains('Jelajah')) {
          locationController.text = 'Kuala Lumpur Convention Centre';
          estimationKmController.text = '150';
          notesController.text = 'Program jelajah ke seluruh negeri';
        } else if (program.contains('Inovasi')) {
          locationController.text = 'Dewan Orang Ramai Kg. Baru';
          estimationKmController.text = '80';
          notesController.text = 'Program inovasi pertanian desa';
        } else if (program.contains('Komuniti')) {
          locationController.text = 'Taman Rekreasi Lembah Hijau';
          estimationKmController.text = '45';
          notesController.text = 'Program gotong royong komuniti';
        }
      } else {
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
          odometerPhoto = File(photo.path);
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
          odometerPhoto = File(photo.path);
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
    if (selectedProgram == null) {
      setState(() {
        errorMessage = 'Sila pilih program';
      });
      return;
    }

    if (selectedVehicle == null) {
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

    setState(() {
      isSubmitting = true;
      errorMessage = null;
    });

    // 🎨 DUMMY MODE - Simulate API call
    await Future.delayed(const Duration(seconds: 2));

    if (mounted) {
      setState(() {
        isSubmitting = false;
      });

      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Check-in berjaya! (Dummy Mode)'),
          backgroundColor: Colors.green,
        ),
      );

      Navigator.pop(context, true);
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

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: PastelColors.primary,
        title: Text('Check In', style: AppTextStyles.h2.copyWith(color: Colors.white)),
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      backgroundColor: PastelColors.background,
      body: isLoading
          ? Center(child: CircularProgressIndicator(color: PastelColors.primary))
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
                      DropdownButtonFormField<String>(
                        value: selectedProgram,
                        items: availablePrograms
                            .map((p) => DropdownMenuItem(value: p, child: Text(p)))
                            .toList(),
                        onChanged: _onProgramChanged,
                        decoration: InputDecoration(
                          hintText: 'Select Program',
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

                      // Vehicle
                      _fieldLabel('Vehicle'),
                      const SizedBox(height: 4),
                      DropdownButtonFormField<String>(
                        value: selectedVehicle,
                        items: availableVehicles
                            .map((v) => DropdownMenuItem(value: v, child: Text(v)))
                            .toList(),
                        onChanged: (v) => setState(() => selectedVehicle = v),
                        decoration: InputDecoration(
                          hintText: 'Select Vehicle',
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
                      if (selectedVehicle != null) ...[
                        const SizedBox(height: 8),
                        Container(
                          padding: const EdgeInsets.all(8),
                          decoration: BoxDecoration(
                            color: PastelColors.info.withOpacity(0.1),
                            border: Border.all(color: PastelColors.info),
                            borderRadius: BorderRadius.circular(4),
                          ),
                          child: Text(
                            'Current Vehicle Odometer: 12,450 km (Dummy)',
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
                                child: Image.file(
                                  odometerPhoto!,
                                  fit: BoxFit.cover,
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