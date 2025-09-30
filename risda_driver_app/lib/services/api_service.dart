import 'dart:io';
import 'package:dio/dio.dart';
import '../core/api_client.dart';
import '../core/constants.dart';
import '../models/user.dart';
import '../models/program.dart';
import '../models/vehicle.dart';
import '../models/driver_log.dart';
import 'dart:developer' as developer;

class ApiService {
  final ApiClient _apiClient;

  ApiService(this._apiClient);

  /// Authentication - Login
  Future<Map<String, dynamic>> login(String email, String password) async {
    try {
      final response = await _apiClient.dio.post(
        ApiConstants.login,
        data: {
          'email': email,
          'password': password,
        },
      );
      return response.data;
    } catch (e) {
      developer.log('Login error: $e');
      rethrow;
    }
  }

  /// Get Profile
  Future<User> getProfile() async {
    try {
      final response = await _apiClient.dio.get(ApiConstants.profile);
      return User.fromJson(response.data['data'] ?? response.data);
    } catch (e) {
      developer.log('Get profile error: $e');
      rethrow;
    }
  }

  /// Update Profile
  Future<User> updateProfile(Map<String, dynamic> data) async {
    try {
      final response = await _apiClient.dio.post(
        ApiConstants.updateProfile,
        data: data,
      );
      return User.fromJson(response.data['data'] ?? response.data);
    } catch (e) {
      developer.log('Update profile error: $e');
      rethrow;
    }
  }

  /// Logout
  Future<void> logout() async {
    try {
      await _apiClient.dio.post(ApiConstants.logout);
    } catch (e) {
      // Ignore errors on logout
      developer.log('Logout error (ignored): $e');
    }
  }

  /// Get Active Programs
  Future<List<Program>> getActivePrograms() async {
    try {
      final response = await _apiClient.dio.get(ApiConstants.activePrograms);
      final List<dynamic> data = response.data['data'] ?? response.data;
      return data.map((json) => Program.fromJson(json)).toList();
    } catch (e) {
      developer.log('Get programs error: $e');
      rethrow;
    }
  }

  /// Get Vehicles
  Future<List<Vehicle>> getVehicles() async {
    try {
      final response = await _apiClient.dio.get(ApiConstants.vehicles);
      final List<dynamic> data = response.data['data'] ?? response.data;
      return data.map((json) => Vehicle.fromJson(json)).toList();
    } catch (e) {
      developer.log('Get vehicles error: $e');
      rethrow;
    }
  }

  /// Get Active Trip
  Future<DriverLog?> getActiveTrip() async {
    try {
      final response = await _apiClient.dio.get(ApiConstants.activeTrip);
      if (response.data['data'] != null) {
        return DriverLog.fromJson(response.data['data']);
      }
      return null;
    } catch (e) {
      developer.log('Get active trip error: $e');
      return null;
    }
  }

  /// Start Trip (Check-in)
  Future<DriverLog> startTrip({
    required int programId,
    required int kenderaanId,
    required String lokasiMula,
    required double bacaanOdometerMula,
    String? notaMula,
    File? odometerPhoto,
  }) async {
    try {
      FormData formData = FormData.fromMap({
        'program_id': programId,
        'kenderaan_id': kenderaanId,
        'lokasi_mula': lokasiMula,
        'bacaan_odometer_mula': bacaanOdometerMula,
        if (notaMula != null) 'nota_mula': notaMula,
      });

      if (odometerPhoto != null) {
        formData.files.add(MapEntry(
          'odometer_photo',
          await MultipartFile.fromFile(
            odometerPhoto.path,
            filename: 'odometer_checkin_${DateTime.now().millisecondsSinceEpoch}.jpg',
          ),
        ));
      }

      final response = await _apiClient.dio.post(
        ApiConstants.startTrip,
        data: formData,
      );
      
      return DriverLog.fromJson(response.data['data'] ?? response.data);
    } catch (e) {
      developer.log('Start trip error: $e');
      rethrow;
    }
  }

  /// End Trip (Check-out)
  Future<DriverLog> endTrip({
    required int logId,
    required double jarakPerjalanan,
    required double bacaanOdometerTamat,
    required String lokasiTamat,
    String? notaTamat,
    double? gpsLatitude,
    double? gpsLongitude,
    File? odometerPhoto,
  }) async {
    try {
      FormData formData = FormData.fromMap({
        'jarak_perjalanan': jarakPerjalanan,
        'bacaan_odometer_tamat': bacaanOdometerTamat,
        'lokasi_tamat': lokasiTamat,
        if (notaTamat != null) 'nota_tamat': notaTamat,
        if (gpsLatitude != null) 'gps_latitude': gpsLatitude,
        if (gpsLongitude != null) 'gps_longitude': gpsLongitude,
      });

      if (odometerPhoto != null) {
        formData.files.add(MapEntry(
          'odometer_photo',
          await MultipartFile.fromFile(
            odometerPhoto.path,
            filename: 'odometer_checkout_${DateTime.now().millisecondsSinceEpoch}.jpg',
          ),
        ));
      }

      final response = await _apiClient.dio.post(
        ApiConstants.endTrip(logId),
        data: formData,
      );
      
      return DriverLog.fromJson(response.data['data'] ?? response.data);
    } catch (e) {
      developer.log('End trip error: $e');
      rethrow;
    }
  }
}
