import 'package:dio/dio.dart';
import 'constants.dart';

class ApiClient {
  late Dio _dio;
  String? _authToken;

  // Singleton pattern
  static final ApiClient _instance = ApiClient._internal();
  factory ApiClient() => _instance;

  ApiClient._internal() {
    _dio = Dio(BaseOptions(
      baseUrl: ApiConstants.baseUrl,
      connectTimeout: ApiConstants.connectTimeout,
      receiveTimeout: ApiConstants.receiveTimeout,
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-API-Key': ApiConstants.apiKey, // Global API Key
        // Note: 'Origin' header is automatically set by the browser and cannot be manually set
      },
    ));

    // Add interceptors for logging and auth
    _dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) {
        // Add auth token if available (Sanctum Bearer token)
        if (_authToken != null) {
          options.headers['Authorization'] = 'Bearer $_authToken';
        }
        
        return handler.next(options);
      },
      onResponse: (response, handler) {
        return handler.next(response);
      },
      onError: (error, handler) {
        return handler.next(error);
      },
    ));
  }

  /// Set authentication token
  void setAuthToken(String? token) {
    _authToken = token;
  }

  /// Get Dio instance
  Dio get dio => _dio;

  /// Clear auth token
  void clearAuthToken() {
    _authToken = null;
  }
}
