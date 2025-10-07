import 'dart:async';
import 'package:flutter/foundation.dart';
import 'package:flutter/widgets.dart';
import 'package:connectivity_plus/connectivity_plus.dart';
import 'package:dio/dio.dart';
import '../core/constants.dart';
import 'dart:developer' as developer;

/// Service to monitor internet connectivity status
/// Provides real-time online/offline status and triggers auto-sync
class ConnectivityService extends ChangeNotifier with WidgetsBindingObserver {
  // Connectivity instance
  final Connectivity _connectivity = Connectivity();
  StreamSubscription<ConnectivityResult>? _connectivitySubscription;
  Timer? _periodicCheckTimer;
  Timer? _connectivityDebounceTimer;
  bool _initialized = false;
  
  // Connection status
  bool _isOnline = true;
  bool _isChecking = false;
  DateTime? _lastChecked;
  DateTime? _lastOnlineTime;
  DateTime? _lastOfflineTime;
  
  // Callbacks
  final List<VoidCallback> _onOnlineCallbacks = [];
  final List<VoidCallback> _onOfflineCallbacks = [];
  
  // Getters
  bool get isOnline => _isOnline;
  bool get isOffline => !_isOnline;
  bool get isChecking => _isChecking;
  DateTime? get lastChecked => _lastChecked;
  
  /// Initialize connectivity monitoring
  Future<void> initialize() async {
    // Make idempotent to avoid duplicate timers/subscriptions
    if (_initialized) {
      developer.log('ℹ️ ConnectivityService already initialized');
      return;
    }
    // Observe app lifecycle to re-check on resume
    WidgetsBinding.instance.addObserver(this);
    // Initial check
    await checkConnection();
    
    // Start monitoring
    startMonitoring();
    
    // Start periodic server ping (every 15 seconds)
    _startPeriodicCheck();
    _initialized = true;
    
    developer.log('✅ ConnectivityService initialized');
  }
  
  /// Start monitoring connectivity changes
  void startMonitoring() {
    _connectivitySubscription = _connectivity.onConnectivityChanged.listen(
      _handleConnectivityChange,
      onError: (error) {
        developer.log('❌ Connectivity stream error: $error');
      },
    );
    
    developer.log('📡 Connectivity monitoring started');
  }
  
  /// Start periodic server ping (detects server down even if WiFi on)
  void _startPeriodicCheck() {
    _periodicCheckTimer?.cancel();
    
    _periodicCheckTimer = Timer.periodic(const Duration(seconds: 15), (timer) {
      developer.log('🔍 Periodic server check...');
      checkConnection();
    });
    
    developer.log('⏰ Periodic server check started (every 15s)');
  }
  
  /// Handle connectivity change event
  Future<void> _handleConnectivityChange(ConnectivityResult result) async {
    developer.log('📶 Connectivity changed: $result');
    
    // Check if connection is available
    final hasConnection = result != ConnectivityResult.none;
    
    if (hasConnection) {
      // Network interface available - verify server reachability
      developer.log('📡 Network interface available - verifying server...');
      // Debounce slightly to allow interface to settle (IP/DNS)
      _connectivityDebounceTimer?.cancel();
      _connectivityDebounceTimer = Timer(const Duration(milliseconds: 800), () {
        checkConnection(); // Will trigger callbacks via _updateStatus()
      });
    } else {
      // No network interface - definitely offline
      developer.log('⚠️ No network interface');
      _updateStatus(false);  // Will trigger callbacks via _updateStatus()
    }
  }
  
  /// Check actual internet connectivity by pinging server
  /// Uses generous timeout (10s) to handle slow connections
  /// Retries once if timeout to differentiate slow vs down
  Future<bool> checkConnection() async {
    if (_isChecking) return _isOnline;
    
    _isChecking = true;
    notifyListeners();
    
    try {
      // Try to ping the server API with generous timeout
      final dio = Dio(BaseOptions(
        connectTimeout: const Duration(seconds: 10),  // Generous for slow internet
        receiveTimeout: const Duration(seconds: 10),
      ));
      
      final response = await dio.get(
        '${ApiConstants.serverUrl}/api/ping',  // Health check endpoint
        options: Options(
          headers: {'Accept': 'application/json'},
          validateStatus: (status) => status != null && status < 500,
        ),
      );
      
      final isConnected = response.statusCode == 200 || response.statusCode == 401;
      // 200 = OK, 401 = Unauthorized (but server is reachable)
      
      _updateStatus(isConnected);
      _lastChecked = DateTime.now();
      
      developer.log(isConnected ? '✅ Server reachable' : '❌ Server not reachable');
      
      return isConnected;
    } catch (e) {
      // First attempt failed - retry once (might be slow connection)
      developer.log('⚠️ First ping failed, retrying... ($e)');
      
      try {
        await Future.delayed(const Duration(seconds: 2));  // Wait 2s before retry
        
        final dio = Dio(BaseOptions(
          connectTimeout: const Duration(seconds: 15),  // Even more generous on retry
          receiveTimeout: const Duration(seconds: 15),
        ));
        
        final response = await dio.get(
          '${ApiConstants.serverUrl}/api/ping',
          options: Options(
            headers: {'Accept': 'application/json'},
            validateStatus: (status) => status != null && status < 500,
          ),
        );
        
        final isConnected = response.statusCode == 200 || response.statusCode == 401;
        _updateStatus(isConnected);
        _lastChecked = DateTime.now();
        
        developer.log(isConnected ? '✅ Server reachable (after retry)' : '❌ Server not reachable');
        return isConnected;
        
      } catch (retryError) {
        // Both attempts failed - truly offline
        developer.log('❌ Connection check failed (after retry): $retryError');
        _updateStatus(false);
        _lastChecked = DateTime.now();
        return false;
      }
    } finally {
      _isChecking = false;
      notifyListeners();
    }
  }
  
  /// Update connection status
  void _updateStatus(bool isOnline) {
    final wasOnline = _isOnline;
    
    developer.log('🔄 _updateStatus called: wasOnline=$wasOnline, newStatus=$isOnline');
    
    // Update status FIRST
    _isOnline = isOnline;
    
    if (isOnline) {
      _lastOnlineTime = DateTime.now();
    } else {
      _lastOfflineTime = DateTime.now();
    }
    
    // Notify UI FIRST so indicator updates immediately
    developer.log('📡 Calling notifyListeners() - should trigger Consumer rebuild');
    notifyListeners();
    developer.log('✅ notifyListeners() completed');
    
    // THEN trigger callbacks (may kick off async sync work)
    if (wasOnline != isOnline) {
      developer.log('📶 Status changed: ${wasOnline ? "ONLINE" : "OFFLINE"} → ${isOnline ? "ONLINE" : "OFFLINE"}');
      if (isOnline) {
        _onBackOnline();
      } else {
        _onBackOffline();
      }
    } else {
      developer.log('   (Status unchanged: ${isOnline ? "ONLINE" : "OFFLINE"})');
    }
  }
  
  /// Called when connection is restored
  void _onBackOnline() {
    developer.log('🟢 Back online - triggering callbacks...');
    
    // Trigger all registered callbacks
    for (var callback in _onOnlineCallbacks) {
      try {
        callback();
      } catch (e) {
        developer.log('❌ Online callback error: $e');
      }
    }
  }
  
  /// Called when connection is lost
  void _onBackOffline() {
    developer.log('🔴 Now offline - triggering callbacks...');
    
    // Trigger all registered callbacks
    for (var callback in _onOfflineCallbacks) {
      try {
        callback();
      } catch (e) {
        developer.log('❌ Offline callback error: $e');
      }
    }
  }
  
  /// Register callback when back online
  void onBackOnline(VoidCallback callback) {
    _onOnlineCallbacks.add(callback);
  }
  
  /// Register callback when going offline
  void onBackOffline(VoidCallback callback) {
    _onOfflineCallbacks.add(callback);
  }
  
  /// Get offline duration (if currently offline)
  Duration? getOfflineDuration() {
    if (_isOnline || _lastOfflineTime == null) return null;
    return DateTime.now().difference(_lastOfflineTime!);
  }
  
  /// Get status text for UI
  String getStatusText() {
    if (_isChecking) return 'Checking connection...';
    if (_isOnline) return 'Online';
    
    final duration = getOfflineDuration();
    if (duration == null) return 'Offline';
    
    if (duration.inMinutes < 1) return 'Offline (just now)';
    if (duration.inHours < 1) return 'Offline (${duration.inMinutes}m)';
    if (duration.inDays < 1) return 'Offline (${duration.inHours}h)';
    return 'Offline (${duration.inDays}d)';
  }
  
  /// Dispose (cleanup)
  @override
  void dispose() {
    _connectivitySubscription?.cancel();
    _periodicCheckTimer?.cancel();
    _connectivityDebounceTimer?.cancel();
    _onOnlineCallbacks.clear();
    _onOfflineCallbacks.clear();
    WidgetsBinding.instance.removeObserver(this);
    super.dispose();
  }

  // Re-check connectivity whenever the app returns to foreground
  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    if (state == AppLifecycleState.resumed) {
      developer.log('🔁 App resumed - rechecking connectivity');
      checkConnection();
    }
  }
}

