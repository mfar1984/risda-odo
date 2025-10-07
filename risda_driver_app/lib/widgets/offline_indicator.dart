import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../services/connectivity_service.dart';
import 'dart:developer' as developer;

/// Offline/Online Indicator Widget
/// Shows animated status before bell icon in AppBar
/// - Green pulse: Online
/// - Red static: Offline
/// - Yellow rotating: Syncing
class OfflineIndicator extends StatefulWidget {
  final bool showSyncStatus;
  
  const OfflineIndicator({
    Key? key,
    this.showSyncStatus = false,
  }) : super(key: key);

  @override
  State<OfflineIndicator> createState() => _OfflineIndicatorState();
}

class _OfflineIndicatorState extends State<OfflineIndicator>
    with SingleTickerProviderStateMixin {
  late AnimationController _animationController;
  late Animation<double> _pulseAnimation;
  
  @override
  void initState() {
    super.initState();
    
    developer.log('🎯 OfflineIndicator: Widget initialized');
    
    // Pulse animation (for online status)
    _animationController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1500),
    )..repeat(reverse: true);
    
    _pulseAnimation = Tween<double>(begin: 0.6, end: 1.0).animate(
      CurvedAnimation(
        parent: _animationController,
        curve: Curves.easeInOut,
      ),
    );
  }
  
  @override
  void dispose() {
    _animationController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Consumer<ConnectivityService>(
      builder: (context, connectivity, child) {
        final isOnline = connectivity.isOnline;
        final isChecking = connectivity.isChecking;
        
        // Debug: Log every rebuild
        developer.log('🎨 OfflineIndicator BUILD: isOnline=$isOnline, isChecking=$isChecking');
        
        return GestureDetector(
          onTap: () => _showConnectionDetails(context, connectivity),
          child: Padding(
            padding: const EdgeInsets.only(right: 8),
            child: AnimatedBuilder(
              animation: _pulseAnimation,
              builder: (context, child) {
                return Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                  decoration: BoxDecoration(
                    color: _getBackgroundColor(isOnline, isChecking),
                    borderRadius: BorderRadius.circular(12),
                    boxShadow: isOnline
                        ? [
                            BoxShadow(
                              color: Colors.green.withOpacity(0.3 * _pulseAnimation.value),
                              blurRadius: 8 * _pulseAnimation.value,
                              spreadRadius: 2 * _pulseAnimation.value,
                            )
                          ]
                        : null,
                  ),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      // Icon
                      Icon(
                        _getIcon(isOnline, isChecking),
                        color: Colors.white,
                        size: 14,
                      ),
                      
                      const SizedBox(width: 4),
                      
                      // Status text
                      Text(
                        isChecking ? 'Checking...' : (isOnline ? 'Online' : 'Offline'),
                        style: const TextStyle(
                          color: Colors.white,
                          fontSize: 11,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                      
                      // Small manual refresh icon appears if offline
                      if (!isOnline && !isChecking) ...[
                        const SizedBox(width: 6),
                        GestureDetector(
                          onTap: () {
                            developer.log('🔁 Manual recheck tapped from indicator');
                            context.read<ConnectivityService>().checkConnection();
                          },
                          child: const Icon(Icons.refresh, color: Colors.white, size: 14),
                        )
                      ]
                    ],
                  ),
                );
              },
            ),
          ),
        );
      },
    );
  }
  
  Color _getBackgroundColor(bool isOnline, bool isChecking) {
    if (isChecking) return Colors.orange;
    return isOnline ? Colors.green : Colors.red;
  }
  
  IconData _getIcon(bool isOnline, bool isChecking) {
    if (isChecking) return Icons.wifi_find;
    return isOnline ? Icons.wifi : Icons.wifi_off;
  }
  
  void _showConnectionDetails(BuildContext context, ConnectivityService connectivity) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        title: Row(
          children: [
            Icon(
              connectivity.isOnline ? Icons.wifi : Icons.wifi_off,
              color: connectivity.isOnline ? Colors.green : Colors.red,
            ),
            const SizedBox(width: 8),
            Text(connectivity.isOnline ? 'Online' : 'Offline'),
          ],
        ),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            _buildDetailRow('Status', connectivity.getStatusText()),
            const SizedBox(height: 8),
            if (connectivity.lastChecked != null)
              _buildDetailRow(
                'Last Checked',
                _formatTime(connectivity.lastChecked!),
              ),
            const SizedBox(height: 8),
            if (connectivity.isOffline && connectivity.getOfflineDuration() != null)
              _buildDetailRow(
                'Offline Since',
                _formatDuration(connectivity.getOfflineDuration()!),
              ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('Close'),
          ),
          if (!connectivity.isChecking)
            ElevatedButton.icon(
              onPressed: () async {
                Navigator.pop(context);
                await connectivity.checkConnection();
              },
              icon: const Icon(Icons.refresh, size: 16),
              label: const Text('Recheck'),
            ),
        ],
      ),
    );
  }
  
  Widget _buildDetailRow(String label, String value) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        SizedBox(
          width: 100,
          child: Text(
            label,
            style: TextStyle(
              fontSize: 12,
              color: Colors.grey[600],
              fontWeight: FontWeight.w500,
            ),
          ),
        ),
        Expanded(
          child: Text(
            value,
            style: const TextStyle(
              fontSize: 12,
              fontWeight: FontWeight.w600,
            ),
          ),
        ),
      ],
    );
  }
  
  String _formatTime(DateTime time) {
    final now = DateTime.now();
    final diff = now.difference(time);
    
    if (diff.inSeconds < 60) return '${diff.inSeconds}s ago';
    if (diff.inMinutes < 60) return '${diff.inMinutes}m ago';
    if (diff.inHours < 24) return '${diff.inHours}h ago';
    return '${diff.inDays}d ago';
  }
  
  String _formatDuration(Duration duration) {
    if (duration.inMinutes < 1) return 'Just now';
    if (duration.inHours < 1) return '${duration.inMinutes} minutes';
    if (duration.inDays < 1) return '${duration.inHours} hours';
    return '${duration.inDays} days';
  }
}

