import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../theme/pastel_colors.dart';
import '../theme/text_styles.dart';
import 'overview_tab.dart';
import 'do_tab.dart';
import 'profile_screen.dart';
import 'settings_screen.dart';
import 'notification_screen.dart';
import '../services/api_service.dart';
import '../core/api_client.dart';
import 'dart:async';
import 'dart:developer' as developer;
import 'help_screen.dart';
import 'privacy_policy_screen.dart';
import 'about_screen.dart';
import 'login_screen.dart';
import 'checkin_screen.dart';
import 'checkout_screen.dart';
import 'logs_screen.dart';
import 'report_tab.dart';
import '../services/auth_service.dart';

class DashboardScreen extends StatefulWidget {
  const DashboardScreen({super.key});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  int _selectedIndex = 0;
  final ApiService _apiService = ApiService(ApiClient());
  int _unreadCount = 0;

  @override
  void initState() {
    super.initState();
    _loadNotificationCount();
    // Auto-refresh notification count every 5 seconds
    _startNotificationPolling();
  }

  @override
  void dispose() {
    _notificationTimer?.cancel();
    super.dispose();
  }

  Timer? _notificationTimer;

  void _startNotificationPolling() {
    _notificationTimer = Timer.periodic(const Duration(seconds: 5), (timer) {
      _loadNotificationCount();
    });
  }

  Future<void> _loadNotificationCount() async {
    try {
      final response = await _apiService.getNotifications();
      if (response['success'] == true && mounted) {
        setState(() {
          _unreadCount = response['unread_count'] ?? 0;
        });
      }
    } catch (e) {
      developer.log('Load notification count error: $e');
    }
  }

  final List<Widget> _pages = [
    const OverviewTab(),
    const DoTab(),
    const ReportTab(),
  ];

  Future<void> _logout() async {
    final authService = context.read<AuthService>();
    await authService.logout();
    
    if (!mounted) return;
    
    // Navigate to login screen
    Navigator.pushReplacement(
      context,
      MaterialPageRoute(builder: (_) => const LoginScreen()),
    );
  }

  void _onItemTapped(int index) {
    setState(() {
      _selectedIndex = index;
    });
  }

  @override
  Widget build(BuildContext context) {
    // Dynamic breadcrumb data
    final List<Map<String, dynamic>> breadcrumbs = [
      {'icon': Icons.dashboard, 'label': 'Overview'},
      {'icon': Icons.check_circle_outline, 'label': 'Do'},
      {'icon': Icons.bar_chart, 'label': 'Report'},
    ];
    final breadcrumb = breadcrumbs[_selectedIndex];

    return Scaffold(
      backgroundColor: Colors.white,
      drawer: Drawer(
        elevation: 0,
        backgroundColor: Colors.white,
        width: 320, // MUCH WIDER drawer
        shape: const RoundedRectangleBorder(borderRadius: BorderRadius.zero),
        child: ListView(
          padding: EdgeInsets.zero,
          children: [
            // Profile section with CLEAR blue gradient
            Container(
              width: double.infinity,
              padding: const EdgeInsets.symmetric(vertical: 36),
              decoration: const BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.topCenter,
                  end: Alignment.bottomCenter,
                  colors: [
                    Color(0xFF1E3A8A), // Dark blue (top)
                    Color(0xFF3B82F6), // Bright blue (middle)
                    Color(0xFF60A5FA), // Light blue (bottom)
                  ],
                  stops: [0.0, 0.5, 1.0], // Clear gradient stops
                ),
              ),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Consumer<AuthService>(
                    builder: (context, authService, child) {
                      final user = authService.currentUser;
                      return Column(
                        children: [
                          CircleAvatar(
                            radius: 36,
                            backgroundColor: Colors.white,
                            child: user != null && (user['user']?['name'] ?? '').isNotEmpty
                                ? Text(
                                  (user['user']?['name'] ?? 'U')[0].toUpperCase(),
                                  style: TextStyle(fontSize: 30, color: PastelColors.primary),
                                )
                              : Icon(Icons.person, color: PastelColors.primary, size: 40),
                          ),
                          const SizedBox(height: 12),
                          Text(user?['user']?['name'] ?? 'User Name', style: AppTextStyles.h2.copyWith(color: Colors.white)),
                          const SizedBox(height: 4),
                          Text(user?['user']?['email'] ?? 'user@email.com', style: AppTextStyles.bodyMedium.copyWith(color: Colors.white70)),
                        ],
                      );
                    },
                  ),
                ],
              ),
            ),
            const SizedBox(height: 8),
            
            // Profile
            ListTile(
              leading: Icon(Icons.person, color: PastelColors.primary),
              title: Text('Profile', style: AppTextStyles.bodyLarge),
              hoverColor: PastelColors.primary.withOpacity(0.1),
              onTap: () {
                Navigator.of(context).pop();
                Navigator.push(context, MaterialPageRoute(builder: (_) => ProfileScreen()));
              },
            ),
            
            // Privacy Policy
            ListTile(
              leading: Icon(Icons.privacy_tip, color: PastelColors.primary),
              title: Text('Privacy Policy', style: AppTextStyles.bodyLarge),
              hoverColor: PastelColors.primary.withOpacity(0.1),
              onTap: () {
                Navigator.of(context).pop();
                Navigator.push(context, MaterialPageRoute(builder: (_) => PrivacyPolicyScreen()));
              },
            ),
            
            // About
            ListTile(
              leading: Icon(Icons.info_outline, color: PastelColors.primary),
              title: Text('About', style: AppTextStyles.bodyLarge),
              hoverColor: PastelColors.primary.withOpacity(0.1),
              onTap: () {
                Navigator.of(context).pop();
                Navigator.push(context, MaterialPageRoute(builder: (_) => AboutScreen()));
              },
            ),
            
            const Divider(),
            
            // Logout
            ListTile(
              leading: Icon(Icons.logout, color: PastelColors.primary),
              title: Text('Logout', style: AppTextStyles.bodyLarge),
              hoverColor: PastelColors.primary.withOpacity(0.1),
              onTap: _logout,
            ),
          ],
        ),
      ),
      appBar: AppBar(
        backgroundColor: PastelColors.primary,
        elevation: 0,
        leading: Builder(
          builder: (context) => IconButton(
            icon: const Icon(Icons.menu, color: Colors.white),
            onPressed: () {
              Scaffold.of(context).openDrawer();
            },
          ),
        ),
        titleSpacing: 0,
        title: Row(
          children: [
            const SizedBox(width: 8),
            Icon(
              breadcrumb['icon'],
              color: Colors.white,
              size: 20,
            ),
            const SizedBox(width: 8),
            Text(
              breadcrumb['label'],
              style: const TextStyle(
                color: Colors.white,
                fontWeight: FontWeight.w600,
                fontSize: 16,
              ),
            ),
          ],
        ),
        actions: [
          // Notification Bell Icon with Badge
          Stack(
            children: [
              IconButton(
                icon: const Icon(Icons.notifications_outlined, color: Colors.white, size: 26),
                onPressed: () async {
                  // Navigate to notification screen
                  await Navigator.push(context, MaterialPageRoute(builder: (_) => NotificationScreen()));
                  // Reload count after returning
                  _loadNotificationCount();
                },
              ),
              // Badge - IgnorePointer to allow clicking through to IconButton
              Positioned(
                right: 10,
                top: 10,
                child: IgnorePointer(
                  child: Container(
                    padding: const EdgeInsets.all(3),
                    decoration: const BoxDecoration(
                      color: Colors.red,
                      shape: BoxShape.circle,
                    ),
                    constraints: const BoxConstraints(
                      minWidth: 14,
                      minHeight: 14,
                    ),
                    child: Text(
                      _unreadCount > 99 ? '99+' : '$_unreadCount',
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 8,
                        fontWeight: FontWeight.bold,
                      ),
                      textAlign: TextAlign.center,
                    ),
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(width: 8),
        ],
      ),
      body: _pages[_selectedIndex],
      bottomNavigationBar: BottomNavigationBar(
        backgroundColor: PastelColors.primary,
        currentIndex: _selectedIndex,
        onTap: _onItemTapped,
        selectedItemColor: Colors.white,
        unselectedItemColor: Colors.white70,
        items: const [
          BottomNavigationBarItem(
            icon: Icon(Icons.dashboard),
            label: 'Overview',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.check_circle_outline),
            label: 'Do',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.bar_chart),
            label: 'Report',
          ),
        ],
      ),
    );
  }
}

