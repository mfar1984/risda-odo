/// API Configuration
class ApiConstants {
  // Base URL - UPDATE THIS WHEN DEPLOYING
  // For Development (Android Emulator): 'http://10.0.2.2:8000'
  // For Production: 'https://jara.my' or your domain
  static const String serverUrl = 'https://jara.my';
  
  // API Base URL
  static String get baseUrl => '$serverUrl/api';
  
  // Storage Base URL (for images/files)
  static String get storageUrl => '$serverUrl/storage';
  
  // Global API Key (from backend Integrasi config)
  static const String apiKey = 'rsk_xhitYqr9tsRDiyUHvr3keN9v82R7LxEvFbqPv5W1RuWMl2nlu1qvLEPmfsXcxdY4';
  
  // Origin (for CORS)
  static String get origin => serverUrl;
  
  // API Endpoints
  static const String login = '/auth/login';
  static const String logout = '/auth/logout';
  static const String logoutAll = '/auth/logout-all';
  static const String getUser = '/auth/user';
  
  static const String activePrograms = '/programs/active';
  static const String vehicles = '/vehicles';
  
  static const String activeTrip = '/logs/active';
  static const String startTrip = '/logs/start';
  static String endTrip(int logId) => '/logs/$logId/end';
  
  // Timeouts
  static const Duration connectTimeout = Duration(seconds: 10);
  static const Duration receiveTimeout = Duration(seconds: 10);
  
  /// Build full URL for storage files (images, receipts, etc)
  /// Returns full URL if path already starts with http/https
  /// Otherwise, prepends storageUrl
  static String buildStorageUrl(String? path) {
    if (path == null || path.isEmpty) return '';
    if (path.startsWith('http://') || path.startsWith('https://')) {
      return path; // Already full URL
    }
    return '$storageUrl/$path';
  }
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
