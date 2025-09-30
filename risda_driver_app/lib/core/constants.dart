/// API Configuration
class ApiConstants {
  // Base URL - Update this to your Laravel backend URL
  static const String baseUrl = 'http://localhost:8000/api';
  
  // API Endpoints
  static const String login = '/auth/login';
  static const String logout = '/auth/logout';
  static const String profile = '/auth/profile';
  static const String updateProfile = '/auth/update-profile';
  
  static const String activePrograms = '/programs/active';
  static const String vehicles = '/vehicles';
  
  static const String activeTrip = '/logs/active';
  static const String startTrip = '/logs/start';
  static String endTrip(int logId) => '/logs/$logId/end';
  
  // Timeouts
  static const Duration connectTimeout = Duration(seconds: 10);
  static const Duration receiveTimeout = Duration(seconds: 10);
}

/// App Configuration
class AppConstants {
  static const String appName = 'RISDA Driver App';
  static const String appVersion = '2.0.0';
  
  // Storage Keys
  static const String tokenKey = 'auth_token';
  static const String userKey = 'user_data';
  static const String isLoggedInKey = 'is_logged_in';
}
