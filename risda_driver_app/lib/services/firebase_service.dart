import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'dart:developer' as developer;
import 'api_service.dart';
import '../core/api_client.dart';

// Top-level function to handle background messages
@pragma('vm:entry-point')
Future<void> _firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  await Firebase.initializeApp();
  developer.log('Background message received: ${message.messageId}');
}

class FirebaseService {
  static final FirebaseService _instance = FirebaseService._internal();
  factory FirebaseService() => _instance;
  FirebaseService._internal();

  final FirebaseMessaging _firebaseMessaging = FirebaseMessaging.instance;
  final FlutterLocalNotificationsPlugin _localNotifications = FlutterLocalNotificationsPlugin();
  final ApiService _apiService = ApiService(ApiClient());

  String? _fcmToken;
  String? get fcmToken => _fcmToken;

  /// Initialize Firebase and FCM
  Future<void> initialize() async {
    try {
      // Request permission for iOS
      NotificationSettings settings = await _firebaseMessaging.requestPermission(
        alert: true,
        badge: true,
        sound: true,
        provisional: false,
      );

      developer.log('FCM Permission status: ${settings.authorizationStatus}');

      if (settings.authorizationStatus == AuthorizationStatus.authorized ||
          settings.authorizationStatus == AuthorizationStatus.provisional) {
        
        // Initialize local notifications
        await _initializeLocalNotifications();

        // Get FCM token
        _fcmToken = await _firebaseMessaging.getToken();
        developer.log('FCM Token: $_fcmToken');

        // Register token with backend
        if (_fcmToken != null) {
          await _registerTokenWithBackend(_fcmToken!);
        }

        // Handle foreground messages
        FirebaseMessaging.onMessage.listen(_handleForegroundMessage);

        // Handle background messages
        FirebaseMessaging.onBackgroundMessage(_firebaseMessagingBackgroundHandler);

        // Handle notification taps (when app is in background/terminated)
        FirebaseMessaging.onMessageOpenedApp.listen(_handleNotificationTap);

        // Check if app was opened from a notification (when terminated)
        RemoteMessage? initialMessage = await _firebaseMessaging.getInitialMessage();
        if (initialMessage != null) {
          _handleNotificationTap(initialMessage);
        }

        // Listen for token refresh
        _firebaseMessaging.onTokenRefresh.listen((newToken) {
          developer.log('FCM Token refreshed: $newToken');
          _fcmToken = newToken;
          _registerTokenWithBackend(newToken);
        });

        developer.log('Firebase initialized successfully');
      } else {
        developer.log('FCM permission denied');
      }
    } catch (e) {
      developer.log('Firebase initialization error: $e');
    }
  }

  /// Initialize local notifications (for foreground)
  Future<void> _initializeLocalNotifications() async {
    const AndroidInitializationSettings androidSettings = AndroidInitializationSettings('@mipmap/ic_launcher');
    
    const DarwinInitializationSettings iosSettings = DarwinInitializationSettings(
      requestAlertPermission: true,
      requestBadgePermission: true,
      requestSoundPermission: true,
    );

    const InitializationSettings initSettings = InitializationSettings(
      android: androidSettings,
      iOS: iosSettings,
    );

    await _localNotifications.initialize(
      initSettings,
      onDidReceiveNotificationResponse: (NotificationResponse response) {
        developer.log('Local notification tapped: ${response.payload}');
        // TODO: Navigate to specific screen based on payload
      },
    );

    // Create Android notification channel
    const AndroidNotificationChannel channel = AndroidNotificationChannel(
      'high_importance_channel', // id
      'High Importance Notifications', // name
      description: 'This channel is used for important notifications.',
      importance: Importance.high,
    );

    await _localNotifications
        .resolvePlatformSpecificImplementation<AndroidFlutterLocalNotificationsPlugin>()
        ?.createNotificationChannel(channel);
  }

  /// Register FCM token with backend
  Future<void> _registerTokenWithBackend(String token) async {
    try {
      await _apiService.registerFcmToken(token);
      developer.log('FCM token registered with backend');
    } catch (e) {
      developer.log('Failed to register FCM token with backend: $e');
    }
  }

  /// Handle foreground messages (show local notification)
  void _handleForegroundMessage(RemoteMessage message) {
    developer.log('Foreground message received: ${message.notification?.title}');

    RemoteNotification? notification = message.notification;
    AndroidNotification? android = message.notification?.android;

    if (notification != null) {
      _localNotifications.show(
        notification.hashCode,
        notification.title,
        notification.body,
        const NotificationDetails(
          android: AndroidNotificationDetails(
            'high_importance_channel',
            'High Importance Notifications',
            channelDescription: 'This channel is used for important notifications.',
            importance: Importance.high,
            priority: Priority.high,
            icon: '@mipmap/ic_launcher',
          ),
          iOS: DarwinNotificationDetails(),
        ),
        payload: message.data.toString(),
      );
    }
  }

  /// Handle notification tap (navigate to specific screen)
  void _handleNotificationTap(RemoteMessage message) {
    developer.log('Notification tapped: ${message.data}');
    
    String? type = message.data['type'];
    
    // TODO: Navigate based on notification type
    switch (type) {
      case 'claim_approved':
      case 'claim_rejected':
        // Navigate to claim detail
        int? claimId = int.tryParse(message.data['claim_id']?.toString() ?? '');
        if (claimId != null) {
          developer.log('Navigate to claim detail: $claimId');
          // navigationService.navigateTo('/claim/$claimId');
        }
        break;
      case 'program_assigned':
        // Navigate to program detail
        int? programId = int.tryParse(message.data['program_id']?.toString() ?? '');
        if (programId != null) {
          developer.log('Navigate to program detail: $programId');
          // navigationService.navigateTo('/program/$programId');
        }
        break;
      default:
        developer.log('Unknown notification type: $type');
    }
  }

  /// Remove FCM token (on logout)
  Future<void> removeToken() async {
    if (_fcmToken != null) {
      try {
        await _apiService.removeFcmToken(_fcmToken!);
        await _firebaseMessaging.deleteToken();
        _fcmToken = null;
        developer.log('FCM token removed');
      } catch (e) {
        developer.log('Failed to remove FCM token: $e');
      }
    }
  }
}

