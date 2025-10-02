import 'package:flutter/material.dart';
import '../theme/pastel_colors.dart';
import '../theme/text_styles.dart';
import '../services/hive_service.dart';
import '../services/api_service.dart';
import '../services/connectivity_service.dart';
import '../models/driver_log_model.dart';
import '../models/user_model.dart';
import '../repositories/driver_log_repository.dart';
import 'dart:io';
import 'package:geolocator/geolocator.dart';
import 'package:image_picker/image_picker.dart';
import 'package:dio/dio.dart';

class CheckOutScreen extends StatefulWidget {
  @override
  State<CheckOutScreen> createState() => _CheckOutScreenState();
}

class _CheckOutScreenState extends State<CheckOutScreen> {
  final TextEditingController odometerController = TextEditingController();
  final TextEditingController programNameController = TextEditingController();
  final TextEditingController vehicleController = TextEditingController();
  final TextEditingController locationController = TextEditingController();
  final TextEditingController estimationKmController = TextEditingController();
  final TextEditingController requestByController = TextEditingController();
  final TextEditingController notesController = TextEditingController();
  final TextEditingController currentDateTimeController = TextEditingController();
  final TextEditingController gpsLocationController = TextEditingController();

  // Active trip data
  DriverLog? activeTrip;
  User? user;
  bool isLoading = true;
  String? errorMessage;
  bool isSubmitting = false;

  // Photo data
  File? odometerPhoto;
  final ImagePicker _picker = ImagePicker();
  
  // Repository
  late final DriverLogRepository _driverLogRepository;

  // GPS data
  String currentLocation = 'Getting location...';
  double gpsLatitude = 0.0;
  double gpsLongitude = 0.0;

  @override
  void initState() {
    super.initState();
    _driverLogRepository = DriverLogRepository(
      apiService: ApiService(),
      connectivityService: ConnectivityService(),
    );
    _loadActiveTrip();
    _getCurrentLocation();
    _setCurrentDateTime();
  }

  Future<void> _loadActiveTrip() async {
    try {
      setState(() {
        isLoading = true;
        errorMessage = null;
      });

      // Get user
      user = HiveService.getUser();
      if (user == null) {
        throw Exception('User not found');
      }

            // Get active trip from Hive first (offline-first approach)
      activeTrip = HiveService.getActiveDriverLog();
      print('DEBUG: Active trip from Hive: ${activeTrip?.id}');
      if (activeTrip != null) {
        print('DEBUG: Hive Active Trip Program Nama: ${activeTrip!.programNama}');
        print('DEBUG: Hive Active Trip Program Lokasi: ${activeTrip!.programLokasi}');
        print('DEBUG: Hive Active Trip Kenderaan No Plat: ${activeTrip!.kenderaanNoPlat}');
        print('DEBUG: Hive Active Trip Kenderaan Jenama: ${activeTrip!.kenderaanJenama}');
        print('DEBUG: Hive Active Trip Kenderaan Model: ${activeTrip!.kenderaanModel}');
        print('DEBUG: Hive Active Trip Program ID: ${activeTrip!.programId}');
        print('DEBUG: Hive Active Trip Kenderaan ID: ${activeTrip!.kenderaanId}');
        print('DEBUG: Hive Active Trip Status: ${activeTrip!.status}');
        print('DEBUG: Hive Active Trip isActive: ${activeTrip!.isActive}');
        
        // If we have data in Hive, use it (offline-first)
        if (activeTrip!.programNama != null && activeTrip!.programNama!.isNotEmpty) {
          print('DEBUG: Using Hive data for checkout (offline-first)');
        } else {
          print('DEBUG: Hive data incomplete, trying to enrich from API...');
          // Only try API if Hive data is incomplete
          final isConnected = await ConnectivityService().checkConnectivity();
          if (isConnected) {
            try {
              final apiService = ApiService();
              final response = await apiService.getActiveTrip();
              if (response['success'] && response['data'] != null) {
                final apiData = response['data'];
                print('DEBUG: Enriching Hive data with API data');
                
                // Update existing Hive log with API data
                if (apiData['program'] != null) {
                  activeTrip!.programNama = apiData['program']['nama_program'];
                  activeTrip!.programLokasi = apiData['program']['lokasi_program'];
                  print('DEBUG: Updated program data from API');
                }
                
                if (apiData['kenderaan'] != null) {
                  activeTrip!.kenderaanNoPlat = apiData['kenderaan']['no_plat'];
                  activeTrip!.kenderaanJenama = apiData['kenderaan']['jenama'];
                  activeTrip!.kenderaanModel = apiData['kenderaan']['model'];
                  print('DEBUG: Updated vehicle data from API');
                }
                
                // Save enriched data back to Hive
                await HiveService.updateDriverLog(activeTrip!);
                print('DEBUG: Enriched data saved to Hive');
              }
            } catch (e) {
              print('DEBUG: Failed to enrich from API: $e');
            }
          }
        }
      } else {
        print('DEBUG: WARNING - No active trip found in Hive!');
        // Show all available logs for debugging
        final allLogs = HiveService.getDriverLogs();
        print('DEBUG: Total logs in Hive: ${allLogs.length}');
        for (var log in allLogs) {
          print('DEBUG: Log ID: ${log.id}, Status: ${log.status}, isActive: ${log.isActive}');
          print('DEBUG: - Program: ${log.programNama} (ID: ${log.programId})');
          print('DEBUG: - Vehicle: ${log.kenderaanNoPlat} (ID: ${log.kenderaanId})');
        }
      }

      if (activeTrip == null) {
        throw Exception('Tiada trip aktif untuk checkout');
      }

      // Fill in the form with active trip data
      _fillFormWithActiveTripData();

      setState(() {
        isLoading = false;
      });
    } catch (e) {
      setState(() {
        errorMessage = 'Ralat: $e';
        isLoading = false;
      });
    }
  }

  void _fillFormWithActiveTripData() {
    if (activeTrip == null) {
      print('DEBUG: ERROR - Cannot fill form, activeTrip is null!');
      return;
    }

    print('DEBUG: ===== FILLING FORM WITH ACTIVE TRIP DATA =====');
    print('DEBUG: Active Trip ID: ${activeTrip!.id}');
    print('DEBUG: Program ID: ${activeTrip!.programId}');
    print('DEBUG: Kenderaan ID: ${activeTrip!.kenderaanId}');
    print('DEBUG: Nama Log: ${activeTrip!.namaLog}');
    print('DEBUG: Program Nama: ${activeTrip!.programNama}');
    print('DEBUG: Program Lokasi: ${activeTrip!.programLokasi}');
    print('DEBUG: Kenderaan No Plat: ${activeTrip!.kenderaanNoPlat}');
    print('DEBUG: Kenderaan Jenama: ${activeTrip!.kenderaanJenama}');
    print('DEBUG: Kenderaan Model: ${activeTrip!.kenderaanModel}');
    print('DEBUG: Status: ${activeTrip!.status}');
    print('DEBUG: isActive: ${activeTrip!.isActive}');
    print('DEBUG: Checkin Time: ${activeTrip!.checkinTime}');
    print('DEBUG: Checkout Time: ${activeTrip!.checkoutTime}');
    print('DEBUG: ==============================================');

    // Fill disabled fields with active trip data
    final programName = activeTrip!.programNama ?? activeTrip!.namaLog ?? 'N/A';
    programNameController.text = programName;
    print('DEBUG: Setting Program Name to: $programName');
    
    // Use vehicle name instead of ID
    String vehicleName;
    if (activeTrip!.kenderaanNoPlat != null && activeTrip!.kenderaanNoPlat!.isNotEmpty) {
      vehicleName = '${activeTrip!.kenderaanNoPlat} - ${activeTrip!.kenderaanJenama ?? ''} ${activeTrip!.kenderaanModel ?? ''}';
    } else {
      // Try to get vehicle details from Hive if not available in active trip
      final vehicle = HiveService.getVehicle(activeTrip!.kenderaanId);
      if (vehicle != null) {
        vehicleName = '${vehicle.noPlat} - ${vehicle.jenama} ${vehicle.model}';
        print('DEBUG: Got vehicle details from Hive: $vehicleName');
      } else {
        vehicleName = 'Vehicle ID: ${activeTrip!.kenderaanId}';
        print('DEBUG: Vehicle not found in Hive, using ID');
      }
    }
    vehicleController.text = vehicleName;
    
    // Use program location instead of program name
    String programLocation = activeTrip!.programLokasi ?? 'N/A';
    if (programLocation == 'N/A') {
      // Try to get program details from Hive if not available in active trip
      final program = HiveService.getProgram(activeTrip!.programId);
      if (program != null) {
        programLocation = program.lokasiProgram ?? 'N/A';
        print('DEBUG: Got program location from Hive: $programLocation');
      }
    }
    locationController.text = programLocation;
    
    estimationKmController.text = '100'; // Default estimation
    requestByController.text = user?.name ?? 'N/A';
    notesController.text = activeTrip!.catatan ?? 'N/A';
    
    // Set current odometer hint to check-in odometer
    odometerController.text = '';
    
    print('DEBUG: ===== FORM CONTROLLERS FILLED =====');
    print('DEBUG: - Program Name Controller: "${programNameController.text}"');
    print('DEBUG: - Vehicle Controller: "${vehicleController.text}"');
    print('DEBUG: - Program Location Controller: "${locationController.text}"');
    print('DEBUG: - Estimation KM Controller: "${estimationKmController.text}"');
    print('DEBUG: - Request By Controller: "${requestByController.text}"');
    print('DEBUG: - Notes Controller: "${notesController.text}"');
    print('DEBUG: ===================================');
  }

  void _setCurrentDateTime() {
    final now = DateTime.now();
    currentDateTimeController.text = '${now.day.toString().padLeft(2, '0')}/${now.month.toString().padLeft(2, '0')}/${now.year} ${now.hour.toString().padLeft(2, '0')}:${now.minute.toString().padLeft(2, '0')}';
  }

  Future<void> _getCurrentLocation() async {
    try {
      setState(() {
        currentLocation = 'Getting location...';
        errorMessage = null;
      });

      bool serviceEnabled = await Geolocator.isLocationServiceEnabled();
      if (!serviceEnabled) {
        setState(() {
          currentLocation = 'Location services disabled';
        });
        return;
      }

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

      Position position = await Geolocator.getCurrentPosition(
        desiredAccuracy: LocationAccuracy.high,
        timeLimit: Duration(seconds: 10),
      );

      setState(() {
        gpsLatitude = position.latitude;
        gpsLongitude = position.longitude;
        currentLocation = '${position.latitude.toStringAsFixed(6)}, ${position.longitude.toStringAsFixed(6)}';
        gpsLocationController.text = currentLocation;
      });
    } catch (e) {
      setState(() {
        currentLocation = 'Error getting location';
        errorMessage = 'GPS Error: $e';
      });
    }
  }

  // Take photo using camera or gallery
  Future<void> _takeOdometerPhoto() async {
    showModalBottomSheet(
      context: context,
      builder: (BuildContext context) {
        return Container(
          padding: EdgeInsets.all(16),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              ListTile(
                leading: Icon(Icons.camera_alt, color: PastelColors.primary),
                title: Text('Ambil Gambar'),
                subtitle: Text('Gunakan camera'),
                onTap: () {
                  Navigator.pop(context);
                  _pickImageFromCamera();
                },
              ),
              ListTile(
                leading: Icon(Icons.photo_library, color: PastelColors.primary),
                title: Text('Pilih dari Gallery'),
                subtitle: Text('Pilih gambar sedia ada'),
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

  // Pick image from camera
  Future<void> _pickImageFromCamera() async {
    try {
      setState(() { 
        errorMessage = null; 
      });
      
      print('DEBUG: Attempting to open camera for checkout...');
      final XFile? photo = await _picker.pickImage(
        source: ImageSource.camera, 
        imageQuality: 80
      );
      
      if (photo != null) {
        print('DEBUG: Photo captured successfully for checkout: ${photo.path}');
        setState(() { 
          odometerPhoto = File(photo.path); 
        });
        
        // Show success message
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Gambar odometer checkout berjaya diambil!'),
            backgroundColor: Colors.green,
            duration: Duration(seconds: 2),
          ),
        );
      } else {
        print('DEBUG: No photo selected for checkout');
        setState(() {
          errorMessage = 'Tiada gambar diambil. Sila cuba lagi.';
        });
      }
    } catch (e) {
      print('DEBUG: Camera error for checkout: $e');
      setState(() { 
        errorMessage = 'Camera tidak tersedia atau tidak berfungsi. Anda boleh teruskan tanpa gambar.'; 
      });
      
      // Show detailed error for debugging
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Camera Error: $e'),
          backgroundColor: Colors.orange,
          duration: Duration(seconds: 5),
        ),
      );
    }
  }

  // Pick image from gallery
  Future<void> _pickImageFromGallery() async {
    try {
      setState(() { 
        errorMessage = null; 
      });
      
      print('DEBUG: Attempting to open gallery for checkout...');
      final XFile? photo = await _picker.pickImage(
        source: ImageSource.gallery, 
        imageQuality: 80
      );
      
      if (photo != null) {
        print('DEBUG: Photo selected from gallery for checkout: ${photo.path}');
        setState(() { 
          odometerPhoto = File(photo.path); 
        });
        
        // Show success message
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Gambar odometer checkout berjaya dipilih dari gallery!'),
            backgroundColor: Colors.green,
            duration: Duration(seconds: 2),
          ),
        );
      } else {
        print('DEBUG: No photo selected from gallery for checkout');
        setState(() {
          errorMessage = 'Tiada gambar dipilih dari gallery.';
        });
      }
    } catch (e) {
      print('DEBUG: Gallery error for checkout: $e');
    setState(() {
        errorMessage = 'Gallery tidak tersedia atau tidak berfungsi.'; 
      });
      
      // Show detailed error for debugging
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Gallery Error: $e'),
          backgroundColor: Colors.orange,
          duration: Duration(seconds: 5),
        ),
      );
    }
  }

  String? validateOdometer(String? value) {
    if (value == null || value.isEmpty) {
      return 'Sila masukkan bacaan odometer';
    }

    final odometer = int.tryParse(value);
    if (odometer == null) {
      return 'Bacaan odometer mesti nombor';
    }

    if (odometer < 0) {
      return 'Bacaan odometer tidak boleh negatif';
    }

    // Check if odometer is less than check-in odometer
    if (activeTrip != null && activeTrip!.bacaanOdometer != null && odometer < activeTrip!.bacaanOdometer!) {
      return 'Bacaan odometer checkout tidak boleh kurang dari bacaan check-in (${activeTrip!.bacaanOdometer!} km)';
    }

    return null;
  }

  Future<void> _submitCheckOut() async {
    if (activeTrip == null) {
      setState(() {
        errorMessage = 'Tiada trip aktif untuk checkout';
      });
      return;
    }

    final odometerError = validateOdometer(odometerController.text);
    if (odometerError != null) {
      setState(() {
        errorMessage = odometerError;
      });
      return;
    }

    setState(() {
      isSubmitting = true;
      errorMessage = null;
    });

    try {
      print('DEBUG: Starting check-out process...');
      print('DEBUG: Active Trip ID: ${activeTrip!.id}');
      print('DEBUG: Check-in Odometer: ${activeTrip!.bacaanOdometer}');
      print('DEBUG: Check-out Odometer: ${odometerController.text}');
      print('DEBUG: Location: $currentLocation');
      print('DEBUG: GPS: $gpsLatitude, $gpsLongitude');

      final checkoutOdometer = int.parse(odometerController.text);
      final distance = checkoutOdometer - (activeTrip!.bacaanOdometer ?? 0);

      // Store the exact checkout time
      final exactCheckoutTime = DateTime.now();
      
      // Update driver log with checkout data
      activeTrip!.checkoutTime = exactCheckoutTime;
      activeTrip!.lokasiCheckout = currentLocation;
      activeTrip!.gpsLatitude = gpsLatitude;
      activeTrip!.gpsLongitude = gpsLongitude;
      activeTrip!.bacaanOdometerCheckout = checkoutOdometer;
      activeTrip!.odometerPhotoCheckout = odometerPhoto?.path;
      activeTrip!.jarakPerjalanan = distance.toInt();
      activeTrip!.status = 'selesai';
      activeTrip!.isSynced = false;

      print('DEBUG: Driver log updated: ${activeTrip!.toJson()}');
      await HiveService.updateDriverLog(activeTrip!);
      print('DEBUG: Driver log saved to Hive');

      // Use repository to handle check-out with offline-first approach
      print('DEBUG: Using repository for check-out...');
      final success = await _driverLogRepository.endTrip(
        log: activeTrip!,
        jarakPerjalanan: distance.toInt(),
        bacaanOdometer: checkoutOdometer,
        lokasiCheckout: currentLocation,
        catatan: notesController.text,
        gpsLatitude: gpsLatitude,
        gpsLongitude: gpsLongitude,
        odometerPhoto: odometerPhoto,
      );
      
      if (!success) {
        throw Exception('Failed to complete check-out');
      }
      
      print('DEBUG: Check-out completed successfully via repository');

      // Show success message
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Check-out berjaya! Jarak perjalanan: ${distance} km'),
          backgroundColor: Colors.green,
        ),
      );

      // Navigate back
      Navigator.pop(context, true);
    } catch (e) {
      print('DEBUG: Check-out process failed: $e');
      setState(() {
        errorMessage = 'Ralat: $e';
        isSubmitting = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    if (isLoading) {
      return Scaffold(
        appBar: AppBar(
          backgroundColor: PastelColors.primary,
          title: Text('Check Out', style: AppTextStyles.h2.copyWith(color: Colors.white)),
          iconTheme: const IconThemeData(color: Colors.white),
        ),
        backgroundColor: PastelColors.background,
        body: Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              CircularProgressIndicator(color: PastelColors.primary),
              SizedBox(height: 16),
              Text('Loading trip data...', style: AppTextStyles.bodyLarge),
            ],
          ),
        ),
      );
    }

    if (errorMessage != null && activeTrip == null) {
      return Scaffold(
        appBar: AppBar(
          backgroundColor: PastelColors.primary,
          title: Text('Check Out', style: AppTextStyles.h2.copyWith(color: Colors.white)),
          iconTheme: const IconThemeData(color: Colors.white),
        ),
        backgroundColor: PastelColors.background,
        body: Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(Icons.error_outline, size: 64, color: PastelColors.errorText),
              SizedBox(height: 16),
              Text(errorMessage!, style: AppTextStyles.bodyLarge, textAlign: TextAlign.center),
              SizedBox(height: 24),
              ElevatedButton(
                onPressed: () => Navigator.pop(context),
                child: Text('Kembali'),
              ),
            ],
          ),
        ),
      );
    }

    return Scaffold(
      appBar: AppBar(
        backgroundColor: PastelColors.primary,
        title: Text('Check Out', style: AppTextStyles.h2.copyWith(color: Colors.white)),
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      backgroundColor: PastelColors.background,
      body: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 24),
        child: Column(
          children: [
            if (errorMessage != null)
              Container(
                width: double.infinity,
                padding: EdgeInsets.all(12),
                margin: EdgeInsets.only(bottom: 16),
                decoration: BoxDecoration(
                  color: Colors.red.shade50,
                  border: Border.all(color: PastelColors.errorText),
                  borderRadius: BorderRadius.circular(4),
                ),
                child: Text(
                  errorMessage!,
                  style: AppTextStyles.bodyMedium.copyWith(color: PastelColors.errorText),
                ),
              ),
            Card(
          color: PastelColors.cardBackground,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8), side: BorderSide(color: PastelColors.border)),
          elevation: 0,
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 24),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                _fieldLabel('Program Name'),
                const SizedBox(height: 4),
                TextField(
                      controller: programNameController,
                  enabled: false,
                  decoration: InputDecoration(
                    border: OutlineInputBorder(borderRadius: BorderRadius.circular(3)),
                    isDense: true,
                    contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                    prefixIcon: Icon(Icons.event, color: PastelColors.primary, size: 20),
                  ),
                  style: AppTextStyles.bodyLarge,
                ),
                const SizedBox(height: 16),
                _fieldLabel('Vehicle'),
                const SizedBox(height: 4),
                TextField(
                      controller: vehicleController,
                  enabled: false,
                  decoration: InputDecoration(
                    border: OutlineInputBorder(borderRadius: BorderRadius.circular(3)),
                    isDense: true,
                    contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                    prefixIcon: Icon(Icons.directions_car, color: PastelColors.primary, size: 20),
                  ),
                  style: AppTextStyles.bodyLarge,
                ),
                const SizedBox(height: 16),
                _fieldLabel('Current Odometer'),
                const SizedBox(height: 4),
                TextField(
                  controller: odometerController,
                  keyboardType: TextInputType.number,
                  decoration: InputDecoration(
                        hintText: 'Enter current odometer (min: ${activeTrip?.bacaanOdometer ?? 0})',
                    border: OutlineInputBorder(borderRadius: BorderRadius.circular(3)),
                    isDense: true,
                    contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                    prefixIcon: Icon(Icons.speed, color: PastelColors.primary, size: 20),
                  ),
                  style: AppTextStyles.bodyLarge,
                ),
                const SizedBox(height: 16),
                _fieldLabel('Odometer Photo'),
                const SizedBox(height: 4),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        ElevatedButton.icon(
                              onPressed: _takeOdometerPhoto,
                          icon: Icon(Icons.add_a_photo, size: 18),
                          label: Text('Take Picture', style: AppTextStyles.bodyLarge),
                          style: ElevatedButton.styleFrom(
                            backgroundColor: PastelColors.primary,
                            foregroundColor: Colors.white,
                            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                            textStyle: AppTextStyles.bodyLarge,
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(3)),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 8),
                        if (odometerPhoto != null)
                          Stack(
                        alignment: Alignment.topRight,
                        children: [
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
                          Positioned(
                            top: -8,
                            right: -8,
                            child: IconButton(
                              icon: Icon(Icons.cancel, color: PastelColors.errorText, size: 20),
                                  onPressed: () => setState(() => odometerPhoto = null),
                              padding: EdgeInsets.zero,
                              constraints: BoxConstraints(),
                            ),
                          ),
                        ],
                    ),
                  ],
                ),
                const SizedBox(height: 16),
                    _fieldLabel('Program Location'),
                const SizedBox(height: 4),
                TextField(
                      controller: locationController,
                  enabled: false,
                  decoration: InputDecoration(
                    border: OutlineInputBorder(borderRadius: BorderRadius.circular(3)),
                    isDense: true,
                    contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                    prefixIcon: Icon(Icons.location_on, color: PastelColors.primary, size: 20),
                  ),
                  style: AppTextStyles.bodyLarge,
                ),
                const SizedBox(height: 16),
                _fieldLabel('Estimation KM'),
                const SizedBox(height: 4),
                TextField(
                      controller: estimationKmController,
                  enabled: false,
                  decoration: InputDecoration(
                    border: OutlineInputBorder(borderRadius: BorderRadius.circular(3)),
                    isDense: true,
                    contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                    prefixIcon: Icon(Icons.straighten, color: PastelColors.primary, size: 20),
                  ),
                  style: AppTextStyles.bodyLarge,
                ),
                const SizedBox(height: 16),
                _fieldLabel('Request By'),
                const SizedBox(height: 4),
                TextField(
                      controller: requestByController,
                  enabled: false,
                  decoration: InputDecoration(
                    border: OutlineInputBorder(borderRadius: BorderRadius.circular(3)),
                    isDense: true,
                    contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                    prefixIcon: Icon(Icons.person, color: PastelColors.primary, size: 20),
                  ),
                  style: AppTextStyles.bodyLarge,
                ),
                const SizedBox(height: 16),
                _fieldLabel('Notes'),
                const SizedBox(height: 4),
                TextField(
                      controller: notesController,
                  enabled: false,
                  maxLines: 2,
                  decoration: InputDecoration(
                    border: OutlineInputBorder(borderRadius: BorderRadius.circular(3)),
                    isDense: true,
                    contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                    prefixIcon: Icon(Icons.note_alt, color: PastelColors.primary, size: 20),
                  ),
                  style: AppTextStyles.bodyLarge,
                ),
                const SizedBox(height: 16),
                _fieldLabel('GPS Location'),
                const SizedBox(height: 4),
                TextField(
                      controller: gpsLocationController,
                  enabled: false,
                  decoration: InputDecoration(
                    border: OutlineInputBorder(borderRadius: BorderRadius.circular(3)),
                    isDense: true,
                    contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                    prefixIcon: Icon(Icons.gps_fixed, color: PastelColors.primary, size: 20),
                  ),
                  style: AppTextStyles.bodyLarge,
                ),
                const SizedBox(height: 16),
                _fieldLabel('Current Date & Time'),
                const SizedBox(height: 4),
                TextField(
                      controller: currentDateTimeController,
                  enabled: false,
                  decoration: InputDecoration(
                    border: OutlineInputBorder(borderRadius: BorderRadius.circular(3)),
                    isDense: true,
                    contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                    prefixIcon: Icon(Icons.access_time, color: PastelColors.primary, size: 20),
                  ),
                  style: AppTextStyles.bodyLarge,
                ),
                const SizedBox(height: 28),
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton(
                        onPressed: isSubmitting ? null : _submitCheckOut,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: PastelColors.primary,
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(vertical: 16),
                      textStyle: AppTextStyles.bodyLarge,
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(3)),
                    ),
                        child: isSubmitting
                            ? Row(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  SizedBox(
                                    width: 20,
                                    height: 20,
                                    child: CircularProgressIndicator(
                                      strokeWidth: 2,
                                      valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                                    ),
                                  ),
                                  SizedBox(width: 12),
                                  Text('Processing...'),
                                ],
                              )
                            : Text('Check Out', style: AppTextStyles.bodyLarge.copyWith(color: Colors.white)),
                  ),
                ),
              ],
            ),
          ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _fieldLabel(String label) {
    return Text(label, style: AppTextStyles.bodyMedium.copyWith(color: PastelColors.textPrimary));
  }
} 