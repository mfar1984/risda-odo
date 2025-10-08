import 'package:flutter/foundation.dart';
import '../models/auth_hive_model.dart';
import 'hive_service.dart';
import 'api_service.dart';
import '../core/api_client.dart';
import 'firebase_service.dart';
 

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
        // Session persists until user manually logs out (no expiry)
        // Set auth token in API client for subsequent requests
        _apiClient.setAuthToken(_currentAuth!.token);
        
        // Load full user data from Hive settings box (raw storage)
        final cachedData = HiveService.settingsBox.get('full_user_data');
        if (cachedData != null && cachedData is Map) {
          _fullUserData = Map<String, dynamic>.from(cachedData as Map);
        }
        
        
        // Try to refresh user data if online (will happen in background)
        // _tryRefreshToken();
      } else {
        
      }
    } catch (e) {
      
      _currentAuth = null;
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  String? _lastErrorMessage;
  String? get lastErrorMessage => _lastErrorMessage;
  
  // Reference to sync service (will be injected)
  dynamic _syncService;
  void setSyncService(dynamic syncService) {
    _syncService = syncService;
  }

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

        // Initialize FCM for this account (register token)
        try {
          await FirebaseService().initialize();
        } catch (e) {
          // Non-fatal if FCM init fails
        }

        // Sync master data after successful login (cold sync)
        if (_syncService != null) {
          try {
            await _syncService.syncAllMasterData();
            // Reconcile TripState and ensure odometer cache for offline usage
            await _syncService.reconcileTripState();
            await _syncService.ensureVehicleOdometerCache();
          } catch (e) {}
        }
        
        return true;
      }

      // Login failed - store error message from API
      _lastErrorMessage = response['message'] ?? 'Login gagal';
      
      _isLoading = false;
      notifyListeners();
      return false;

    } catch (e) {
      // Network or other error
      _lastErrorMessage = 'Ralat sambungan. Cuba lagi.';
      
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  /// Logout (REAL API)
  /// Clears all user-related data (including master caches) to prevent cross-account contamination
  Future<void> logout() async {
    try {
      
      
      // Check pending sync data
      final pendingCount = HiveService.getTotalPendingSyncCount();
      if (pendingCount > 0) {
        // Note: UI should handle this warning before calling logout()
      }
      
      // Remove FCM token
      try {
        await FirebaseService().removeToken();
      } catch (e) {
        
      }
      
      // Call API logout endpoint (revoke current token)
      try {
        await _apiService.logout();
      } catch (e) {
        
      }

      // Clear API client token
      _apiClient.clearAuthToken();

      // ============================================
      // CLEAR USER-SPECIFIC DATA
      // ============================================
      await HiveService.clearAuth();                          // Auth session
      await HiveService.settingsBox.delete('full_user_data'); // User profile
      await HiveService.journeyBox.clear();                   // User journeys
      await HiveService.claimBox.clear();                     // User claims
      await HiveService.syncQueueBox.clear();                 // Pending sync
      // Remove cached program details (settings keys)
      try {
        final keys = HiveService.settingsBox.keys.toList();
        for (final k in keys) {
          if (k is String && k.startsWith('program_detail_')) {
            await HiveService.settingsBox.delete(k);
          }
        }
      } catch (e) {}

      // Also clear master data to avoid leftover from another account
      try { await HiveService.programBox.clear(); } catch (e) {}
      try { await HiveService.vehicleBox.clear(); } catch (e) {}
      
      final programsKept = HiveService.programBox.length;
      final vehiclesKept = HiveService.vehicleBox.length;

      _currentAuth = null;
      _fullUserData = null;
      notifyListeners();
      
      
    } catch (e) {
      
      rethrow;
    }
  }

  /// Refresh user data from API (updates profile picture, etc.)
  Future<void> refreshUserData() async {
    try {
      
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
      }
    } catch (e) {
      
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
      //     // token refreshed
      //   }
      // }
    } catch (e) {
      
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
