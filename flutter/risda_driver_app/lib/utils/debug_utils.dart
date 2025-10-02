import 'package:flutter/material.dart';
import '../services/hive_service.dart';
import '../theme/text_styles.dart';
import '../theme/pastel_colors.dart';
import '../services/hive_service.dart';
import '../models/driver_log_model.dart';

void showHiveDataDialog(BuildContext context) {
  final driverLogs = HiveService.getDriverLogs();
  final syncQueue = HiveService.getSyncQueue();
  final auth = HiveService.getAuth();
  final user = HiveService.getUser();
  final programs = HiveService.getPrograms();
  final vehicles = HiveService.getVehicles();

  showDialog(
    context: context,
    builder: (context) => AlertDialog(
      title: Text('Hive Storage Data', style: AppTextStyles.h2),
      content: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisSize: MainAxisSize.min,
          children: [
            _buildDataSection('Auth', auth != null ? 'Logged in: ${auth.isLoggedIn}' : 'No auth'),
            _buildDataSection('User', user != null ? 'User: ${user.name}' : 'No user'),
            _buildDataSection('Programs', '${programs.length} programs'),
            _buildDataSection('Vehicles', '${vehicles.length} vehicles'),
            _buildDataSection('Driver Logs', '${driverLogs.length} logs'),
            _buildDataSection('Sync Queue', '${syncQueue.length} items'),
            
            if (driverLogs.isNotEmpty) ...[
              const SizedBox(height: 16),
              Text('Driver Logs Details:', style: AppTextStyles.h3),
              const SizedBox(height: 8),
              ...driverLogs.map((log) => Container(
                margin: EdgeInsets.only(bottom: 8),
                padding: EdgeInsets.all(8),
                decoration: BoxDecoration(
                  border: Border.all(color: PastelColors.border),
                  borderRadius: BorderRadius.circular(4),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('ID: ${log.id ?? 'Local'}', style: AppTextStyles.bodySmall),
                    Text('Program: ${log.programNama ?? 'N/A'}', style: AppTextStyles.bodySmall),
                    Text('Vehicle: ${log.kenderaanNoPlat ?? 'N/A'}', style: AppTextStyles.bodySmall),
                    Text('Status: ${log.status}', style: AppTextStyles.bodySmall),
                    Text('Synced: ${log.isSynced}', style: AppTextStyles.bodySmall),
                    Text('Check-in: ${log.checkinTime?.toString() ?? 'N/A'}', style: AppTextStyles.bodySmall),
                    if (log.checkoutTime != null)
                      Text('Check-out: ${log.checkoutTime.toString()}', style: AppTextStyles.bodySmall),
                  ],
                ),
              )).toList(),
            ],
            
            if (syncQueue.isNotEmpty) ...[
              const SizedBox(height: 16),
              Text('Sync Queue Details:', style: AppTextStyles.h3),
              const SizedBox(height: 8),
              ...syncQueue.map((item) => Container(
                margin: EdgeInsets.only(bottom: 8),
                padding: EdgeInsets.all(8),
                decoration: BoxDecoration(
                  border: Border.all(color: PastelColors.border),
                  borderRadius: BorderRadius.circular(4),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('ID: ${item.id}', style: AppTextStyles.bodySmall),
                    Text('Method: ${item.method}', style: AppTextStyles.bodySmall),
                    Text('Endpoint: ${item.endpoint}', style: AppTextStyles.bodySmall),
                    Text('Processing: ${item.isProcessing}', style: AppTextStyles.bodySmall),
                    Text('Retry Count: ${item.retryCount}', style: AppTextStyles.bodySmall),
                    if (item.errorMessage != null)
                      Text('Error: ${item.errorMessage}', style: AppTextStyles.bodySmall),
                  ],
                ),
              )).toList(),
            ],
          ],
        ),
      ),
      actions: [
        TextButton(
          onPressed: () => Navigator.of(context).pop(),
          child: Text('Close'),
        ),
      ],
    ),
  );
}

Widget _buildDataSection(String title, String data) {
  return Container(
    margin: EdgeInsets.only(bottom: 8),
    child: Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(title, style: AppTextStyles.bodyMedium.copyWith(fontWeight: FontWeight.bold)),
        Text(data, style: AppTextStyles.bodySmall),
      ],
    ),
  );
} 

class DebugUtils {
  static void checkCheckoutDataInHive() {
    print('🔍 === CHECKOUT DATA IN HIVE ANALYSIS ===');
    
    final driverLogs = HiveService.getDriverLogs();
    print('📊 Total logs in Hive: ${driverLogs.length}');
    
    if (driverLogs.isEmpty) {
      print('❌ No logs found in Hive');
      return;
    }
    
    // Categorize logs
    List<DriverLog> activeLogs = [];
    List<DriverLog> completedLogs = [];
    List<DriverLog> unsyncedLogs = [];
    
    for (var log in driverLogs) {
      if (log.isActive) {
        activeLogs.add(log);
      } else if (log.isCompleted) {
        completedLogs.add(log);
      }
      
      if (!log.isSynced) {
        unsyncedLogs.add(log);
      }
    }
    
    print('\n📋 === LOG CATEGORIES ===');
    print('🟢 Active logs: ${activeLogs.length}');
    print('✅ Completed logs: ${completedLogs.length}');
    print('🔄 Unsynced logs: ${unsyncedLogs.length}');
    
    // Check active logs
    if (activeLogs.isNotEmpty) {
      print('\n🟢 === ACTIVE LOGS ===');
      for (var log in activeLogs) {
        print('ID: ${log.id}');
        print('  Status: ${log.status}');
        print('  Check-in: ${log.checkinTime?.toString() ?? 'N/A'}');
        print('  Check-out: ${log.checkoutTime}');
        print('  Synced: ${log.isSynced}');
        print('  ---');
      }
    }
    
    // Check completed logs with checkout data
    if (completedLogs.isNotEmpty) {
      print('\n✅ === COMPLETED LOGS WITH CHECKOUT DATA ===');
      for (var log in completedLogs) {
        print('ID: ${log.id}');
        print('  Status: ${log.status}');
        print('  Check-in: ${log.checkinTime}');
        print('  Check-out: ${log.checkoutTime}');
        print('  Odometer Start: ${log.bacaanOdometer}');
        print('  Odometer End: ${log.bacaanOdometerCheckout}');
        print('  Jarak Perjalanan: ${log.jarakPerjalanan}');
        print('  Synced: ${log.isSynced}');
        print('  ---');
      }
    }
    
    // Check unsynced logs
    if (unsyncedLogs.isNotEmpty) {
      print('\n🔄 === UNSYNCED LOGS ===');
      for (var log in unsyncedLogs) {
        print('ID: ${log.id}');
        print('  Status: ${log.status}');
        print('  Check-in: ${log.checkinTime}');
        print('  Check-out: ${log.checkoutTime}');
        print('  Synced: ${log.isSynced}');
        print('  ---');
      }
    }
    
    // Check for checkout data specifically
    print('\n🔍 === CHECKOUT DATA ANALYSIS ===');
    List<DriverLog> logsWithCheckout = driverLogs.where((log) => log.checkoutTime != null).toList();
    print('📸 Logs with checkout time: ${logsWithCheckout.length}');
    
    if (logsWithCheckout.isNotEmpty) {
      for (var log in logsWithCheckout) {
        print('ID: ${log.id}');
        print('  Checkout Time: ${log.checkoutTime}');
        print('  Checkout Odometer: ${log.bacaanOdometerCheckout}');
        print('  Checkout Location: ${log.lokasiCheckout}');
        print('  Checkout Photo: ${log.odometerPhotoCheckout != null ? "Yes" : "No"}');
        print('  Synced: ${log.isSynced}');
        print('  ---');
      }
    }
    
    // Check sync queue
    print('\n📦 === SYNC QUEUE STATUS ===');
    final syncQueue = HiveService.getSyncQueue();
    print('Queue items: ${syncQueue.length}');
    
    if (syncQueue.isNotEmpty) {
      for (var item in syncQueue) {
        print('ID: ${item.id}');
        print('  Endpoint: ${item.endpoint}');
        print('  Method: ${item.method}');
        print('  Retry Count: ${item.retryCount}');
        print('  ---');
      }
    }
    
    print('🔍 === END OF ANALYSIS ===\n');
  }

  // Simple function to check user's offline case
  static void checkUserOfflineCase() {
    print('🔍 === USER OFFLINE CASE CHECK ===');
    
    final driverLogs = HiveService.getDriverLogs();
    print('📊 Total logs in Hive: ${driverLogs.length}');
    
    if (driverLogs.isEmpty) {
      print('❌ No logs found in Hive');
      return;
    }
    
    // Find the most recent logs (should be the offline case)
    final sortedLogs = List<DriverLog>.from(driverLogs);
    sortedLogs.sort((a, b) => b.createdAt.compareTo(a.createdAt));
    
    print('\n📋 === MOST RECENT LOGS (Your Offline Case) ===');
    for (int i = 0; i < sortedLogs.length && i < 5; i++) {
      final log = sortedLogs[i];
      print('Log ${i + 1}:');
      print('  ID: ${log.id ?? 'Local'}');
      print('  Status: ${log.status}');
      print('  Check-in: ${log.checkinTime?.toString() ?? 'N/A'}');
      print('  Check-out: ${log.checkoutTime ?? 'Not checked out'}');
      print('  Odometer Start: ${log.bacaanOdometer}');
      print('  Odometer End: ${log.bacaanOdometerCheckout}');
      print('  Jarak Perjalanan: ${log.jarakPerjalanan}');
      print('  Synced: ${log.isSynced}');
      print('  Program: ${log.programNama}');
      print('  Vehicle: ${log.kenderaanNoPlat}');
      print('  ---');
    }
    
    // Check sync queue for this case
    print('\n📦 === SYNC QUEUE FOR OFFLINE CASE ===');
    final syncQueue = HiveService.getSyncQueue();
    print('Queue items: ${syncQueue.length}');
    
    if (syncQueue.isNotEmpty) {
      for (var item in syncQueue) {
        print('Queue Item:');
        print('  ID: ${item.id}');
        print('  Endpoint: ${item.endpoint}');
        print('  Method: ${item.method}');
        print('  Data: ${item.data}');
        print('  ---');
      }
    }
    
    print('🔍 === END OF USER CASE CHECK ===\n');
  }
} 