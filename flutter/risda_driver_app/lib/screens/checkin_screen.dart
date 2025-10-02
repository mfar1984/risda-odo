import 'package:flutter/material.dart';
import '../theme/pastel_colors.dart';
import '../theme/text_styles.dart';
import '../services/hive_service.dart';
import '../services/api_service.dart';
import '../services/connectivity_service.dart';
import '../models/program_model.dart';
import '../models/vehicle_model.dart';
import '../repositories/driver_log_repository.dart';

import 'dart:io';
import 'package:geolocator/geolocator.dart';
import 'package:image_picker/image_picker.dart';

class CheckInScreen extends StatefulWidget {
  @override
  State<CheckInScreen> createState() => _CheckInScreenState();
}

class _CheckInScreenState extends State<CheckInScreen> {
  Program? selectedProgram;
  Vehicle? selectedVehicle;
  final TextEditingController odometerController = TextEditingController();
  final TextEditingController locationController = TextEditingController();
  final TextEditingController estimationKmController = TextEditingController();
  final TextEditingController requestByController = TextEditingController();
  final TextEditingController notesController = TextEditingController();
  final TextEditingController currentDateTimeController = TextEditingController();
  
  List<Program> availablePrograms = [];
  List<Vehicle> availableVehicles = [];
  
  bool isLoading = true;
  bool isSubmitting = false;
  String? errorMessage;
  
  // Location data
  String currentLocation = 'Getting location...';
  double? gpsLatitude;
  double? gpsLongitude;
  
  // Photo data
  File? odometerPhoto;
  final ImagePicker _picker = ImagePicker();
  
  // Repository
  late final DriverLogRepository _driverLogRepository;

  @override
  void initState() {
    super.initState();
    _driverLogRepository = DriverLogRepository(
      apiService: ApiService(),
      connectivityService: ConnectivityService(),
    );
    _loadData();
    _setCurrentDateTime(); // New method
    
    // Get current location
    _getCurrentLocation();
    
    // Test API connectivity
    _testApiConnectivity();
  }

  Future<void> _testApiConnectivity() async {
    try {
      print('DEBUG: Testing API connectivity...');
      final isConnected = await ConnectivityService().checkConnectivity();
      print('DEBUG: Connectivity result: $isConnected');
      
      if (isConnected) {
        final apiService = ApiService();
        final auth = HiveService.getAuth();
        print('DEBUG: Auth token: ${auth?.authorizationHeader}');
        
        // Test simple API call
        try {
          final response = await apiService.getActiveTrip();
          print('DEBUG: API test successful: $response');
        } catch (e) {
          print('DEBUG: API test failed: $e');
        }
      }
    } catch (e) {
      print('DEBUG: Connectivity test failed: $e');
    }
  }

  void _setCurrentDateTime() {
    final now = DateTime.now();
    currentDateTimeController.text = '${now.day.toString().padLeft(2, '0')}/${now.month.toString().padLeft(2, '0')}/${now.year} ${now.hour.toString().padLeft(2, '0')}:${now.minute.toString().padLeft(2, '0')}';
  }

  Future<void> _loadData() async {
    try {
      setState(() {
        isLoading = true;
        errorMessage = null;
      });

      // Get current user
      final user = HiveService.getUser();
      if (user == null) {
        setState(() {
          errorMessage = 'User data not found. Please login again.';
          isLoading = false;
        });
        return;
      }

      // Load programs and vehicles from API
      final apiService = ApiService();
      final isConnected = await ConnectivityService().checkConnectivity();
      
      if (isConnected) {
        try {
          // Get active programs from API
          final programsResponse = await apiService.getActivePrograms();
          print('DEBUG: Programs response: $programsResponse');
          final programs = programsResponse.map((p) => Program.fromJson(p)).toList();
          print('DEBUG: Parsed programs: ${programs.length} programs');
          for (var program in programs) {
            print('DEBUG: Program: ${program.namaProgram} (ID: ${program.id})');
          }
          
          // Get vehicles from API
          final vehiclesResponse = await apiService.getVehicles();
          print('DEBUG: Vehicles response: $vehiclesResponse');
          final vehicles = vehiclesResponse.map((v) => Vehicle.fromJson(v)).toList();
          print('DEBUG: Parsed vehicles: ${vehicles.length} vehicles');
          
          // Save to Hive for offline use
          await HiveService.savePrograms(programs);
          await HiveService.saveVehicles(vehicles);
          
          setState(() {
            availablePrograms = programs;
            availableVehicles = vehicles;
            isLoading = false;
          });
          print('DEBUG: State updated with ${programs.length} programs and ${vehicles.length} vehicles');
        } catch (e) {
          print('DEBUG: API failed, loading from Hive: $e');
          // If API fails, try to load from Hive
          final programs = HiveService.getPrograms();
          final vehicles = HiveService.getVehicles();
          print('DEBUG: Loaded from Hive: ${programs.length} programs, ${vehicles.length} vehicles');
          
          setState(() {
            availablePrograms = programs;
            availableVehicles = vehicles;
            isLoading = false;
          });
        }
      } else {
        print('DEBUG: Offline mode - loading from Hive');
        // Offline mode - load from Hive
        final programs = HiveService.getPrograms();
        final vehicles = HiveService.getVehicles();
        print('DEBUG: Offline loaded: ${programs.length} programs, ${vehicles.length} vehicles');
        
        setState(() {
          availablePrograms = programs;
          availableVehicles = vehicles;
          isLoading = false;
        });
      }
    } catch (e) {
      setState(() {
        errorMessage = 'Error loading data: $e';
        isLoading = false;
      });
    }
  }

  Future<void> _getCurrentLocation() async {
    print('DEBUG: _getCurrentLocation called');
    try {
      setState(() {
        currentLocation = 'Getting location...';
      });

      // Check if location services are enabled
      bool serviceEnabled = await Geolocator.isLocationServiceEnabled();
      print('DEBUG: Location service enabled: $serviceEnabled');
      if (!serviceEnabled) {
        print('DEBUG: Location services disabled');
        setState(() {
          currentLocation = 'Location services disabled';
        });
        return;
      }

      // Check location permission
      LocationPermission permission = await Geolocator.checkPermission();
      print('DEBUG: Initial permission: $permission');
      if (permission == LocationPermission.denied) {
        print('DEBUG: Requesting permission...');
        permission = await Geolocator.requestPermission();
        print('DEBUG: Permission after request: $permission');
        if (permission == LocationPermission.denied) {
          print('DEBUG: Permission denied');
          setState(() {
            currentLocation = 'Location permission denied';
          });
          return;
        }
      }

      if (permission == LocationPermission.deniedForever) {
        print('DEBUG: Permission denied forever');
        setState(() {
          currentLocation = 'Location permissions permanently denied';
        });
        return;
      }

      print('DEBUG: Getting current position...');
      // Get current position
      Position position = await Geolocator.getCurrentPosition(
        desiredAccuracy: LocationAccuracy.high,
        timeLimit: Duration(seconds: 10),
      );

      print('DEBUG: Position obtained: ${position.latitude}, ${position.longitude}');
      setState(() {
        gpsLatitude = position.latitude;
        gpsLongitude = position.longitude;
        currentLocation = '${position.latitude.toStringAsFixed(4)}, ${position.longitude.toStringAsFixed(4)}';
      });
      print('DEBUG: Location set: $currentLocation');
    } catch (e) {
      print('DEBUG: Error getting location: $e');
      setState(() {
        currentLocation = 'Error getting location: $e';
      });
    }
  }

  // Filter vehicles based on selected program
  List<Vehicle> getFilteredVehicles() {
    if (selectedProgram == null) return availableVehicles;
    
    // Filter vehicles that are assigned to the selected program
    return availableVehicles.where((v) => v.status == 'aktif').toList();
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
      
      print('DEBUG: Attempting to open camera...');
      final XFile? photo = await _picker.pickImage(
        source: ImageSource.camera,
        imageQuality: 80,
      );
      
      if (photo != null) {
        print('DEBUG: Photo captured successfully: ${photo.path}');
        setState(() {
          odometerPhoto = File(photo.path);
        });
        
        // Show success message
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Gambar odometer berjaya diambil!'),
            backgroundColor: Colors.green,
            duration: Duration(seconds: 2),
          ),
        );
      } else {
        print('DEBUG: No photo selected');
        setState(() {
          errorMessage = 'Tiada gambar diambil. Sila cuba lagi.';
        });
      }
    } catch (e) {
      print('DEBUG: Camera error: $e');
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
      
      print('DEBUG: Attempting to open gallery...');
      final XFile? photo = await _picker.pickImage(
        source: ImageSource.gallery,
        imageQuality: 80,
      );
      
      if (photo != null) {
        print('DEBUG: Photo selected from gallery: ${photo.path}');
        setState(() {
          odometerPhoto = File(photo.path);
        });
        
        // Show success message
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Gambar odometer berjaya dipilih dari gallery!'),
            backgroundColor: Colors.green,
            duration: Duration(seconds: 2),
          ),
        );
      } else {
        print('DEBUG: No photo selected from gallery');
        setState(() {
          errorMessage = 'Tiada gambar dipilih dari gallery.';
        });
      }
    } catch (e) {
      print('DEBUG: Gallery error: $e');
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

  // Update form fields when program is selected
  void _onProgramChanged(Program? program) {
    print('DEBUG: _onProgramChanged called with: ${program?.namaProgram ?? 'null'}');
    setState(() {
      selectedProgram = program;
      selectedVehicle = null; // Reset vehicle selection
      
      // Update form fields based on selected program
      if (program != null) {
        print('DEBUG: Updating form fields for program: ${program.namaProgram}');
        print('DEBUG: Program location: ${program.lokasiProgram}');
        print('DEBUG: Program anggaran KM: ${program.anggaranKm}');
        locationController.text = program.lokasiProgram ?? 'N/A';
        estimationKmController.text = program.anggaranKm.toString();
        requestByController.text = 'Mohd Fairiz Bin Abdul Rahman'; // From user data
        notesController.text = program.penerangan ?? 'N/A';
      } else {
        print('DEBUG: Clearing form fields - no program selected');
        locationController.text = '';
        estimationKmController.text = '';
        requestByController.text = '';
        notesController.text = '';
      }
    });
  }

  // Validate odometer reading
  String? validateOdometer(String? value) {
    if (value == null || value.isEmpty) {
      return 'Odometer reading is required';
    }
    
    final odometer = int.tryParse(value);
    if (odometer == null) {
      return 'Please enter a valid number';
    }
    
    if (odometer < 0) {
      return 'Odometer cannot be negative';
  }

    // Check if odometer is less than current vehicle odometer
    if (selectedVehicle != null) {
      if (odometer < selectedVehicle!.odometerSemasa) {
        return 'Odometer reading cannot be less than current vehicle odometer (${selectedVehicle!.odometerSemasa} km)';
      }
    }
    
    return null;
  }

  Future<void> _submitCheckIn() async {
    if (selectedProgram == null || selectedVehicle == null) {
      setState(() {
        errorMessage = 'Sila pilih program dan kenderaan';
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
      isLoading = true;
      errorMessage = null;
    });

    try {
      print('DEBUG: Starting check-in process using repository...');
      print('DEBUG: Selected Program: ${selectedProgram!.namaProgram} (ID: ${selectedProgram!.id})');
      print('DEBUG: Selected Vehicle: ${selectedVehicle!.noPlat} (ID: ${selectedVehicle!.id})');
      print('DEBUG: Odometer: ${odometerController.text}');
      print('DEBUG: Current Location: $currentLocation');
      print('DEBUG: GPS Latitude: $gpsLatitude');
      print('DEBUG: GPS Longitude: $gpsLongitude');
      print('DEBUG: Notes: ${notesController.text}');
      print('DEBUG: Location Controller: ${locationController.text}');
      print('DEBUG: Odometer Photo: ${odometerPhoto?.path ?? 'No photo'}');
      
      // Check if there's already an active trip
      final activeTrip = HiveService.getActiveDriverLog();
      print('DEBUG: Active trip check - Found: ${activeTrip != null}');
      if (activeTrip != null) {
        print('DEBUG: WARNING - Active trip found: ${activeTrip.id}');
        print('DEBUG: Active trip status: ${activeTrip.status}');
        print('DEBUG: Active trip checkin: ${activeTrip.checkinTime}');
        print('DEBUG: Active trip checkout: ${activeTrip.checkoutTime}');
        setState(() {
          errorMessage = 'Anda masih ada trip aktif. Sila checkout trip sedia ada terlebih dahulu.';
          isLoading = false;
        });
        return;
      }
      print('DEBUG: No active trip found - proceeding with check-in');
      
      // Check if location is available
      if (currentLocation.isEmpty || currentLocation == 'Getting location...' || currentLocation.contains('Error') || currentLocation.contains('disabled') || currentLocation.contains('denied')) {
        print('DEBUG: WARNING - currentLocation is not valid: $currentLocation');
        // Use fallback location from program
        final fallbackLocation = locationController.text.isNotEmpty ? locationController.text : 'Unknown Location';
        print('DEBUG: Using fallback location: $fallbackLocation');
        currentLocation = fallbackLocation;
      }
      
      // Check if GPS coordinates are available
      if (gpsLatitude == null || gpsLongitude == null) {
        print('DEBUG: WARNING - GPS coordinates are null! Using default coordinates');
        // Use default coordinates (Kuala Lumpur)
        gpsLatitude = 3.1390;
        gpsLongitude = 101.6869;
      }

      // Use repository to handle check-in
      final driverLog = await _driverLogRepository.startTrip(
        programId: selectedProgram!.id,
        kenderaanId: selectedVehicle!.id,
        lokasiMula: currentLocation,
        notaMula: notesController.text,
        gpsLatitude: gpsLatitude,
        gpsLongitude: gpsLongitude,
        bacaanOdometerMula: int.tryParse(odometerController.text) ?? 0,
        odometerPhoto: odometerPhoto,
      );

      if (driverLog == null) {
        throw Exception('Failed to create driver log');
      }

      print('DEBUG: Driver log created successfully: ${driverLog.id}');
      print('DEBUG: Driver log synced: ${driverLog.isSynced}');

      // Show success message
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Check-in berjaya!'),
          backgroundColor: Colors.green,
        ),
      );

      // Navigate back
      Navigator.pop(context, true);
    } catch (e) {
      print('DEBUG: Check-in process failed: $e');
      setState(() {
        errorMessage = 'Ralat: $e';
        isLoading = false;
      });
    }
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
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8), side: BorderSide(color: PastelColors.border)),
          elevation: 0,
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 24),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                    if (errorMessage != null)
                      Container(
                        width: double.infinity,
                        padding: EdgeInsets.all(12),
                        margin: EdgeInsets.only(bottom: 16),
                        decoration: BoxDecoration(
                          color: PastelColors.error.withOpacity(0.1),
                          border: Border.all(color: PastelColors.error),
                          borderRadius: BorderRadius.circular(4),
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              errorMessage!,
                              style: AppTextStyles.bodyMedium.copyWith(color: PastelColors.errorText),
                            ),
                            if (errorMessage!.contains('trip aktif'))
                              Padding(
                                padding: EdgeInsets.only(top: 8),
                                child: Column(
                                  children: [
                                    ElevatedButton.icon(
                                      onPressed: () async {
                                        // Show confirmation dialog
                                        final confirmed = await showDialog<bool>(
                                          context: context,
                                          builder: (context) => AlertDialog(
                                            title: Text('Resolve Trip Conflict'),
                                            content: Text('This will force complete any active trips and allow new check-ins. Continue?'),
                                            actions: [
                                              TextButton(
                                                onPressed: () => Navigator.pop(context, false),
                                                child: Text('Cancel'),
                                              ),
                                              TextButton(
                                                onPressed: () => Navigator.pop(context, true),
                                                child: Text('Resolve'),
                                              ),
                                            ],
                                          ),
                                        );
                                        
                                        if (confirmed == true) {
                                          try {
                                            await HiveService.forceCompleteAllActiveTrips();
                                            ScaffoldMessenger.of(context).showSnackBar(
                                              SnackBar(
                                                content: Text('Trip conflict resolved! You can now check-in.'),
                                                backgroundColor: Colors.green,
                                                duration: Duration(seconds: 3),
                                              ),
                                            );
                                            // Clear error message and refresh active trip check
                                            setState(() {
                                              errorMessage = '';
                                            });
                                            
                                            // Force refresh the screen to re-check active trip
                                            await Future.delayed(Duration(milliseconds: 500));
                                            if (mounted) {
                                              setState(() {});
                                            }
                                          } catch (e) {
                                            ScaffoldMessenger.of(context).showSnackBar(
                                              SnackBar(
                                                content: Text('Error resolving conflict: $e'),
                                                backgroundColor: Colors.red,
                                                duration: Duration(seconds: 3),
                                              ),
                                            );
                                          }
                                        }
                                      },
                                      icon: Icon(Icons.warning, size: 16),
                                      label: Text('Resolve Conflict'),
                                      style: ElevatedButton.styleFrom(
                                        backgroundColor: Colors.orange,
                                        foregroundColor: Colors.white,
                                        padding: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                                      ),
                                    ),
                                    SizedBox(height: 8),
                                    ElevatedButton.icon(
                                      onPressed: () async {
                                        // Show confirmation dialog for nuclear option
                                        final confirmed = await showDialog<bool>(
                                          context: context,
                                          builder: (context) => AlertDialog(
                                            title: Text('Clear All Active Trips'),
                                            content: Text('⚠️ WARNING: This will DELETE all active trips permanently. This is a nuclear option. Continue?'),
                                            actions: [
                                              TextButton(
                                                onPressed: () => Navigator.pop(context, false),
                                                child: Text('Cancel'),
                                              ),
                                              TextButton(
                                                onPressed: () => Navigator.pop(context, true),
                                                child: Text('Clear All'),
                                                style: TextButton.styleFrom(foregroundColor: Colors.red),
                                              ),
                                            ],
                                          ),
                                        );
                                        
                                        if (confirmed == true) {
                                          try {
                                            await HiveService.nuclearClearAllActiveTrips();
                                            ScaffoldMessenger.of(context).showSnackBar(
                                              SnackBar(
                                                content: Text('Nuclear cleanup completed! You can now check-in.'),
                                                backgroundColor: Colors.green,
                                                duration: Duration(seconds: 3),
                                              ),
                                            );
                                            // Clear error message and refresh active trip check
                                            setState(() {
                                              errorMessage = '';
                                            });
                                            
                                            // Force refresh the screen to re-check active trip
                                            await Future.delayed(Duration(milliseconds: 500));
                                            if (mounted) {
                                              setState(() {});
                                            }
                                          } catch (e) {
                                            ScaffoldMessenger.of(context).showSnackBar(
                                              SnackBar(
                                                content: Text('Error in nuclear cleanup: $e'),
                                                backgroundColor: Colors.red,
                                                duration: Duration(seconds: 3),
                                              ),
                                            );
                                          }
                                        }
                                      },
                                      icon: Icon(Icons.delete_forever, size: 16),
                                      label: Text('Nuclear Clear Active Trips'),
                                      style: ElevatedButton.styleFrom(
                                        backgroundColor: Colors.red,
                                        foregroundColor: Colors.white,
                                        padding: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                                      ),
                                    ),
                                    SizedBox(height: 8),
                                    ElevatedButton.icon(
                                      onPressed: () async {
                                        // Show confirmation dialog for ultimate nuclear option
                                        final confirmed = await showDialog<bool>(
                                          context: context,
                                          builder: (context) => AlertDialog(
                                            title: Text('Ultimate Nuclear Clear'),
                                            content: Text('☢️ ULTIMATE WARNING: This will CLEAR ENTIRE DATABASE and keep only completed trips. This is the last resort. Continue?'),
                                            actions: [
                                              TextButton(
                                                onPressed: () => Navigator.pop(context, false),
                                                child: Text('Cancel'),
                                              ),
                                              TextButton(
                                                onPressed: () => Navigator.pop(context, true),
                                                child: Text('Ultimate Clear'),
                                                style: TextButton.styleFrom(foregroundColor: Colors.red),
                                              ),
                                            ],
                                          ),
                                        );
                                        
                                        if (confirmed == true) {
                                          try {
                                            await HiveService.ultimateNuclearClear();
                                            ScaffoldMessenger.of(context).showSnackBar(
                                              SnackBar(
                                                content: Text('Ultimate nuclear clear completed! Database reset.'),
                                                backgroundColor: Colors.green,
                                                duration: Duration(seconds: 3),
                                              ),
                                            );
                                            // Clear error message and refresh active trip check
                                            setState(() {
                                              errorMessage = '';
                                            });
                                            
                                            // Force refresh the screen to re-check active trip
                                            await Future.delayed(Duration(milliseconds: 500));
                                            if (mounted) {
                                              setState(() {});
                                            }
                                          } catch (e) {
                                            ScaffoldMessenger.of(context).showSnackBar(
                                              SnackBar(
                                                content: Text('Error in ultimate nuclear clear: $e'),
                                                backgroundColor: Colors.red,
                                                duration: Duration(seconds: 3),
                                              ),
                                            );
                                          }
                                        }
                                      },
                                      icon: Icon(Icons.warning_amber, size: 16),
                                      label: Text('Ultimate Nuclear Clear'),
                                      style: ElevatedButton.styleFrom(
                                        backgroundColor: Colors.purple,
                                        foregroundColor: Colors.white,
                                        padding: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                                      ),
                                    ),
                                    SizedBox(height: 8),
                                    ElevatedButton.icon(
                                      onPressed: () async {
                                        // Show confirmation dialog for audit and fix
                                        final confirmed = await showDialog<bool>(
                                          context: context,
                                          builder: (context) => AlertDialog(
                                            title: Text('Audit & Fix All Logs'),
                                            content: Text('This will scan all logs and fix any inconsistencies. This operation is safe and will not delete any completed logs. Continue?'),
                                            actions: [
                                              TextButton(
                                                onPressed: () => Navigator.pop(context, false),
                                                child: Text('Cancel'),
                                              ),
                                              TextButton(
                                                onPressed: () => Navigator.pop(context, true),
                                                child: Text('Continue'),
                                              ),
                                            ],
                                          ),
                                        );
                                        
                                        if (confirmed == true) {
                                          try {
                                            setState(() {
                                              isLoading = true;
                                            });
                                            
                                            final auditResult = await HiveService.auditAndFixAllLogs();
                                            
                                            setState(() {
                                              isLoading = false;
                                            });
                                            
                                            if (auditResult['success'] == true) {
                                              // Show detailed results dialog
                                              showDialog(
                                                context: context,
                                                builder: (context) => AlertDialog(
                                                  title: Text('Audit Results'),
                                                  content: Column(
                                                    mainAxisSize: MainAxisSize.min,
                                                    crossAxisAlignment: CrossAxisAlignment.start,
                                                    children: [
                                                      Text('Fixed ${auditResult['fixedActiveCount']} active logs'),
                                                      Text('Removed ${auditResult['removedDuplicateCount']} duplicate logs'),
                                                      Text('Fixed ${auditResult['fixedInconsistentCount']} inconsistent logs'),
                                                      Text('Remaining active logs: ${auditResult['remainingActiveCount']}'),
                                                    ],
                                                  ),
                                                  actions: [
                                                    TextButton(
                                                      onPressed: () => Navigator.pop(context),
                                                      child: Text('OK'),
                                                    ),
                                                  ],
                                                ),
                                              );
                                              
                                              // Clear error message and refresh active trip check
                                              setState(() {
                                                errorMessage = '';
                                              });
                                              
                                              // Force refresh the screen to re-check active trip
                                              await Future.delayed(Duration(milliseconds: 500));
                                              if (mounted) {
                                                setState(() {});
                                              }
                                            } else {
                                              ScaffoldMessenger.of(context).showSnackBar(
                                                SnackBar(
                                                  content: Text('Audit failed: ${auditResult['error']}'),
                                                  backgroundColor: Colors.red,
                                                  duration: Duration(seconds: 3),
                                                ),
                                              );
                                            }
                                          } catch (e) {
                                            setState(() {
                                              isLoading = false;
                                            });
                                            ScaffoldMessenger.of(context).showSnackBar(
                                              SnackBar(
                                                content: Text('Error during audit: $e'),
                                                backgroundColor: Colors.red,
                                                duration: Duration(seconds: 3),
                                              ),
                                            );
                                          }
                                        }
                                      },
                                      icon: Icon(Icons.auto_fix_high, size: 16),
                                      label: Text('Audit & Fix All Logs'),
                                      style: ElevatedButton.styleFrom(
                                        backgroundColor: Colors.green,
                                        foregroundColor: Colors.white,
                                        padding: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                          ],
                        ),
                      ),
                    
                _fieldLabel('Program Name'),
                const SizedBox(height: 4),
                    Text('DEBUG: Available programs: ${availablePrograms.length}', style: TextStyle(fontSize: 10, color: Colors.grey)),
                    DropdownButtonFormField<Program>(
                  value: selectedProgram,
                      items: availablePrograms.map((p) => DropdownMenuItem(
                        value: p,
                        child: Text(p.namaProgram),
                      )).toList(),
                      onChanged: _onProgramChanged,
                  decoration: InputDecoration(
                    hintText: 'Select Program',
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
                    Text('DEBUG: Available vehicles: ${availableVehicles.length}, Filtered: ${getFilteredVehicles().length}', style: TextStyle(fontSize: 10, color: Colors.grey)),
                    DropdownButtonFormField<Vehicle>(
                  value: selectedVehicle,
                      items: getFilteredVehicles().map((v) => DropdownMenuItem(
                        value: v,
                        child: Text('${v.noPlat} - ${v.jenama} ${v.model}'),
                      )).toList(),
                  onChanged: (v) => setState(() => selectedVehicle = v),
                  decoration: InputDecoration(
                    hintText: 'Select Vehicle',
                    border: OutlineInputBorder(borderRadius: BorderRadius.circular(3)),
                    isDense: true,
                    contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                    prefixIcon: Icon(Icons.directions_car, color: PastelColors.primary, size: 20),
                  ),
                  style: AppTextStyles.bodyLarge,
                ),
                    if (selectedVehicle != null) ...[
                      const SizedBox(height: 8),
                      Container(
                        padding: EdgeInsets.all(8),
                        decoration: BoxDecoration(
                          color: PastelColors.info.withOpacity(0.1),
                          border: Border.all(color: PastelColors.info),
                          borderRadius: BorderRadius.circular(4),
                        ),
                        child: Text(
                          'Current Vehicle Odometer: ${selectedVehicle!.odometerSemasa} km',
                          style: AppTextStyles.bodySmall.copyWith(color: PastelColors.infoText),
                        ),
                      ),
                    ],
                const SizedBox(height: 16),
                _fieldLabel('Current Odometer'),
                const SizedBox(height: 4),
                TextField(
                  controller: odometerController,
                  keyboardType: TextInputType.number,
                  decoration: InputDecoration(
                    hintText: 'Enter current odometer',
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
                              icon: Icon(Icons.camera_alt, size: 18),
                              label: Text('Take Photo', style: AppTextStyles.bodyLarge),
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
                _fieldLabel('Location Program'),
                const SizedBox(height: 4),
                TextField(
                      controller: locationController,
                  enabled: false,
                  decoration: InputDecoration(
                        hintText: 'Program location',
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
                        hintText: 'Estimated kilometers',
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
                        hintText: 'Requested by',
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
                        hintText: 'Program notes',
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
                  enabled: false,
                  decoration: InputDecoration(
                        hintText: currentLocation,
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
                        hintText: 'Current date and time',
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
                        onPressed: isSubmitting ? null : _submitCheckIn,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: PastelColors.primary,
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(vertical: 16),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(3)),
                    ),
                        child: isSubmitting
                          ? Row(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                SizedBox(
                                  width: 16,
                                  height: 16,
                                  child: CircularProgressIndicator(
                                    strokeWidth: 2,
                                    valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                                  ),
                                ),
                                const SizedBox(width: 8),
                                Text('Processing...', style: AppTextStyles.bodyLarge.copyWith(color: Colors.white)),
                              ],
                            )
                          : Text('Check In', style: AppTextStyles.bodyLarge.copyWith(color: Colors.white)),
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
    return Text(label, style: AppTextStyles.bodyMedium.copyWith(fontWeight: FontWeight.w600, color: PastelColors.textPrimary));
  }
} 