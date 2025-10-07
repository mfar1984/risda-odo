import 'dart:io';
import 'package:dio/dio.dart';
import '../core/api_client.dart';
import '../core/constants.dart';
import '../models/program.dart';
import '../models/vehicle.dart';
import '../models/driver_log.dart';

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
      
      // Backend response structure:
      // {
      //   "success": true,
      //   "message": "Login berjaya",
      //   "data": {
      //     "token": "...",
      //     "token_type": "Bearer",
      //     "user": { ... }
      //   }
      // }
      
      return response.data;
    } catch (e) {
      rethrow;
    }
  }
  
  /// Get Current User
  Future<Map<String, dynamic>> getCurrentUser() async {
    try {
      final response = await _apiClient.dio.get(ApiConstants.getUser);
      
      // Backend response structure:
      // {
      //   "success": true,
      //   "data": { user object }
      // }
      
      return response.data;
    } catch (e) {
      rethrow;
    }
  }
  
  /// Logout All Devices
  Future<void> logoutAll() async {
    try {
      await _apiClient.dio.post(ApiConstants.logoutAll);
    } catch (e) {
    }
  }

  /// Upload Profile Picture
  Future<Map<String, dynamic>> uploadProfilePicture(File imageFile) async {
    try {
      FormData formData = FormData.fromMap({
        'profile_picture': await MultipartFile.fromFile(
          imageFile.path,
          filename: 'profile_${DateTime.now().millisecondsSinceEpoch}.jpg',
        ),
      });

      final response = await _apiClient.dio.post(
        '/user/profile-picture',
        data: formData,
      );
      
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  /// Delete Profile Picture
  Future<void> deleteProfilePicture() async {
    try {
      await _apiClient.dio.delete('/user/profile-picture');
    } catch (e) {
      rethrow;
    }
  }

  /// Change Password
  Future<Map<String, dynamic>> changePassword({
    required String currentPassword,
    required String newPassword,
    required String newPasswordConfirmation,
  }) async {
    try {
      final response = await _apiClient.dio.put(
        '/user/change-password',
        data: {
          'current_password': currentPassword,
          'new_password': newPassword,
          'new_password_confirmation': newPasswordConfirmation,
        },
      );
      
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  /// Get Programs with status filter (current, ongoing, past)
  Future<Map<String, dynamic>> getPrograms({String? status}) async {
    try {
      final queryParams = status != null ? {'status': status} : null;
      final response = await _apiClient.dio.get(
        '/programs',
        queryParameters: queryParams,
      );
      
      // Backend response structure:
      // {
      //   "success": true,
      //   "data": [ ... program objects ... ],
      //   "meta": { "total": 5, "filter": "current" }
      // }
      
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  /// Get Program Detail by ID
  Future<Map<String, dynamic>> getProgramDetail(int programId) async {
    try {
      final response = await _apiClient.dio.get('/programs/$programId');
      
      // Backend response structure:
      // {
      //   "success": true,
      //   "data": { ... program object with details ... }
      // }
      
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  /// Get Active Journey
  Future<Map<String, dynamic>> getActiveJourney() async {
    try {
      final response = await _apiClient.dio.get('/log-pemandu/active');
      
      // Backend response structure:
      // {
      //   "success": true,
      //   "data": { ... log object ... } or null,
      //   "message": "..."
      // }
      
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  /// Get All Logs (alias for getDriverLogs)
  Future<Map<String, dynamic>> getLogs({String? status}) async {
    return getDriverLogs(status: status);
  }

  /// Get All Driver Logs
  Future<Map<String, dynamic>> getDriverLogs({String? status}) async {
    try {
      final queryParams = status != null ? {'status': status} : null;
      final response = await _apiClient.dio.get(
        '/log-pemandu',
        queryParameters: queryParams,
      );
      
      // Backend response structure:
      // {
      //   "success": true,
      //   "data": [ ... log objects ... ],
      //   "meta": { "total": 5, "filter": "aktif" }
      // }
      
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  /// Start Journey (Check-Out)
  Future<Map<String, dynamic>> startJourney({
    required int programId,
    required int kenderaanId,
    required int odometerKeluar,
    double? lokasiKeluarLat,
    double? lokasiKeluarLong,
    String? catatan,
    List<int>? fotoOdometerKeluarBytes, // Image bytes (works for both web & mobile)
    String? fotoOdometerKeluarFilename,
  }) async {
    try {
      // Use FormData for multipart file upload
      final formData = FormData.fromMap({
        'program_id': programId,
        'kenderaan_id': kenderaanId,
        'odometer_keluar': odometerKeluar,
        if (lokasiKeluarLat != null) 'lokasi_keluar_lat': lokasiKeluarLat,
        if (lokasiKeluarLong != null) 'lokasi_keluar_long': lokasiKeluarLong,
        if (catatan != null) 'catatan': catatan,
        if (fotoOdometerKeluarBytes != null)
          'foto_odometer_keluar': MultipartFile.fromBytes(
            fotoOdometerKeluarBytes,
            filename: fotoOdometerKeluarFilename ?? 'odometer_${DateTime.now().millisecondsSinceEpoch}.jpg',
          ),
      });

      final response = await _apiClient.dio.post(
        '/log-pemandu/start',
        data: formData,
      );
      
      // Backend response structure:
      // {
      //   "success": true,
      //   "message": "Perjalanan dimulakan",
      //   "data": { ... log object ... }
      // }
      
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  /// End Journey (Check-In)
  Future<Map<String, dynamic>> endJourney({
    required int logId,
    required int odometerMasuk,
    double? lokasiCheckinLat,
    double? lokasiCheckinLong,
    String? catatan,
    double? literMinyak,
    double? kosMinyak,
    String? stesenMinyak,
    List<int>? fotoOdometerMasukBytes, // Image bytes (works for both web & mobile)
    String? fotoOdometerMasukFilename,
    List<int>? resitMinyakBytes, // Image bytes (works for both web & mobile)
    String? resitMinyakFilename,
  }) async {
    try {
      // Use FormData for multipart file upload
      // Note: Using POST with _method=PUT because Laravel doesn't parse FormData for PUT requests
      final formData = FormData.fromMap({
        '_method': 'PUT', // Laravel method spoofing for FormData
        'odometer_masuk': odometerMasuk,
        if (lokasiCheckinLat != null) 'lokasi_checkin_lat': lokasiCheckinLat,
        if (lokasiCheckinLong != null) 'lokasi_checkin_long': lokasiCheckinLong,
        if (catatan != null) 'catatan': catatan,
        if (literMinyak != null) 'liter_minyak': literMinyak,
        if (kosMinyak != null) 'kos_minyak': kosMinyak,
        if (stesenMinyak != null) 'stesen_minyak': stesenMinyak,
        if (fotoOdometerMasukBytes != null)
          'foto_odometer_masuk': MultipartFile.fromBytes(
            fotoOdometerMasukBytes,
            filename: fotoOdometerMasukFilename ?? 'odometer_checkin_${DateTime.now().millisecondsSinceEpoch}.jpg',
          ),
        if (resitMinyakBytes != null)
          'resit_minyak': MultipartFile.fromBytes(
            resitMinyakBytes,
            filename: resitMinyakFilename ?? 'fuel_receipt_${DateTime.now().millisecondsSinceEpoch}.jpg',
          ),
      });

      // Use POST with _method=PUT for FormData compatibility
      final response = await _apiClient.dio.post(
        '/log-pemandu/$logId/end',
        data: formData,
      );
      
      // Backend response structure:
      // {
      //   "success": true,
      //   "message": "Perjalanan berjaya ditamatkan",
      //   "data": { ... log object ... }
      // }
      
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  /// Logout
  Future<void> logout() async {
    try {
      await _apiClient.dio.post(ApiConstants.logout);
    } catch (e) {
      // Ignore errors on logout
    }
  }

  /// Get Active Programs
  Future<List<Program>> getActivePrograms() async {
    try {
      final response = await _apiClient.dio.get(ApiConstants.activePrograms);
      final List<dynamic> data = response.data['data'] ?? response.data;
      return data.map((json) => Program.fromJson(json)).toList();
    } catch (e) {
      rethrow;
    }
  }

  /// Get Vehicles
  Future<List<Vehicle>> getVehicles() async {
    try {
      // Gracefully handle 404 (endpoint not available) without throwing
      final response = await _apiClient.dio.get(
        ApiConstants.vehicles,
        options: Options(validateStatus: (_) => true),
      );
      if (response.statusCode == 200) {
        final List<dynamic> data = response.data['data'] ?? response.data;
        return data.map((json) => Vehicle.fromJson(json)).toList();
      }
      return <Vehicle>[];
    } catch (e) {
      return <Vehicle>[];
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
      rethrow;
    }
  }

  // ==================== TUNTUTAN/CLAIMS API ====================

  /// Get all claims for authenticated driver
  Future<Map<String, dynamic>> getClaims({String? status}) async {
    try {
      final queryParams = status != null ? {'status': status} : null;
      final response = await _apiClient.dio.get(
        '/tuntutan',
        queryParameters: queryParams,
      );
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  /// Get single claim by ID
  Future<Map<String, dynamic>> getClaim(int id) async {
    try {
      final response = await _apiClient.dio.get('/tuntutan/$id');
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  /// Create new claim
  Future<Map<String, dynamic>> createClaim({
    required int logPemanduId,
    required String kategori,
    required double jumlah,
    String? keterangan,
    List<int>? resitBytes,
    String? resitFilename,
  }) async {
    try {
      final formData = FormData.fromMap({
        'log_pemandu_id': logPemanduId,
        'kategori': kategori,
        'jumlah': jumlah,
        if (keterangan != null) 'keterangan': keterangan,
        if (resitBytes != null && resitFilename != null)
          'resit': MultipartFile.fromBytes(
            resitBytes,
            filename: resitFilename,
          ),
      });

      final response = await _apiClient.dio.post(
        '/tuntutan',
        data: formData,
      );

      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  /// Update claim (only if status = ditolak)
  Future<Map<String, dynamic>> updateClaim({
    required int id,
    required String kategori,
    required double jumlah,
    String? keterangan,
    List<int>? resitBytes,
    String? resitFilename,
  }) async {
    try {
      final formData = FormData.fromMap({
        '_method': 'PUT', // Laravel method spoofing
        'kategori': kategori,
        'jumlah': jumlah,
        if (keterangan != null) 'keterangan': keterangan,
        if (resitBytes != null && resitFilename != null)
          'resit': MultipartFile.fromBytes(
            resitBytes,
            filename: resitFilename,
          ),
      });

      final response = await _apiClient.dio.post(
        '/tuntutan/$id',
        data: formData,
      );

      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  // ==================== REPORTS API ====================

  /// Get Vehicle Report
  Future<Map<String, dynamic>> getVehicleReport({
    String? dateFrom,
    String? dateTo,
  }) async {
    try {
      final response = await _apiClient.dio.get(
        '/reports/vehicle',
        queryParameters: {
          if (dateFrom != null) 'date_from': dateFrom,
          if (dateTo != null) 'date_to': dateTo,
        },
      );

      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  /// Get Cost Report
  Future<Map<String, dynamic>> getCostReport({
    String? dateFrom,
    String? dateTo,
  }) async {
    try {
      final response = await _apiClient.dio.get(
        '/reports/cost',
        queryParameters: {
          if (dateFrom != null) 'date_from': dateFrom,
          if (dateTo != null) 'date_to': dateTo,
        },
      );

      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  /// Get Driver Report
  Future<Map<String, dynamic>> getDriverReport({
    String? dateFrom,
    String? dateTo,
  }) async {
    try {
      final response = await _apiClient.dio.get(
        '/reports/driver',
        queryParameters: {
          if (dateFrom != null) 'date_from': dateFrom,
          if (dateTo != null) 'date_to': dateTo,
        },
      );

      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  /// Get Dashboard Statistics
  Future<Map<String, dynamic>> getDashboardStatistics() async {
    try {
      final response = await _apiClient.dio.get('/dashboard/statistics');
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  // ==================== App Information ====================
  
  /// Get app information (public endpoint - no auth required)
  Future<Map<String, dynamic>> getAppInfo() async {
    try {
      final response = await _apiClient.dio.get('/app-info');
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  // ==================== Privacy Policy ====================
  
  /// Get privacy policy (public endpoint - no auth required)
  Future<Map<String, dynamic>> getPrivacyPolicy() async {
    try {
      final response = await _apiClient.dio.get('/privacy-policy');
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  // ==================== Chart Data ====================
  
  /// Get overview chart data (Fuel Cost vs Claims)
  Future<Map<String, dynamic>> getOverviewChartData({String period = '6months'}) async {
    try {
      final response = await _apiClient.dio.get('/chart/overview', queryParameters: {'period': period});
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  /// Get Do tab chart data (Start vs End Journey)
  Future<Map<String, dynamic>> getDoActivityChartData({String period = '6months'}) async {
    try {
      final response = await _apiClient.dio.get('/chart/do-activity', queryParameters: {'period': period});
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  /// Register FCM token
  Future<Map<String, dynamic>> registerFcmToken(String token) async {
    try {
      final response = await _apiClient.dio.post('/notifications/register-token', data: {
        'token': token,
        'device_type': 'android', // TODO: Detect platform
        'device_id': 'flutter-web-or-mobile', // TODO: Get actual device ID
      });
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  /// Remove FCM token
  Future<Map<String, dynamic>> removeFcmToken(String token) async {
    try {
      final response = await _apiClient.dio.post('/notifications/remove-token', data: {
        'token': token,
      });
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  /// Get all notifications
  Future<Map<String, dynamic>> getNotifications({bool? unreadOnly}) async {
    try {
      final response = await _apiClient.dio.get('/notifications', queryParameters: {
        if (unreadOnly != null) 'unread_only': unreadOnly,
      });
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  /// Mark notification as read
  Future<Map<String, dynamic>> markNotificationAsRead(int id) async {
    try {
      // Accept non-2xx to avoid throwing; we'll treat non-200 as soft-fail
      final response = await _apiClient.dio.post(
        '/notifications/$id/mark-as-read',
        options: Options(validateStatus: (_) => true),
      );
      if (response.statusCode == 200) {
        return response.data;
      }
      return {
        'success': false,
        'status': response.statusCode,
        'message': response.statusMessage ?? 'Server returned ${response.statusCode}',
      };
    } catch (e) {
      return {'success': false, 'message': e.toString()};
    }
  }

  /// Mark all notifications as read
  Future<Map<String, dynamic>> markAllNotificationsAsRead() async {
    try {
      final response = await _apiClient.dio.post('/notifications/mark-all-as-read');
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  // ============================================
  // SUPPORT TICKETING ENDPOINTS
  // ============================================

  /// Get all support tickets for driver
  Future<Map<String, dynamic>> getSupportTickets({String? status}) async {
    try {
      final queryParams = status != null ? {'status': status} : null;
      final response = await _apiClient.dio.get(
        '/support/tickets',
        queryParameters: queryParams,
      );
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  /// Get single ticket details with messages
  Future<Map<String, dynamic>> getSupportTicketDetail(int ticketId) async {
    try {
      final response = await _apiClient.dio.get('/support/tickets/$ticketId');
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  /// Create new support ticket
  Future<Map<String, dynamic>> createSupportTicket({
    required String subject,
    required String category,
    required String priority,
    required String message,
    double? latitude,
    double? longitude,
    List<File>? attachments,
  }) async {
    try {
      FormData formData = FormData.fromMap({
        'subject': subject,
        'category': category,
        'priority': priority,
        'message': message,
        if (latitude != null) 'latitude': latitude,
        if (longitude != null) 'longitude': longitude,
      });

      // Add attachments if any
      if (attachments != null && attachments.isNotEmpty) {
        for (var file in attachments) {
          formData.files.add(MapEntry(
            'attachments[]',
            await MultipartFile.fromFile(file.path),
          ));
        }
      }

      final response = await _apiClient.dio.post(
        '/support/tickets',
        data: formData,
      );
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  /// Send message/reply to ticket
  Future<Map<String, dynamic>> sendSupportMessage({
    required int ticketId,
    required String message,
    double? latitude,
    double? longitude,
    List<File>? attachments,
  }) async {
    try {
      FormData formData = FormData.fromMap({
        'message': message,
        if (latitude != null) 'latitude': latitude,
        if (longitude != null) 'longitude': longitude,
      });

      if (attachments != null && attachments.isNotEmpty) {
        for (var file in attachments) {
          formData.files.add(MapEntry(
            'attachments[]',
            await MultipartFile.fromFile(file.path),
          ));
        }
      }

      final response = await _apiClient.dio.post(
        '/support/tickets/$ticketId/messages',
        data: formData,
      );
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  /// Get messages for a ticket (for real-time sync)
  Future<Map<String, dynamic>> getSupportMessages(int ticketId) async {
    try {
      final response = await _apiClient.dio.get('/support/tickets/$ticketId/messages');
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  /// Get ticket status (for polling)
  Future<Map<String, dynamic>> getSupportTicketStatus(int ticketId) async {
    try {
      final response = await _apiClient.dio.get('/support/tickets/$ticketId/status');
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  /// Reopen closed ticket
  Future<Map<String, dynamic>> reopenSupportTicket(int ticketId) async {
    try {
      final response = await _apiClient.dio.post('/support/tickets/$ticketId/reopen');
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  /// Delete ticket (only if status is 'baru')
  Future<Map<String, dynamic>> deleteSupportTicket(int ticketId) async {
    try {
      final response = await _apiClient.dio.delete('/support/tickets/$ticketId');
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  /// Update typing status
  Future<Map<String, dynamic>> updateTypingStatus(int ticketId) async {
    try {
      final response = await _apiClient.dio.post('/support/tickets/$ticketId/typing');
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  /// Get typing status
  Future<Map<String, dynamic>> getTypingStatus(int ticketId) async {
    try {
      final response = await _apiClient.dio.get('/support/tickets/$ticketId/typing');
      return response.data;
    } catch (e) {
      rethrow;
    }
  }
}

