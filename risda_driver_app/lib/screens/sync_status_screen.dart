import 'package:flutter/material.dart';
import 'dart:async';
import '../theme/pastel_colors.dart';
import '../theme/text_styles.dart';
import '../main.dart';
import '../services/hive_service.dart';
import '../models/sync_queue_model.dart';
import '../services/connectivity_service.dart';

class SyncStatusScreen extends StatefulWidget {
  @override
  _SyncStatusScreenState createState() => _SyncStatusScreenState();
}

class _SyncStatusScreenState extends State<SyncStatusScreen> {
  bool _isLoading = false;
  bool _isOnline = false;
  String _lastSyncTime = 'Never';
  List<SyncQueueItem> _pendingItems = [];
  int _completedSyncs = 0;
  int _failedSyncs = 0;
  String _syncStatus = 'Idle';
  String _errorMessage = '';
  Timer? _refreshTimer;

  @override
  void initState() {
    super.initState();
    _checkConnectivity();
    _loadSyncData();
    
    // Set up timer to refresh data every 5 seconds
    _refreshTimer = Timer.periodic(Duration(seconds: 5), (timer) {
      if (mounted) {
        _loadSyncData();
      }
    });
  }

  @override
  void dispose() {
    _refreshTimer?.cancel();
    super.dispose();
  }

  Future<void> _checkConnectivity() async {
    final connectivityService = ConnectivityService();
    final isConnected = await connectivityService.checkConnectivity();
    setState(() {
      _isOnline = isConnected;
    });
  }

  Future<void> _loadSyncData() async {
    try {
      // Get pending sync items
      final pendingItems = HiveService.getSyncQueue();
      
      setState(() {
        _pendingItems = pendingItems;
      });
      
      // Get last sync time from preferences or use default
      // This would need to be implemented in a real app
      // For now, we'll just use a placeholder
      setState(() {
        _lastSyncTime = DateTime.now().toString().substring(0, 16);
      });
      
    } catch (e) {
      print('Error loading sync data: $e');
    }
  }

  Future<void> _triggerManualSync() async {
    if (!_isOnline) {
      setState(() {
        _errorMessage = 'Cannot sync in offline mode';
      });
      return;
    }
    
    setState(() {
      _isLoading = true;
      _syncStatus = 'Sync in progress...';
      _errorMessage = '';
    });
    
    try {
      final result = await syncManager.syncNow();
      
      setState(() {
        _isLoading = false;
        _syncStatus = 'Sync completed';
        _lastSyncTime = DateTime.now().toString().substring(0, 16);
        
        if (result != null && result['success'] == true) {
          _completedSyncs = result['successCount'] ?? 0;
          _failedSyncs = (result['totalCount'] ?? 0) - (result['successCount'] ?? 0);
        }
      });
      
      // Reload sync data after sync
      _loadSyncData();
      
    } catch (e) {
      setState(() {
        _isLoading = false;
        _syncStatus = 'Sync failed';
        _errorMessage = 'Error: $e';
      });
    }
  }
  
  Future<void> _resolveActiveTrips() async {
    // Show confirmation dialog
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: Text('Resolve Trip Conflict'),
        content: Text('This will force complete any active trips and allow new check-ins. Continue?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: Text('Cancel'),
          ),
          TextButton(
            onPressed: () => Navigator.pop(context, true),
            child: Text('Resolve'),
          ),
        ],
      ),
    );
    
    if (confirmed != true) return;
    
    setState(() {
      _isLoading = true;
      _syncStatus = 'Resolving active trips...';
      _errorMessage = '';
    });
    
    try {
      await HiveService.forceCompleteAllActiveTrips();
      
      setState(() {
        _isLoading = false;
        _syncStatus = 'Active trips resolved';
      });
      
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Trip conflict resolved! You can now check-in.'),
          backgroundColor: Colors.green,
          duration: Duration(seconds: 3),
        ),
      );
      
      // Reload sync data after resolve
      _loadSyncData();
      
    } catch (e) {
      setState(() {
        _isLoading = false;
        _syncStatus = 'Resolve failed';
        _errorMessage = 'Error: $e';
      });
      
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Error resolving conflict: $e'),
          backgroundColor: Colors.red,
          duration: Duration(seconds: 3),
        ),
      );
    }
  }
  
  Future<void> _ultimateNuclearClear() async {
    // Show confirmation dialog
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: Text('Ultimate Nuclear Clear'),
        content: Text('WARNING: This will clear all active trips and reset the sync state. Only completed trips will be preserved. This action cannot be undone. Continue?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: Text('Cancel'),
          ),
          TextButton(
            onPressed: () => Navigator.pop(context, true),
            style: TextButton.styleFrom(
              foregroundColor: Colors.red,
            ),
            child: Text('CLEAR ALL'),
          ),
        ],
      ),
    );
    
    if (confirmed != true) return;
    
    setState(() {
      _isLoading = true;
      _syncStatus = 'Performing nuclear clear...';
      _errorMessage = '';
    });
    
    try {
      await HiveService.ultimateNuclearClear();
      
      setState(() {
        _isLoading = false;
        _syncStatus = 'Nuclear clear completed';
      });
      
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Ultimate nuclear clear completed successfully!'),
          backgroundColor: Colors.green,
          duration: Duration(seconds: 3),
        ),
      );
      
      // Reload sync data after nuclear clear
      _loadSyncData();
      
    } catch (e) {
      setState(() {
        _isLoading = false;
        _syncStatus = 'Nuclear clear failed';
        _errorMessage = 'Error: $e';
      });
      
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Error during nuclear clear: $e'),
          backgroundColor: Colors.red,
          duration: Duration(seconds: 3),
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: PastelColors.primary,
        title: Text('Sync Status', style: AppTextStyles.h2.copyWith(color: Colors.white)),
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      backgroundColor: PastelColors.background,
      body: RefreshIndicator(
        onRefresh: _loadSyncData,
        child: SingleChildScrollView(
          physics: AlwaysScrollableScrollPhysics(),
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Status Card
              Card(
                color: PastelColors.cardBackground,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(8),
                  side: BorderSide(color: PastelColors.border),
                ),
                elevation: 0,
                child: Padding(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        children: [
                          Icon(
                            _isOnline ? Icons.cloud_done : Icons.cloud_off,
                            color: _isOnline ? PastelColors.success : PastelColors.warning,
                            size: 24,
                          ),
                          SizedBox(width: 12),
                          Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                _isOnline ? 'Online' : 'Offline',
                                style: AppTextStyles.h3.copyWith(
                                  color: _isOnline ? PastelColors.success : PastelColors.warning,
                                ),
                              ),
                              Text(
                                _isOnline ? 'Sync available' : 'Sync not available',
                                style: AppTextStyles.bodyMedium,
                              ),
                            ],
                          ),
                        ],
                      ),
                      Divider(height: 32),
                      _infoRow('Status', _syncStatus),
                      _infoRow('Last Sync', _lastSyncTime),
                      _infoRow('Pending Items', '${_pendingItems.length} items'),
                      if (_completedSyncs > 0 || _failedSyncs > 0) ...[
                        Divider(height: 32),
                        _infoRow('Successfully Synced', '$_completedSyncs items'),
                        _infoRow('Failed to Sync', '$_failedSyncs items'),
                      ],
                      if (_errorMessage.isNotEmpty) ...[
                        Divider(height: 32),
                        Text(
                          _errorMessage,
                          style: AppTextStyles.bodyMedium.copyWith(color: PastelColors.errorText),
                        ),
                      ],
                    ],
                  ),
                ),
              ),
              SizedBox(height: 24),
              
              // Sync Button
              SizedBox(
                width: double.infinity,
                height: 50,
                child: ElevatedButton.icon(
                  onPressed: _isLoading ? null : _triggerManualSync,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: PastelColors.primary,
                    foregroundColor: Colors.white,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(8),
                    ),
                    disabledBackgroundColor: PastelColors.textLight,
                  ),
                  icon: _isLoading 
                      ? SizedBox(
                          width: 20, 
                          height: 20, 
                          child: CircularProgressIndicator(
                            color: Colors.white,
                            strokeWidth: 2,
                          ),
                        )
                      : Icon(Icons.sync),
                  label: Text(
                    _isLoading ? 'Syncing...' : 'Sync Now',
                    style: TextStyle(fontSize: 16),
                  ),
                ),
              ),
              
              SizedBox(height: 16),
              
              // Resolve Button
              SizedBox(
                width: double.infinity,
                height: 50,
                child: ElevatedButton.icon(
                  onPressed: _isLoading ? null : _resolveActiveTrips,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: PastelColors.warning,
                    foregroundColor: Colors.white,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(8),
                    ),
                    disabledBackgroundColor: PastelColors.textLight,
                  ),
                  icon: Icon(Icons.build),
                  label: Text(
                    'Resolve Trip Conflicts',
                    style: TextStyle(fontSize: 16),
                  ),
                ),
              ),
              
              SizedBox(height: 16),
              
              // Ultimate Nuclear Clear Button
              SizedBox(
                width: double.infinity,
                height: 50,
                child: ElevatedButton.icon(
                  onPressed: _isLoading ? null : _ultimateNuclearClear,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: PastelColors.error,
                    foregroundColor: Colors.white,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(8),
                    ),
                    disabledBackgroundColor: PastelColors.textLight,
                  ),
                  icon: Icon(Icons.delete_forever),
                  label: Text(
                    'Ultimate Nuclear Clear',
                    style: TextStyle(fontSize: 16),
                  ),
                ),
              ),
              
              SizedBox(height: 24),
              
              // Pending Items
              Text('Pending Sync Items', style: AppTextStyles.h3),
              SizedBox(height: 8),
              
              _pendingItems.isEmpty
                  ? Card(
                      color: PastelColors.cardBackground,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(8),
                        side: BorderSide(color: PastelColors.border),
                      ),
                      elevation: 0,
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Center(
                          child: Column(
                            children: [
                              Icon(
                                Icons.check_circle_outline,
                                color: PastelColors.success,
                                size: 48,
                              ),
                              SizedBox(height: 8),
                              Text(
                                'All data synced',
                                style: AppTextStyles.bodyLarge,
                              ),
                              SizedBox(height: 4),
                              Text(
                                'No pending items waiting to be synced',
                                style: AppTextStyles.bodyMedium.copyWith(color: PastelColors.textSecondary),
                              ),
                            ],
                          ),
                        ),
                      ),
                    )
                  : ListView.builder(
                      shrinkWrap: true,
                      physics: NeverScrollableScrollPhysics(),
                      itemCount: _pendingItems.length,
                      itemBuilder: (context, index) {
                        final item = _pendingItems[index];
                        return Card(
                          color: PastelColors.cardBackground,
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(8),
                            side: BorderSide(color: PastelColors.border),
                          ),
                          elevation: 0,
                          margin: EdgeInsets.only(bottom: 8),
                          child: ListTile(
                            leading: _getIconForEndpoint(item.endpoint),
                            title: Text(_getTitleForEndpoint(item.endpoint)),
                            subtitle: Text(
                              'Attempts: ${item.retryCount}/3 • ${item.method} ${item.endpoint}',
                              style: AppTextStyles.bodySmall,
                            ),
                            trailing: Icon(Icons.hourglass_top, color: PastelColors.warning),
                          ),
                        );
                      },
                    ),
            ],
          ),
        ),
      ),
    );
  }
  
  Widget _infoRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label, style: AppTextStyles.bodyMedium.copyWith(color: PastelColors.textSecondary)),
          Text(value, style: AppTextStyles.bodyLarge),
        ],
      ),
    );
  }
  
  Icon _getIconForEndpoint(String endpoint) {
    if (endpoint.contains('start')) {
      return Icon(Icons.login, color: PastelColors.success);
    } else if (endpoint.contains('end') || endpoint.contains('checkout')) {
      return Icon(Icons.logout, color: PastelColors.warning);
    } else {
      return Icon(Icons.sync, color: PastelColors.primary);
    }
  }
  
  String _getTitleForEndpoint(String endpoint) {
    if (endpoint.contains('start')) {
      return 'Check In';
    } else if (endpoint.contains('end') || endpoint.contains('checkout')) {
      return 'Check Out';
    } else if (endpoint.contains('profile')) {
      return 'User Profile';
    } else {
      return 'App Data';
    }
  }
} 