import 'dart:io';
import 'package:dio/dio.dart';
import 'dart:developer' as developer;
import 'package:flutter/foundation.dart';
import 'package:hive/hive.dart';
import 'package:path/path.dart' as path;
import '../services/hive_service.dart';
import '../models/sync_queue_model.dart';
import '../models/driver_log_model.dart';
import '../services/connectivity_service.dart';

class ApiService {
  // Update baseUrl to connect to the actual Laravel backend with correct port
  static const String baseUrl = 'http://172.20.10.2:8000/api';
  late Dio _dio;

  ApiService() {
    _dio = Dio(BaseOptions(
      baseUrl: baseUrl,
      connectTimeout: const Duration(seconds: 10),
      receiveTimeout: const Duration(seconds: 10),
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
    ));

    // Add auth interceptor
    _dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) {
        developer.log('API Request: ${options.method} ${options.path}');
        developer.log('Request data: ${options.data}');
        final auth = HiveService.getAuth();
        if (auth != null) {
          options.headers['Authorization'] = auth.authorizationHeader;
          developer.log('Using auth token: ${auth.authorizationHeader}');
        }
        return handler.next(options);
      },
      onResponse: (response, handler) {
        developer.log('API Response: ${response.statusCode} ${response.requestOptions.path}');
        developer.log('Response data: ${response.data}');
        return handler.next(response);
      },
      onError: (DioException error, handler) {
        developer.log('API Error: ${error.response?.statusCode} ${error.requestOptions.path}');
        developer.log('Error data: ${error.response?.data}');
        if (error.response?.statusCode == 401) {
          // Token expired or invalid
          HiveService.clearAuth();
        }
        return handler.next(error);
      },
    ));
  }

  // Authentication
  Future<Map<String, dynamic>> login(String email, String password) async {
    try {
      developer.log('Attempting to login with email: $email');
      final response = await _dio.post('/auth/login', data: {
        'email': email,
        'password': password,
      });
      return response.data;
    } catch (e) {
      developer.log('Login error: $e');
      rethrow;
    }
  }

  Future<Map<String, dynamic>> getProfile() async {
    try {
      final response = await _dio.get('/auth/profile');
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  Future<Map<String, dynamic>> updateProfile(Map<String, dynamic> data) async {
    try {
      final response = await _dio.post('/auth/update-profile', data: data);
      return response.data;
    } catch (e) {
      developer.log('Error updating profile: $e');
      rethrow;
    }
  }

  Future<void> logout() async {
    try {
      await _dio.post('/auth/logout');
    } catch (e) {
      // Ignore errors on logout
    }
  }

  // Programs
  Future<List<dynamic>> getActivePrograms() async {
    try {
      final response = await _dio.get('/programs/active');
      return response.data['data'];
    } catch (e) {
      rethrow;
    }
  }

  // Driver logs
  Future<Map<String, dynamic>> getActiveTrip() async {
    try {
      final response = await _dio.get('/logs/active');
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  Future<Map<String, dynamic>> startTrip(Map<String, dynamic> data) async {
    try {
      // Handle image upload if present
      if (data['odometer_photo'] != null && data['odometer_photo'].toString().isNotEmpty) {
        final photoPath = data['odometer_photo'];
        final photoFile = File(photoPath);

        if (await photoFile.exists()) {
          // Create FormData for file upload
          final formData = FormData.fromMap({
            'program_id': data['program_id'],
            'kenderaan_id': data['kenderaan_id'],
            'lokasi_mula': data['lokasi_mula'],
            'bacaan_odometer_mula': data['bacaan_odometer_mula'],
            'nota_mula': data['nota_mula'],
            'odometer_photo': await MultipartFile.fromFile(
              photoPath,
              filename: 'odometer_checkin_${DateTime.now().millisecondsSinceEpoch}.jpg',
            ),
          });

          final response = await _dio.post('/logs/start', data: formData);
          return response.data;
        }
      }

      // Fallback to regular JSON request if no image
      final response = await _dio.post('/logs/start', data: data);
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  Future<Map<String, dynamic>> endTrip(int logId, Map<String, dynamic> data) async {
    try {
      print('DEBUG: endTrip API call - Log ID: $logId');
      print('DEBUG: endTrip API call - Data: $data');

      // Check if the log is still active before attempting to end it
      try {
        print('DEBUG: Checking if log $logId is still active before ending it');
        final activeResponse = await _dio.get('/logs/active');

        bool isLogActive = false;
        if (activeResponse.data['success'] == true && activeResponse.data['data'] != null) {
          // Handle both single object and array responses
          List<dynamic> activeLogs = [];
          if (activeResponse.data['data'] is List) {
            activeLogs = activeResponse.data['data'];
          } else if (activeResponse.data['data'] is Map) {
            activeLogs = [activeResponse.data['data']];
          }

          // Check if our log ID is in the active logs
          for (var log in activeLogs) {
            if (log['id'] == logId) {
              isLogActive = true;
              print('DEBUG: Confirmed log $logId is still active on server');
              break;
            }
          }
        }

        if (!isLogActive) {
          print('DEBUG: WARNING - Log $logId is not active on server, may fail to end');
        }
      } catch (e) {
        // If check fails, continue anyway
        print('DEBUG: Failed to check log active status: $e');
      }

      // Handle image upload if present
      if (data['odometer_photo'] != null && data['odometer_photo'].toString().isNotEmpty) {
        final photoPath = data['odometer_photo'];
        final photoFile = File(photoPath);

        if (await photoFile.exists()) {
          print('DEBUG: endTrip - Photo file exists, creating FormData');
          // Create FormData for file upload with correct field names for backend
          final formData = FormData.fromMap({
            'jarak_perjalanan': data['jarak_perjalanan'],
            'bacaan_odometer_tamat': data['bacaan_odometer'], // Backend expects this field name
            'lokasi_tamat': data['lokasi_checkout'], // Backend expects this field name
            'nota_tamat': data['catatan'], // Backend expects this field name
            'gps_latitude': data['gps_latitude'],
            'gps_longitude': data['gps_longitude'],
            'odometer_photo': await MultipartFile.fromFile(
              photoPath,
              filename: 'odometer_checkout_${DateTime.now().millisecondsSinceEpoch}.jpg',
            ),
          });

          // Add checkout_time if available
          if (data['checkout_time'] != null) {
            formData.fields.add(MapEntry('checkout_time', data['checkout_time']));
            print('DEBUG: endTrip - Added checkout_time to FormData: ${data['checkout_time']}');
          }

          print('DEBUG: endTrip - FormData created: $formData');

          print('DEBUG: endTrip - Sending FormData to /logs/$logId/end');
          final response = await _dio.post('/logs/$logId/end', data: formData);
          print('DEBUG: endTrip - API Response: ${response.data}');

          // Verify checkout time in response
          if (response.data['success'] == true && response.data['data'] != null) {
            final checkoutTime = response.data['data']['checkout_time'];
            print('DEBUG: endTrip - Server checkout time: $checkoutTime');
          }

          return response.data;
        } else {
          print('DEBUG: endTrip - Photo file not found: $photoPath');
        }
      }

      // Fallback to regular JSON request if no image
      print('DEBUG: endTrip - Sending JSON data to /logs/$logId/end');

      // Convert field names for backend compatibility
      final jsonData = Map<String, dynamic>.from(data);
      if (jsonData.containsKey('bacaan_odometer')) {
        jsonData['bacaan_odometer_tamat'] = jsonData.remove('bacaan_odometer');
      }
      if (jsonData.containsKey('lokasi_checkout')) {
        jsonData['lokasi_tamat'] = jsonData.remove('lokasi_checkout');
      }
      if (jsonData.containsKey('catatan')) {
        jsonData['nota_tamat'] = jsonData.remove('catatan');
      }

      // Ensure checkout_time is preserved
      if (jsonData.containsKey('checkout_time')) {
        // Keep the checkout_time field as is - the backend expects this name
        print('DEBUG: endTrip - Using provided checkout time: ${jsonData['checkout_time']}');
      }

      print('DEBUG: endTrip - Converted JSON data: $jsonData');
      final response = await _dio.post('/logs/$logId/end', data: jsonData);
      print('DEBUG: endTrip - API Response: ${response.data}');

      // Verify checkout time in response
      if (response.data['success'] == true && response.data['data'] != null) {
        final checkoutTime = response.data['data']['checkout_time'];
        print('DEBUG: endTrip - Server checkout time: $checkoutTime');
      }

      return response.data;
    } catch (e) {
      print('DEBUG: endTrip - API Error: $e');
      print('DEBUG: endTrip - Error type: ${e.runtimeType}');
      rethrow;
    }
  }

  // Vehicles
  Future<List<dynamic>> getVehicles() async {
    try {
      final response = await _dio.get('/vehicles');
      return response.data['data'];
    } catch (e) {
      rethrow;
    }
  }

  // Process sync queue
  Future<Map<String, dynamic>> processSyncQueue({bool forceSync = false}) async {
    try {
      print('DEBUG: Processing sync queue...');

      // Check connectivity first
      final isConnected = await ConnectivityService().checkConnectivity();
      if (!isConnected && !forceSync) {
        print('DEBUG: No internet connection, skipping sync queue processing');
        return {'success': false, 'message': 'No internet connection'};
      }

      // Get sync queue
    final queue = HiveService.getSyncQueue();
      print('DEBUG: Queue item count: ${queue.length}');

      if (queue.isEmpty) {
        print('DEBUG: Sync queue is empty');
        return {'success': true, 'message': 'Sync queue is empty'};
      }

      print('DEBUG: Processing sync queue with ${queue.length} items');

      int successCount = 0;
      List<String> failedItems = [];

      for (var item in queue) {
      try {
        print('DEBUG: Processing sync item: ${item.method} ${item.endpoint}');
        print('DEBUG: Item data: ${item.data}');
        print('DEBUG: Item retry count: ${item.retryCount}');

          // Special handling for offline checkout
          if (item.endpoint == '/logs/offline_checkout') {
            print('DEBUG: Special handling for offline checkout');
            await handleOfflineCheckout(item.data);

            // Remove from queue
            await HiveService.removeSyncQueueItem(item.id);
            print('DEBUG: Successfully synced item ${item.id} - removed from queue');
            successCount++;
            continue;
          }

          // Regular API call
        Response response;

          // Handle file uploads for check-in and check-out
          if ((item.endpoint == '/logs/start' || item.endpoint.contains('/end')) &&
              item.data['odometer_photo'] != null) {

              final photoPath = item.data['odometer_photo'];
              final photoFile = File(photoPath);

              if (await photoFile.exists()) {
              print('DEBUG: Processing FormData upload for photo');

              // Create form data
              final formData = FormData();

              // Add all fields from data with field name mapping for checkout
              for (var entry in item.data.entries) {
                if (entry.key != 'odometer_photo') {
                  // Map field names for checkout endpoints
                  if (item.endpoint.contains('/end')) {
                    if (entry.key == 'bacaan_odometer') {
                      formData.fields.add(MapEntry('bacaan_odometer_tamat', entry.value.toString()));
                    } else if (entry.key == 'lokasi_checkout') {
                      formData.fields.add(MapEntry('lokasi_tamat', entry.value.toString()));
                    } else if (entry.key == 'catatan') {
                      formData.fields.add(MapEntry('nota_tamat', entry.value.toString()));
                    } else {
                      formData.fields.add(MapEntry(entry.key, entry.value.toString()));
                    }
                  } else {
                    // For non-checkout endpoints, use original field names
                    formData.fields.add(MapEntry(entry.key, entry.value.toString()));
                  }
                }
              }

              // Add photo
              formData.files.add(MapEntry(
                'odometer_photo',
                await MultipartFile.fromFile(photoPath, filename: path.basename(photoPath)),
              ));

              print('DEBUG: FormData created: $formData');

              // Make API call with FormData
              response = await _dio.request(
                item.endpoint,
                data: formData,
                options: Options(method: item.method),
              );
            } else {
              print('DEBUG: Photo file does not exist: $photoPath');
              // Remove photo field and proceed with regular request
              final dataWithoutPhoto = Map<String, dynamic>.from(item.data);
              dataWithoutPhoto.remove('odometer_photo');

              // Map field names for checkout endpoints
              if (item.endpoint.contains('/end')) {
                if (dataWithoutPhoto.containsKey('bacaan_odometer')) {
                  dataWithoutPhoto['bacaan_odometer_tamat'] = dataWithoutPhoto.remove('bacaan_odometer');
                }
                if (dataWithoutPhoto.containsKey('lokasi_checkout')) {
                  dataWithoutPhoto['lokasi_tamat'] = dataWithoutPhoto.remove('lokasi_checkout');
                }
                if (dataWithoutPhoto.containsKey('catatan')) {
                  dataWithoutPhoto['nota_tamat'] = dataWithoutPhoto.remove('catatan');
                }
              }

              response = await _dio.request(
                item.endpoint,
                data: dataWithoutPhoto,
                options: Options(method: item.method),
              );
            }
          } else {
            // Regular request without file upload
            final requestData = Map<String, dynamic>.from(item.data);

            // Map field names for checkout endpoints
            if (item.endpoint.contains('/end')) {
              if (requestData.containsKey('bacaan_odometer')) {
                requestData['bacaan_odometer_tamat'] = requestData.remove('bacaan_odometer');
              }
              if (requestData.containsKey('lokasi_checkout')) {
                requestData['lokasi_tamat'] = requestData.remove('lokasi_checkout');
              }
              if (requestData.containsKey('catatan')) {
                requestData['nota_tamat'] = requestData.remove('catatan');
              }
            }

            response = await _dio.request(
              item.endpoint,
              data: requestData,
              options: Options(method: item.method),
            );
        }

        print('DEBUG: API Response status: ${response.statusCode}');
        print('DEBUG: API Response data: ${response.data}');

          // Check if response is successful
          if (response.data['success'] == true) {
            // Remove from queue
            await HiveService.removeSyncQueueItem(item.id);
            print('DEBUG: Successfully synced item ${item.id} - removed from queue');
            successCount++;

            // Special handling for check-in response
            if (item.endpoint == '/logs/start' && response.data['data'] != null) {
              print('DEBUG: Updating local log with API response data');
              await _updateLocalLogWithApiResponse(response.data['data']);
            }
          } else {
            print('DEBUG: API returned success: false: ${response.data['message']}');

            // Special handling for "Log ini tidak aktif" error
            if (item.endpoint.contains('/end') && response.data['message'] == 'Log ini tidak aktif') {
              print('DEBUG: Log was already ended on server, removing from queue');
              await HiveService.removeSyncQueueItem(item.id);
              successCount++; // Count as success since the log is already in the desired state
              continue;
            }

            // Increment retry count
            item.retryCount++;
            if (item.retryCount < 3) {
              await HiveService.updateSyncQueueItem(item);
              print('DEBUG: Incremented retry count for item ${item.id} to ${item.retryCount}');
              failedItems.add(item.id);
            } else {
              print('DEBUG: Max retries reached for item ${item.id}, removing from queue');
              await HiveService.removeSyncQueueItem(item.id);
            }
        }
      } catch (e) {
          print('DEBUG: Error processing sync item ${item.id}: $e');
        print('DEBUG: Error type: ${e.runtimeType}');

          // Special handling for "Log ini tidak aktif" error
          if (e is DioException &&
              e.response?.statusCode == 400 &&
              e.response?.data is Map &&
              e.response?.data['message'] == 'Log ini tidak aktif' &&
              item.endpoint.contains('/end')) {
            print('DEBUG: Log was already ended on server, removing from queue');
            await HiveService.removeSyncQueueItem(item.id);
            successCount++; // Count as success since the log is already in the desired state
            continue;
          }

          // Increment retry count
        item.retryCount++;
          if (item.retryCount < 3) {
        await HiveService.updateSyncQueueItem(item);
            print('DEBUG: Incremented retry count for item ${item.id} to ${item.retryCount}');
            failedItems.add(item.id);
          } else {
            print('DEBUG: Max retries reached for item ${item.id}, removing from queue');
          await HiveService.removeSyncQueueItem(item.id);
          }
        }
      }

      print('DEBUG: Sync queue processed successfully');
      print('DEBUG: Successfully synced $successCount/${queue.length} items');

      return {
        'success': true,
        'message': 'Sync queue processed',
        'successCount': successCount,
        'totalCount': queue.length,
        'failedItems': failedItems,
      };
    } catch (e) {
      print('DEBUG: Error processing sync queue: $e');
      return {'success': false, 'message': 'Error processing sync queue: $e'};
    }
  }

  // Add to sync queue
  Future<void> addToSyncQueue({
    required String endpoint,
    required String method,
    required Map<String, dynamic> data,
  }) async {
    final item = SyncQueueItem.create(
      endpoint: endpoint,
      method: method,
      data: data,
    );

    print('DEBUG: Creating sync queue item: ${item.id}');
    print('DEBUG: Endpoint: $endpoint, Method: $method');
    print('DEBUG: Data: $data');

    await HiveService.addToSyncQueue(item);
    print('DEBUG: Sync queue item added successfully');
  }

  // Handle offline checkout
  Future<Map<String, dynamic>> handleOfflineCheckout(Map<String, dynamic> data) async {
    try {
      print('DEBUG: Handling offline checkout');
      print('DEBUG: Offline checkout data: $data');

      // Extract program and vehicle IDs from data
      final programId = data['program_id'];
      final kenderaanId = data['kenderaan_id'];
      final checkinTime = data['checkin_time'];

      print('DEBUG: Looking for active trip with Program ID: $programId, Vehicle ID: $kenderaanId');
      print('DEBUG: Original check-in time: $checkinTime');

      // Get active trips from server
      final response = await getActiveTrip();
      print('DEBUG: Active trips response: $response');

      // Check if we have an active trip that matches
      if (response['success'] == true && response['data'] != null) {
        // Handle both single object and array responses
        List<dynamic> trips = [];
        if (response['data'] is List) {
          trips = response['data'];
        } else if (response['data'] is Map) {
          trips = [response['data']];
        }

        print('DEBUG: Found ${trips.length} active trip${trips.length == 1 ? "" : "s"}');

        // Look for matching trip
        for (var trip in trips) {
          if (trip['program_id'] == programId && trip['kenderaan_id'] == kenderaanId) {
            print('DEBUG: Found matching active trip: ${trip['id']}');

            // Prepare checkout data in the format expected by the API
            final checkoutData = {
              'jarak_perjalanan': data['jarak_perjalanan'],
              'bacaan_odometer_tamat': data['bacaan_odometer'],
              'lokasi_tamat': data['lokasi_checkout'],
              'nota_tamat': data['catatan'] ?? 'N/A',
            };

            // Preserve the checkout time if available
            if (data['checkout_time'] != null) {
              checkoutData['checkout_time'] = data['checkout_time'];
              print('DEBUG: Using preserved checkout time for offline checkout: ${data['checkout_time']}');
            }

            // Add GPS coordinates if available
            if (data['gps_latitude'] != null && data['gps_longitude'] != null) {
              checkoutData['gps_latitude'] = data['gps_latitude'];
              checkoutData['gps_longitude'] = data['gps_longitude'];
            }

            // Add photo if available
            if (data['odometer_photo'] != null) {
              checkoutData['odometer_photo'] = data['odometer_photo'];
            }

            print('DEBUG: endTrip API call - Log ID: ${trip['id']}');
            print('DEBUG: endTrip API call - Data: $checkoutData');

            // Send checkout data to server
            try {
              // Check if the photo exists
              if (checkoutData.containsKey('odometer_photo')) {
                final photoPath = checkoutData['odometer_photo'];
                final photoFile = File(photoPath);

                if (await photoFile.exists()) {
                  print('DEBUG: endTrip - Photo file exists, creating FormData');

                  // Create form data for multipart request
                  final formData = FormData.fromMap({
                    'jarak_perjalanan': checkoutData['jarak_perjalanan'],
                    'bacaan_odometer_tamat': checkoutData['bacaan_odometer_tamat'],
                    'lokasi_tamat': checkoutData['lokasi_tamat'],
                    'nota_tamat': checkoutData['nota_tamat'],
                  });

                  // Add GPS coordinates if available
                  if (checkoutData.containsKey('gps_latitude') && checkoutData.containsKey('gps_longitude')) {
                    formData.fields.add(MapEntry('gps_latitude', checkoutData['gps_latitude'].toString()));
                    formData.fields.add(MapEntry('gps_longitude', checkoutData['gps_longitude'].toString()));
                  }

                  // Add photo
                  formData.files.add(MapEntry(
                    'odometer_photo',
                    await MultipartFile.fromFile(photoPath, filename: path.basename(photoPath)),
                  ));

                  print('DEBUG: endTrip - Sending FormData to /logs/${trip['id']}/end');
                  final response = await _dio.post('/logs/${trip['id']}/end', data: formData);
                  return response.data;
                } else {
                  print('DEBUG: endTrip - Photo file does not exist: $photoPath');
                  // Proceed without photo
                  final response = await _dio.post('/logs/${trip['id']}/end', data: checkoutData);
                  return response.data;
                }
              } else {
                // No photo, send regular data
                final response = await _dio.post('/logs/${trip['id']}/end', data: checkoutData);
                return response.data;
              }
            } catch (e) {
              print('DEBUG: endTrip - API Error: $e');
              print('DEBUG: endTrip - Error type: ${e.runtimeType}');
              rethrow;
            }
          }
        }

        // No matching trip found
        print('DEBUG: No matching active trip found for offline checkout');
        return {'success': false, 'message': 'No matching active trip found'};
      } else {
        print('DEBUG: No active trips found or API error');
        return {'success': false, 'message': 'No active trips found'};
      }
    } catch (e) {
      print('DEBUG: Error handling offline checkout: $e');
      throw Exception('Offline checkout failed: $e');
    }
  }

  // Check for active trip and sync it if found
  Future<Map<String, dynamic>> checkAndSyncActiveTrip() async {
    try {
      print('DEBUG: Checking for active trip on server');

      // First check if there's an active trip on the server
      final activeTrip = await getActiveTrip();

      if (activeTrip['success'] == true && activeTrip['data'] != null) {
        print('DEBUG: Found active trip on server: ${activeTrip['data']}');

        // Return the active trip data
        return {
          'success': true,
          'hasActiveTrip': true,
          'data': activeTrip['data'],
          'message': 'Active trip found on server'
        };
      } else {
        print('DEBUG: No active trip found on server');

        // Check if there are any active trips in local storage that need to be synced
        final localActiveLogs = HiveService.getActiveDriverLogs();

        if (localActiveLogs.isNotEmpty) {
          print('DEBUG: Found ${localActiveLogs.length} active logs in local storage');

          // Get the most recent active log
          final mostRecentLog = localActiveLogs.reduce((a, b) =>
            a.createdAt.isAfter(b.createdAt) ? a : b);

          print('DEBUG: Most recent active log: ${mostRecentLog.id}, created at ${mostRecentLog.createdAt}');

          // Try to sync this log
          final syncResult = await processSyncQueue(forceSync: true);

          if (syncResult['success']) {
            print('DEBUG: Successfully synced active log');

            // Check again for active trip on server
            final updatedActiveTrip = await getActiveTrip();

            if (updatedActiveTrip['success'] == true && updatedActiveTrip['data'] != null) {
              print('DEBUG: Found active trip on server after sync: ${updatedActiveTrip['data']}');

              return {
                'success': true,
                'hasActiveTrip': true,
                'data': updatedActiveTrip['data'],
                'message': 'Active trip found on server after sync',
                'synced': true
              };
            }
          }

          return {
            'success': true,
            'hasActiveTrip': true,
            'data': mostRecentLog.toJson(),
            'message': 'Active trip found in local storage',
            'synced': false
          };
        }

        return {
          'success': true,
          'hasActiveTrip': false,
          'message': 'No active trip found'
        };
      }
    } catch (e) {
      print('DEBUG: Error checking for active trip: $e');
      return {
        'success': false,
        'hasActiveTrip': false,
        'message': 'Error checking for active trip: $e'
      };
    }
  }

  // Helper to update local log with API response data
  Future<void> _updateLocalLogWithApiResponse(Map<String, dynamic> apiData) async {
    final log = DriverLog.fromJson(apiData);

    // Preserve existing program and vehicle details if API doesn't return them
    final existingLog = HiveService.getActiveDriverLog();
    if (existingLog != null) {
      // Preserve program and vehicle details if not in API response
      if (log.programNama == null) {
        log.programNama = existingLog.programNama;
        log.programLokasi = existingLog.programLokasi;
      }

      if (log.kenderaanNoPlat == null) {
        log.kenderaanNoPlat = existingLog.kenderaanNoPlat;
        log.kenderaanJenama = existingLog.kenderaanJenama;
        log.kenderaanModel = existingLog.kenderaanModel;
      }

      // If API doesn't provide timestamps, preserve the original ones
      if (log.checkinTime == null || (log.checkinTime != null && log.checkinTime!.year < 2000)) {
        log.checkinTime = existingLog.checkinTime;
        print('DEBUG: Preserved original checkin time: ${log.checkinTime}');
      }

      if (existingLog.checkoutTime != null &&
          (log.checkoutTime == null || log.checkoutTime!.year < 2000)) {
        log.checkoutTime = existingLog.checkoutTime;
        print('DEBUG: Preserved original checkout time: ${log.checkoutTime}');
      }

      print('DEBUG: Preserved existing program/vehicle details and timestamps');
    }

    // IMPROVED CHECKOUT HANDLING: Check for completed logs with the same program and vehicle
    print('DEBUG: Looking for completed logs that need to be synced with the new server ID');
    final allLogs = HiveService.getDriverLogs();

    // Find logs that are marked as completed but not synced
    final completedLogs = allLogs.where((l) =>
      l.status == 'selesai' &&
      !l.isSynced &&
      l.checkoutTime != null &&
      l.programId == log.programId &&
      l.kenderaanId == log.kenderaanId
    ).toList();

    print('DEBUG: Found ${completedLogs.length} completed logs that match program/vehicle');

    if (completedLogs.isNotEmpty) {
      // Sort by creation time to get the most recent one
      completedLogs.sort((a, b) => b.createdAt.compareTo(a.createdAt));
      final mostRecentLog = completedLogs.first;

      print('DEBUG: Most recent completed log - Created: ${mostRecentLog.createdAt}');
      print('DEBUG: Most recent completed log - Checkout: ${mostRecentLog.checkoutTime}');
      print('DEBUG: New check-in time from server: ${log.checkinTime}');

      // Check if this is likely the same trip (within 30 minutes)
      int timeDifference = 0;
      if (log.checkinTime != null && mostRecentLog.checkinTime != null) {
        timeDifference = log.checkinTime!.difference(mostRecentLog.checkinTime!).inMinutes.abs();
        print('DEBUG: Time difference between logs: $timeDifference minutes');
      } else {
        print('DEBUG: Cannot calculate time difference - one of the checkin times is null');
      }

      if (timeDifference < 30) {
        print('DEBUG: Found matching completed log within 30 minutes - adding checkout to queue');

        // Create checkout data
        final checkoutData = {
          'jarak_perjalanan': mostRecentLog.jarakPerjalanan ?? 0,
          'bacaan_odometer_tamat': mostRecentLog.bacaanOdometerCheckout ?? 0,
          'lokasi_tamat': mostRecentLog.lokasiCheckout ?? 'Unknown location',
          'nota_tamat': mostRecentLog.catatan ?? 'N/A',
          'gps_latitude': mostRecentLog.gpsLatitude,
          'gps_longitude': mostRecentLog.gpsLongitude,
        };

        if (mostRecentLog.odometerPhotoCheckout != null) {
          checkoutData['odometer_photo'] = mostRecentLog.odometerPhotoCheckout;
        }

        // Add checkout to sync queue with the new server ID
        print('DEBUG: Adding checkout to sync queue for server ID: ${log.id}');
        await addToSyncQueue(
          endpoint: '/logs/${log.id}/end',
          method: 'POST',
          data: checkoutData,
        );
        print('DEBUG: Checkout added to sync queue successfully');

        // Mark the local log as synced
        mostRecentLog.isSynced = true;
        await HiveService.updateDriverLog(mostRecentLog);
        print('DEBUG: Local log marked as synced');
      } else {
        print('DEBUG: Time difference too large (${timeDifference}m) - not the same trip');
      }
    } else {
      print('DEBUG: No completed logs found that need syncing');
    }

    await HiveService.saveDriverLog(log);
    print('DEBUG: Local log updated successfully with API data');
  }
}
