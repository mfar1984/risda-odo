import 'dart:async';
import 'package:connectivity_plus/connectivity_plus.dart';
import 'package:internet_connection_checker/internet_connection_checker.dart';

class ConnectivityService {
  final Connectivity _connectivity = Connectivity();
  final InternetConnectionChecker _connectionChecker = InternetConnectionChecker();
  
  // Stream to broadcast connectivity status
  final _connectivityController = StreamController<bool>.broadcast();
  Stream<bool> get connectivityStream => _connectivityController.stream;

  // Current connectivity status
  bool _isConnected = false;
  bool get isConnected => _isConnected;

  ConnectivityService() {
    // Initialize connectivity status
    _checkConnectivity();
    
    // Listen for connectivity changes
    _connectivity.onConnectivityChanged.listen((_) {
      _checkConnectivity();
    });
  }

  // Check connectivity and update status
  Future<void> _checkConnectivity() async {
    bool previousStatus = _isConnected;
    _isConnected = await _connectionChecker.hasConnection;
    
    // Only broadcast if status changed
    if (previousStatus != _isConnected) {
      _connectivityController.add(_isConnected);
    }
  }

  // Check connectivity on demand
  Future<bool> checkConnectivity() async {
    _isConnected = await _connectionChecker.hasConnection;
    return _isConnected;
  }

  // Dispose resources
  void dispose() {
    _connectivityController.close();
  }
} 