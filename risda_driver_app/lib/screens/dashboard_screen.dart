import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../theme/pastel_colors.dart';
import '../theme/text_styles.dart';
import 'overview_tab.dart';
import 'do_tab.dart';
import 'profile_screen.dart';
import 'settings_screen.dart';
import 'notification_screen.dart';
import 'help_screen.dart';
import 'about_screen.dart';
import 'login_screen.dart';
import 'checkin_screen.dart';
import 'checkout_screen.dart';
import 'logs_screen.dart';
import 'report_tab.dart';
import 'sync_status_screen.dart';
import '../services/auth_service.dart';

class DashboardScreen extends StatefulWidget {
  const DashboardScreen({super.key});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  int _selectedIndex = 0;

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
        shape: const RoundedRectangleBorder(borderRadius: BorderRadius.zero),
        child: Column(
          children: [
            // Profile section with pastel green gradient
            Container(
              width: double.infinity,
              padding: const EdgeInsets.symmetric(vertical: 36),
              decoration: const BoxDecoration(
                gradient: PastelColors.primaryGradient,
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
                            child: user != null && user.name.isNotEmpty
                              ? Text(
                                  user.name[0].toUpperCase(),
                                  style: TextStyle(fontSize: 30, color: PastelColors.primary),
                                )
                              : Icon(Icons.person, color: PastelColors.primary, size: 40),
                          ),
                          const SizedBox(height: 12),
                          Text(user?.name ?? 'User Name', style: AppTextStyles.h2.copyWith(color: Colors.white)),
                          const SizedBox(height: 4),
                          Text(user?.email ?? 'user@email.com', style: AppTextStyles.bodyMedium.copyWith(color: Colors.white70)),
                        ],
                      );
                    },
                  ),
                ],
              ),
            ),
            const SizedBox(height: 16),
            ListTile(
              leading: Icon(Icons.person, color: PastelColors.primary),
              title: Text('Profile', style: AppTextStyles.bodyLarge),
              onTap: () {
                Navigator.of(context).pop();
                Navigator.push(context, MaterialPageRoute(builder: (_) => ProfileScreen()));
              },
            ),
            ListTile(
              leading: Icon(Icons.settings, color: PastelColors.primary),
              title: Text('Settings', style: AppTextStyles.bodyLarge),
              onTap: () {
                Navigator.of(context).pop();
                Navigator.push(context, MaterialPageRoute(builder: (_) => SettingsScreen()));
              },
            ),
            const Divider(),
            ListTile(
              leading: Icon(Icons.logout, color: PastelColors.primary),
              title: Text('Logout', style: AppTextStyles.bodyLarge),
              onTap: _logout,
            ),
            const Spacer(),
            Padding(
              padding: EdgeInsets.only(left: 16, right: 16, bottom: 24, top: 8),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Expanded(
                    child: ListTile(
                      tileColor: PastelColors.error.withOpacity(0.08),
                      leading: Icon(Icons.delete_forever, color: PastelColors.errorText),
                      title: Center(child: Text('Delete Account', style: AppTextStyles.bodyLarge.copyWith(color: PastelColors.errorText))),
                      onTap: () {
                        Navigator.of(context).pop();
                        // TODO: Implement delete account logic
                      },
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(6)),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
      endDrawer: Drawer(
        elevation: 0,
        shape: const RoundedRectangleBorder(borderRadius: BorderRadius.zero),
        child: ListView(
          padding: EdgeInsets.zero,
          children: [
            const SizedBox(height: 32),
            // Sync Status - New Item
            ListTile(
              leading: Icon(Icons.sync, color: PastelColors.primary),
              title: Text('Sync Status', style: AppTextStyles.bodyLarge),
              subtitle: Text('View sync status and resolve issues'),
              onTap: () {
                Navigator.of(context).pop();
                Navigator.push(context, MaterialPageRoute(builder: (_) => SyncStatusScreen()));
              },
            ),
            const Divider(),
            ListTile(
              leading: Icon(Icons.notifications, color: PastelColors.primary),
              title: Text('Notification', style: AppTextStyles.bodyLarge),
              onTap: () {
                Navigator.of(context).pop();
                Navigator.push(context, MaterialPageRoute(builder: (_) => NotificationScreen()));
              },
            ),
            ListTile(
              leading: Icon(Icons.privacy_tip, color: PastelColors.primary),
              title: Text('Privacy Policy', style: AppTextStyles.bodyLarge),
              onTap: () {
                Navigator.of(context).pop();
                Navigator.push(context, MaterialPageRoute(builder: (_) => HelpScreen()));
              },
            ),
            ListTile(
              leading: Icon(Icons.info_outline, color: PastelColors.primary),
              title: Text('About', style: AppTextStyles.bodyLarge),
              onTap: () {
                Navigator.of(context).pop();
                Navigator.push(context, MaterialPageRoute(builder: (_) => AboutScreen()));
              },
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
          // Debug button
          // Debug button removed - no longer needed
          // Removed Sync button and kept only the menu button
          Builder(
            builder: (context) => IconButton(
              icon: const Icon(Icons.more_vert, color: Colors.white),
              onPressed: () {
                Scaffold.of(context).openEndDrawer();
              },
            ),
          ),
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

