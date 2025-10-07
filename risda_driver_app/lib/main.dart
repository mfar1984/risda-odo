import 'package:flutter/material.dart';
import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:provider/provider.dart';
import 'package:firebase_core/firebase_core.dart';
import 'core/api_client.dart';
import 'services/api_service.dart';
import 'services/auth_service.dart';
import 'services/hive_service.dart';
import 'services/firebase_service.dart';
import 'services/connectivity_service.dart';
import 'services/sync_service.dart';
import 'repositories/driver_log_repository.dart';
import 'screens/splash_screen.dart';
import 'screens/login_screen.dart';
import 'screens/dashboard_screen.dart';

Future<void> main() async {
  // Ensure Flutter is initialized
  WidgetsFlutterBinding.ensureInitialized();
  
  // Initialize Firebase (skip on web for now - web FCM needs different setup)
  if (!kIsWeb) {
    try {
      await Firebase.initializeApp(
        options: const FirebaseOptions(
          apiKey: 'AIzaSyBp2C6KN_C8J3YKsFHvHt6kI20YW7kJYvY',
          appId: '1:664926126437:android:abc123',
          messagingSenderId: '664926126437',
          projectId: 'jara-risda',
          storageBucket: 'jara-risda.firebasestorage.app',
        ),
      );
      
      // Initialize Firebase Cloud Messaging (mobile only)
      await FirebaseService().initialize();
    } catch (e) {}
  }
  
  // Initialize Hive (offline storage)
  await HiveService.init();
  
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    // Initialize services
    final apiClient = ApiClient();
    final apiService = ApiService(apiClient);
    final driverLogRepository = DriverLogRepository(apiService);
    final connectivityService = ConnectivityService();
    final syncService = SyncService(apiService, connectivityService);
    final authService = AuthService();
    
    // Inject sync service into auth service (for post-login sync)
    authService.setSyncService(syncService);

    return MultiProvider(
      providers: [
        Provider<ApiClient>.value(value: apiClient),
        Provider<ApiService>.value(value: apiService),
        ChangeNotifierProvider<AuthService>.value(value: authService),
        ChangeNotifierProvider<ConnectivityService>.value(value: connectivityService),
        ChangeNotifierProvider<SyncService>.value(value: syncService),
        Provider<DriverLogRepository>.value(value: driverLogRepository),
      ],
      child: MaterialApp(
        title: 'RISDA Driver App',
        debugShowCheckedModeBanner: false,
        theme: ThemeData(
          colorScheme: ColorScheme.fromSeed(
            seedColor: const Color(0xFF2E7D32), // RISDA green
            primary: const Color(0xFF2E7D32),
          ),
          useMaterial3: true,
          appBarTheme: const AppBarTheme(
            centerTitle: true,
            elevation: 0,
            backgroundColor: Color(0xFF2E7D32),
            foregroundColor: Colors.white,
          ),
          cardTheme: CardThemeData(
            elevation: 2,
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(12),
            ),
          ),
          elevatedButtonTheme: ElevatedButtonThemeData(
            style: ElevatedButton.styleFrom(
              padding: const EdgeInsets.symmetric(
                horizontal: 24,
                vertical: 12,
              ),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(8),
              ),
            ),
          ),
        ),
        home: const SplashScreen(),
        routes: {
          '/login': (context) => const LoginScreen(),
          '/dashboard': (context) => const DashboardScreen(),
        },
      ),
    );
  }
}