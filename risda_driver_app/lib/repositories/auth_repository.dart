import 'dart:developer' as developer;
import '../services/api_service.dart';
import '../services/hive_service.dart';
import '../services/connectivity_service.dart';
import '../models/auth_model.dart';
import '../models/user_model.dart';

class AuthRepository {
  final ApiService _apiService;
  final ConnectivityService _connectivityService;

  AuthRepository({
    required ApiService apiService,
    required ConnectivityService connectivityService,
  })  : _apiService = apiService,
        _connectivityService = connectivityService;

  // Check if user is logged in
  bool isLoggedIn() {
    final isLoggedIn = HiveService.isLoggedIn();
    print('DEBUG: AuthRepository.isLoggedIn() called: $isLoggedIn');
    return isLoggedIn;
  }

  // Login user
  Future<bool> login(String email, String password) async {
    try {
      // Check connectivity
      final isConnected = await _connectivityService.checkConnectivity();
      
      if (isConnected) {
        // Online login
        developer.log('Attempting online login with email: $email');
        final response = await _apiService.login(email, password);
        
        // Log response for debugging
        developer.log('Login API response: $response');
        
        if (response['success'] == true) {
          // Save auth data
          final authData = response['data'];
          final auth = Auth.fromJson(authData);
          await HiveService.saveAuth(auth);
          
          // Save user data from the response
          if (authData.containsKey('user')) {
            final userData = authData['user'];
            final user = User(
              id: userData['id'],
              name: userData['name'],
              email: userData['email'],
              role: userData['role'],
              bahagian: userData['bahagian'],
              stesen: userData['stesen'],
            );
            await HiveService.saveUser(user);
          } else {
            // Try to get user profile if not included in login response
            try {
              final profileResponse = await _apiService.getProfile();
              developer.log('Profile API response: $profileResponse');
              
              if (profileResponse['success'] == true) {
                final userData = profileResponse['data'];
                final user = User.fromJson(userData);
                await HiveService.saveUser(user);
              }
            } catch (e) {
              developer.log('Error fetching profile: $e');
              // Continue even if profile fetch fails
            }
          }
          
          return true;
        }
      } else {
        // Offline login - check if credentials match stored data
        developer.log('Attempting offline login with email: $email');
        final user = HiveService.getUser();
        if (user != null && user.email == email) {
          // We can't verify password in offline mode as it's not stored
          // This is just a simple check - in real app, you might want to store a hash
          return true;
        }
      }
      
      return false;
    } catch (e) {
      developer.log('Login error: $e');
      return false;
    }
  }

  // Logout user
  Future<void> logout() async {
    try {
      print('DEBUG: Logging out user...');
      final isConnected = await _connectivityService.checkConnectivity();
      
      if (isConnected) {
        print('DEBUG: Attempting API logout...');
        await _apiService.logout();
      }
      
      // Clear local data regardless of connection status
      print('DEBUG: Clearing local auth data...');
      await HiveService.clearAuth();
      
      // Don't clear user data to allow offline login
      print('DEBUG: Logout completed');
    } catch (e) {
      print('DEBUG: Logout error: $e');
      // Ensure auth is cleared even if API call fails
      await HiveService.clearAuth();
    }
  }

  // Get current user
  User? getCurrentUser() {
    return HiveService.getUser();
  }

  // Refresh user profile
  Future<bool> refreshProfile() async {
    try {
      final isConnected = await _connectivityService.checkConnectivity();
      
      if (isConnected && isLoggedIn()) {
        final response = await _apiService.getProfile();
        
        if (response['success'] == true) {
          final userData = response['data'];
          final user = User.fromJson(userData);
          await HiveService.saveUser(user);
          return true;
        }
      }
      
      return false;
    } catch (e) {
      return false;
    }
  }

  // Auto refresh token if needed
  Future<bool> refreshTokenIfNeeded() async {
    try {
      final auth = HiveService.getAuth();
      if (auth == null) return false;
      
      // Check if token will expire in next 5 minutes
      final expiryDate = auth.createdAt.add(Duration(seconds: auth.expiresIn));
      final fiveMinutesFromNow = DateTime.now().add(Duration(minutes: 5));
      
      if (expiryDate.isBefore(fiveMinutesFromNow)) {
        print('DEBUG: Token will expire soon, attempting refresh...');
        final isConnected = await _connectivityService.checkConnectivity();
        
        if (isConnected) {
          // Try to refresh token by calling a protected endpoint
          try {
            final response = await _apiService.getProfile();
            if (response['success'] == true) {
              print('DEBUG: Token refresh successful');
              return true;
            }
          } catch (e) {
            print('DEBUG: Token refresh failed: $e');
            // If refresh fails, clear auth and force re-login
            await HiveService.clearAuth();
            return false;
          }
        }
      }
      
      return true;
    } catch (e) {
      print('DEBUG: Error in refreshTokenIfNeeded: $e');
      return false;
    }
  }
} 