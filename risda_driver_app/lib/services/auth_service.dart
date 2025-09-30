import 'package:flutter/foundation.dart';
import '../core/api_client.dart';
import '../models/user.dart';
import 'api_service.dart';
import 'dart:developer' as developer;

class AuthService extends ChangeNotifier {
  final ApiClient _apiClient;
  final ApiService _apiService;
  
  User? _currentUser;
  String? _authToken;
  bool _isLoading = false;

  AuthService(this._apiClient, this._apiService);

  User? get currentUser => _currentUser;
  String? get authToken => _authToken;
  bool get isLoading => _isLoading;
  bool get isLoggedIn => _currentUser != null && _authToken != null;

  /// Login - DUMMY MODE untuk testing design
  Future<bool> login(String email, String password) async {
    try {
      _setLoading(true);
      
      // 🎨 DUMMY LOGIN FOR DESIGN TESTING
      // Kredensial yang diterima:
      // Email: demo@risda.my ATAU faizan@jara.my
      // Password: password
      
      await Future.delayed(const Duration(seconds: 1)); // Simulasi network delay
      
      if ((email == 'demo@risda.my' || email == 'faizan@jara.my') && password == 'password') {
        _authToken = 'dummy_token_12345';
        _apiClient.setAuthToken(_authToken);
        
        // Create dummy user data
        _currentUser = User(
          id: 1,
          name: email == 'demo@risda.my' ? 'Demo User' : 'Muhammad Faizan',
          email: email,
          phone: '011-1234 5678',
          staffId: 'RISDA001',
          stationName: 'JARA',
          divisionName: 'Bahagian Teknologi Maklumat',
          jenisOrganisasi: 'stesen',
          organisasiId: 1,
          createdAt: DateTime.now(),
        );
        
        _setLoading(false);
        notifyListeners();
        return true;
      }
      
      // Login failed - wrong credentials
      _setLoading(false);
      return false;
      
      /* 🔌 REAL API LOGIN (Commented untuk design testing)
      final response = await _apiService.login(email, password);
      
      if (response['success'] == true || response['token'] != null) {
        _authToken = response['token'];
        _apiClient.setAuthToken(_authToken);
        
        if (response['user'] != null) {
          _currentUser = User.fromJson(response['user']);
        } else {
          _currentUser = await _apiService.getProfile();
        }
        
        notifyListeners();
        _setLoading(false);
        return true;
      }
      
      _setLoading(false);
      return false;
      */
    } catch (e) {
      developer.log('Login error: $e');
      _setLoading(false);
      return false;
    }
  }

  /// Logout - DUMMY MODE
  Future<void> logout() async {
    try {
      // 🎨 DUMMY LOGOUT - Skip API call
      // await _apiService.logout();
      await Future.delayed(const Duration(milliseconds: 500));
    } catch (e) {
      developer.log('Logout error: $e');
    } finally {
      _currentUser = null;
      _authToken = null;
      _apiClient.clearAuthToken();
      notifyListeners();
    }
  }

  /// Update Profile
  Future<bool> updateProfile(Map<String, dynamic> data) async {
    try {
      _setLoading(true);
      _currentUser = await _apiService.updateProfile(data);
      notifyListeners();
      _setLoading(false);
      return true;
    } catch (e) {
      developer.log('Update profile error: $e');
      _setLoading(false);
      return false;
    }
  }

  void _setLoading(bool loading) {
    _isLoading = loading;
    notifyListeners();
  }
}
