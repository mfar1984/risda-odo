import 'package:dio/dio.dart';
import 'constants.dart';
import 'dart:developer' as developer;

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
        developer.log('API Request: ${options.method} ${options.path}');
        
        // Add auth token if available (Sanctum Bearer token)
        if (_authToken != null) {
          options.headers['Authorization'] = 'Bearer $_authToken';
        }
        
        return handler.next(options);
      },
      onResponse: (response, handler) {
        developer.log('API Response: ${response.statusCode} ${response.requestOptions.path}');
        return handler.next(response);
      },
      onError: (error, handler) {
        developer.log('API Error: ${error.response?.statusCode} ${error.requestOptions.path}');
        developer.log('Error: ${error.message}');
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
