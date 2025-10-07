import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
 
import 'api_service.dart';
import '../core/api_client.dart';

// Top-level function to handle background messages
@pragma('vm:entry-point')
Future<void> _firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  await Firebase.initializeApp();
  
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

      

      if (settings.authorizationStatus == AuthorizationStatus.authorized ||
          settings.authorizationStatus == AuthorizationStatus.provisional) {
        
        // Initialize local notifications
        await _initializeLocalNotifications();

        // Get FCM token
        _fcmToken = await _firebaseMessaging.getToken();
        

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
          
          _fcmToken = newToken;
          _registerTokenWithBackend(newToken);
        });

        
      } else {
        
      }
    } catch (e) {
      
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
      
    } catch (e) {
      
    }
  }

  /// Handle foreground messages (show local notification)
  void _handleForegroundMessage(RemoteMessage message) {
    

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
    
    
    String? type = message.data['type'];
    
    // TODO: Navigate based on notification type
    switch (type) {
      case 'claim_approved':
      case 'claim_rejected':
        // Navigate to claim detail
        int? claimId = int.tryParse(message.data['claim_id']?.toString() ?? '');
        if (claimId != null) {
          
          // navigationService.navigateTo('/claim/$claimId');
        }
        break;
      case 'program_assigned':
        // Navigate to program detail
        int? programId = int.tryParse(message.data['program_id']?.toString() ?? '');
        if (programId != null) {
          
          // navigationService.navigateTo('/program/$programId');
        }
        break;
      case 'support_reply':
      case 'support_ticket':
      case 'support_assigned':
      case 'support_escalated':
      case 'support_closed':
        // Navigate to support ticket detail
        int? ticketId = int.tryParse(message.data['ticket_id']?.toString() ?? '');
        String? ticketNumber = message.data['ticket_number']?.toString();
        if (ticketId != null) {
          
          // TODO: Navigate to SupportTicketDetailScreen
          // navigationService.navigateTo('/support/tickets/$ticketId');
        }
        break;
      default:
        
    }
  }

  /// Remove FCM token (on logout)
  Future<void> removeToken() async {
    if (_fcmToken != null) {
      try {
        await _apiService.removeFcmToken(_fcmToken!);
        await _firebaseMessaging.deleteToken();
        _fcmToken = null;
        
      } catch (e) {
        
      }
    }
  }
}

