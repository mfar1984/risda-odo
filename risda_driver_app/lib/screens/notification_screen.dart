import 'package:flutter/material.dart';
import '../theme/pastel_colors.dart';
import '../theme/text_styles.dart';
import '../services/api_service.dart';
import '../core/api_client.dart';
 
import 'package:intl/intl.dart';

class NotificationScreen extends StatefulWidget {
  @override
  State<NotificationScreen> createState() => _NotificationScreenState();
}

class _NotificationScreenState extends State<NotificationScreen> {
  final ApiService _apiService = ApiService(ApiClient());
  List<Map<String, dynamic>> _notifications = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadNotifications();
  }

  Future<void> _loadNotifications() async {
    setState(() => _isLoading = true);
    try {
      final response = await _apiService.getNotifications();
      if (response['success'] == true && mounted) {
        setState(() {
          _notifications = List<Map<String, dynamic>>.from(response['data'] ?? []);
          _isLoading = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() => _isLoading = false);
      }
    }
  }

  Future<void> _markAsRead(int id) async {
    // Optimistic UI: mark as read immediately
    final index = _notifications.indexWhere((n) => n['id'] == id);
    if (index != -1 && mounted) {
      setState(() {
        _notifications[index]['read_at'] = DateTime.now().toIso8601String();
      });
    }
    // Fire-and-forget API (soft-fail already handled in ApiService)
    final res = await _apiService.markNotificationAsRead(id);
    if (res['success'] != true && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Gagal mark as read (server). Akan cuba semula.')),
      );
    }
  }

  Future<void> _markAllAsRead() async {
    // Optimistic UI: mark all as read immediately
    if (mounted) {
      setState(() {
        _notifications = _notifications.map((n) {
          final m = Map<String, dynamic>.from(n);
          m['read_at'] = DateTime.now().toIso8601String();
          return m;
        }).toList();
      });
    }
    // Fire-and-forget API
    try {
      await _apiService.markAllNotificationsAsRead();
    } catch (e) {
      
    }
  }

  Color _getNotificationColor(String type) {
    switch (type) {
      case 'claim_approved':
        return PastelColors.success;
      case 'claim_rejected':
      case 'claim_cancelled':
        return PastelColors.error;
      case 'program_assigned':
      case 'program_approved':
        return PastelColors.info;
      case 'journey_started':
      case 'journey_ended':
        return PastelColors.warning;
      case 'program_auto_closed':
        return PastelColors.success;
      case 'program_tertunda':
        return PastelColors.error;
      default:
        return PastelColors.info;
    }
  }

  IconData _getNotificationIcon(String type) {
    switch (type) {
      case 'claim_approved':
        return Icons.check_circle;
      case 'claim_rejected':
        return Icons.cancel;
      case 'claim_cancelled':
        return Icons.block;
      case 'program_assigned':
      case 'program_approved':
        return Icons.event;
      case 'journey_started':
        return Icons.trip_origin;
      case 'journey_ended':
        return Icons.flag;
      case 'program_auto_closed':
        return Icons.check_circle_outline;
      case 'program_tertunda':
        return Icons.warning_amber;
      default:
        return Icons.notifications;
    }
  }

  String _formatDate(String? dateStr) {
    if (dateStr == null) return '';
    try {
      final date = DateTime.parse(dateStr);
      return DateFormat('dd MMM yyyy, HH:mm').format(date);
    } catch (e) {
      return dateStr;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: PastelColors.primary,
        title: Text('Notifications', style: AppTextStyles.h2.copyWith(color: Colors.white)),
        iconTheme: const IconThemeData(color: Colors.white),
        actions: [
          if (_notifications.any((n) => n['read_at'] == null))
            TextButton(
              onPressed: _markAllAsRead,
              child: const Text('Mark All Read', style: TextStyle(color: Colors.white, fontSize: 12)),
            ),
        ],
      ),
      backgroundColor: PastelColors.background,
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _notifications.isEmpty
              ? Center(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Icon(Icons.notifications_off, size: 64, color: Colors.grey[400]),
                      const SizedBox(height: 16),
                      Text('No notifications', style: AppTextStyles.bodyLarge.copyWith(color: Colors.grey[600])),
                    ],
                  ),
                )
              : RefreshIndicator(
                  onRefresh: _loadNotifications,
                  child: ListView.builder(
                    padding: const EdgeInsets.all(16),
                    itemCount: _notifications.length,
                    itemBuilder: (context, index) {
                      final notif = _notifications[index];
                      final isRead = notif['read_at'] != null;
                      final color = _getNotificationColor(notif['type'] ?? '');
                      final icon = _getNotificationIcon(notif['type'] ?? '');

                      return Card(
                        color: isRead ? Colors.white : color.withOpacity(0.1),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(6),
                          side: BorderSide(
                            color: isRead ? PastelColors.border : color.withOpacity(0.3),
                            width: isRead ? 1 : 2,
                          ),
                        ),
                        margin: const EdgeInsets.only(bottom: 12),
                        child: ListTile(
                          leading: Icon(icon, color: color),
                          title: Text(
                            notif['title'] ?? 'Notification',
                            style: AppTextStyles.bodyLarge.copyWith(
                              fontWeight: isRead ? FontWeight.normal : FontWeight.bold,
                            ),
                          ),
                          subtitle: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              const SizedBox(height: 4),
                              Text(
                                notif['message'] ?? '',
                                style: AppTextStyles.bodyMedium,
                              ),
                              const SizedBox(height: 4),
                              Text(
                                _formatDate(notif['created_at']),
                                style: AppTextStyles.bodySmall.copyWith(color: Colors.grey[600]),
                              ),
                            ],
                          ),
                          trailing: !isRead
                              ? IconButton(
                                  icon: const Icon(Icons.check, color: Colors.green),
                                  onPressed: () => _markAsRead(notif['id']),
                                )
                              : null,
                          onTap: () {
                            if (!isRead) {
                              _markAsRead(notif['id']);
                            }
                            // TODO: Navigate to detail screen based on action_url or data
                          },
                        ),
                      );
                    },
                  ),
                ),
    );
  }
}
