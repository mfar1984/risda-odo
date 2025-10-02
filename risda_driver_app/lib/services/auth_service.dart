import 'package:flutter/foundation.dart';
import '../models/auth_hive_model.dart';
import 'hive_service.dart';
import 'api_service.dart';
import '../core/api_client.dart';
import 'dart:developer' as developer;

class AuthService extends ChangeNotifier {
  AuthHive? _currentAuth;
  bool _isLoading = false;
  Map<String, dynamic>? _fullUserData; // Store complete API response
  
  // API Client & Service
  final ApiClient _apiClient = ApiClient();
  late final ApiService _apiService;

  AuthService() {
    _apiService = ApiService(_apiClient);
  }

  AuthHive? get currentAuth => _currentAuth;
  bool get isAuthenticated => _currentAuth != null;
  bool get isLoading => _isLoading;

  /// Initialize - Check Hive for existing session
  Future<void> initialize() async {
    _isLoading = true;
    notifyListeners();

    try {
      _currentAuth = HiveService.getCurrentAuth();
      
      if (_currentAuth != null) {
        // Check if session is still valid (< 7 days old)
        final daysSinceLogin = DateTime.now().difference(_currentAuth!.loginAt).inDays;
        
        if (daysSinceLogin > 7) {
          // Session expired, logout
          developer.log('⏰ Session expired (> 7 days), logging out...');
          await logout();
        } else {
          // Set auth token in API client for subsequent requests
          _apiClient.setAuthToken(_currentAuth!.token);
          
          // Load full user data from Hive settings box (raw storage)
          final cachedData = HiveService.settingsBox.get('full_user_data');
          if (cachedData != null && cachedData is Map) {
            _fullUserData = Map<String, dynamic>.from(cachedData as Map);
          }
          
          developer.log('✅ Session valid, auto-login successful');
          developer.log('👤 User: ${_currentAuth!.name} (${_currentAuth!.email})');
          // Session valid, try to refresh token if online (TODO: implement later)
          // _tryRefreshToken();
        }
      } else {
        developer.log('ℹ️ No cached session found');
      }
    } catch (e) {
      developer.log('❌ Auth initialization error: $e');
      _currentAuth = null;
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  String? _lastErrorMessage;
  String? get lastErrorMessage => _lastErrorMessage;

  /// Login with credentials (REAL API)
  Future<bool> login(String email, String password, {bool rememberMe = true}) async {
    _isLoading = true;
    _lastErrorMessage = null;
    notifyListeners();

    try {
      // 🚀 Call Real API
      final response = await _apiService.login(email.trim(), password);
      
      // Check if login successful
      if (response['success'] == true && response['data'] != null) {
        final data = response['data'];
        final user = data['user'];
        final token = data['token'];
        
        // Create auth data from API response
        final authData = AuthHive(
          token: token,
          userId: user['id'],
          name: user['name'],
          email: user['email'],
          jenisOrganisasi: user['jenis_organisasi'] ?? '',
          organisasiId: user['organisasi_id']?.toString() ?? '',
          organisasiName: user['stesen']?['nama'] ?? user['bahagian']?['nama'] ?? '',
          role: user['kumpulan']?['nama'] ?? 'Driver',
          loginAt: DateTime.now(),
          lastSync: DateTime.now(),
          rememberMe: rememberMe,
        );

        // Set auth token in API client for future requests
        _apiClient.setAuthToken(token);

        // Save to Hive
        await HiveService.saveAuth(authData);
        
        // Store full user data (including staf) to Hive settings box
        _fullUserData = data;
        await HiveService.settingsBox.put('full_user_data', data);

        _currentAuth = authData;
        _isLoading = false;
        notifyListeners();
        
        developer.log('✅ Login successful: ${authData.name} (${authData.email})');
        developer.log('📍 Organisasi: ${authData.organisasiName} (${authData.jenisOrganisasi})');
        if (data['user']['staf'] != null) {
          developer.log('👤 Staf: ${data['user']['staf']['no_pekerja']} - ${data['user']['staf']['jawatan']}');
        }
        return true;
      }

      // Login failed - store error message from API
      _lastErrorMessage = response['message'] ?? 'Login gagal';
      developer.log('❌ Login failed: $_lastErrorMessage');
      _isLoading = false;
      notifyListeners();
      return false;

    } catch (e) {
      // Network or other error
      _lastErrorMessage = 'Ralat sambungan. Cuba lagi.';
      developer.log('❌ Login error: $e');
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  /// Logout (REAL API)
  Future<void> logout() async {
    try {
      developer.log('🔐 Logging out...');
      
      // Call API logout endpoint (revoke current token)
      try {
        await _apiService.logout();
        developer.log('✅ API logout successful');
      } catch (e) {
        developer.log('⚠️ API logout failed (continuing with local logout): $e');
      }

      // Clear API client token
      _apiClient.clearAuthToken();

      // Clear Hive auth and full user data
      await HiveService.clearAuth();
      await HiveService.settingsBox.delete('full_user_data');

      _currentAuth = null;
      _fullUserData = null;
      notifyListeners();
      
      developer.log('✅ Logout successful');
    } catch (e) {
      developer.log('❌ Logout error: $e');
    }
  }

  /// Refresh user data from API (updates profile picture, etc.)
  Future<void> refreshUserData() async {
    try {
      developer.log('🔄 Refreshing user data from API...');
      
      final response = await _apiService.getCurrentUser();
      
      if (response['success'] == true && response['data'] != null) {
        // Update full user data
        _fullUserData = response['data'];
        
        // Save to Hive
        await HiveService.settingsBox.put('full_user_data', response['data']);
        
        // Update current auth if user data changed
        if (_currentAuth != null) {
          final user = response['data'];
          _currentAuth = AuthHive(
            token: _currentAuth!.token,
            userId: user['id'],
            name: user['name'],
            email: user['email'],
            jenisOrganisasi: user['jenis_organisasi'] ?? '',
            organisasiId: user['organisasi_id']?.toString() ?? '',
            organisasiName: user['stesen']?['nama'] ?? user['bahagian']?['nama'] ?? '',
            role: user['kumpulan']?['nama'] ?? 'Driver',
            loginAt: _currentAuth!.loginAt,
            lastSync: DateTime.now(),
            rememberMe: _currentAuth!.rememberMe,
          );
          await HiveService.saveAuth(_currentAuth!);
        }
        
        notifyListeners();
        developer.log('✅ User data refreshed successfully');
      }
    } catch (e) {
      developer.log('❌ Refresh user data error: $e');
      // Don't throw error, just log it
    }
  }

  /// Try to refresh token (if online)
  Future<void> _tryRefreshToken() async {
    try {
      // TODO: Call API to refresh token
      // if (await ConnectivityService.isOnline()) {
      //   final response = await apiService.post('/api/refresh-token');
      //   if (response.success) {
      //     _currentAuth!.token = response.data['token'];
      //     _currentAuth!.lastSync = DateTime.now();
      //     await _currentAuth!.save(); // HiveObject save method
      //     notifyListeners();
      //     developer.log('Token refreshed successfully');
      //   }
      // }
    } catch (e) {
      developer.log('Token refresh error: $e');
    }
  }

  /// Get current user data as Map (with full staf data)
  Map<String, dynamic> get currentUser {
    if (_currentAuth == null) return {};
    // Return full user data including staf info from API
    return _fullUserData ?? _currentAuth!.toJson();
  }

  /// Get user ID
  int? get userId => _currentAuth?.userId;

  /// Get user name
  String? get userName => _currentAuth?.name;

  /// Get user email
  String? get userEmail => _currentAuth?.email;

  /// Get auth token
  String? get authToken => _currentAuth?.token;
}
