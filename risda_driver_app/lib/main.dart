import 'package:flutter/material.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'services/hive_service.dart';
import 'services/api_service.dart';
import 'services/connectivity_service.dart';
import 'repositories/auth_repository.dart';
import 'repositories/driver_log_repository.dart';
import 'utils/sync_manager.dart';
import 'screens/splash_screen.dart';
import 'theme/pastel_colors.dart';
import 'theme/text_styles.dart';

// Global instances for easy access
late ApiService apiService;
late ConnectivityService connectivityService;
late AuthRepository authRepository;
late DriverLogRepository driverLogRepository;
late SyncManager syncManager;

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  
  try {
    // Initialize Hive
    await HiveService.init();
    
    // Jalankan audit dan pembetulan menyeluruh pada semua log
    print('DEBUG: Running comprehensive audit and fix of all logs');
    final auditResult = await HiveService.auditAndFixAllLogs();
    if (auditResult['success'] == true) {
      print('DEBUG: Audit completed successfully');
      print('DEBUG: Fixed ${auditResult['fixedActiveCount']} active logs');
      print('DEBUG: Removed ${auditResult['removedDuplicateCount']} duplicate logs');
      print('DEBUG: Fixed ${auditResult['fixedInconsistentCount']} inconsistent logs');
      print('DEBUG: Remaining active logs: ${auditResult['remainingActiveCount']}');
    } else {
      print('DEBUG: Audit failed: ${auditResult['error']}');
    }
    
    // Bersihkan log yang tidak konsisten secara automatik
    print('DEBUG: Running automatic cleanup of inconsistent logs');
    await HiveService.cleanupInconsistentActiveLogs();
    print('DEBUG: Automatic cleanup completed');
  } catch (e) {
    print('DEBUG: Error during Hive initialization: $e');
    print('DEBUG: Continuing with app startup despite Hive error');
    // Jangan hentikan aplikasi jika ada ralat Hive
  }
  
  // Initialize services
  apiService = ApiService();
  connectivityService = ConnectivityService();
  
  // Initialize repositories
  authRepository = AuthRepository(
    apiService: apiService,
    connectivityService: connectivityService,
  );
  
  driverLogRepository = DriverLogRepository(
    apiService: apiService,
    connectivityService: connectivityService,
  );
  
  // Initialize sync manager
  syncManager = SyncManager(
    connectivityService: ConnectivityService(),
    driverLogRepository: DriverLogRepository(
      apiService: ApiService(),
      connectivityService: ConnectivityService(),
    ),
  );
  
  // Start periodic sync
  syncManager.startPeriodicSync();
  
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'JARA',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        primarySwatch: Colors.blue,
        visualDensity: VisualDensity.adaptivePlatformDensity,
      ),
      home: const SplashScreen(),
    );
  }
}