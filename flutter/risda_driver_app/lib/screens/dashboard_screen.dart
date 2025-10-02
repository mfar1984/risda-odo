import 'package:flutter/material.dart';
import '../theme/pastel_colors.dart';
import '../theme/text_styles.dart';
import 'overview_tab.dart';
import 'package:fl_chart/fl_chart.dart';
import 'profile_screen.dart';
import 'settings_screen.dart';
import 'notification_screen.dart';
import 'help_screen.dart';
import 'about_screen.dart';
import 'login_screen.dart';
import 'checkin_screen.dart';
import 'checkout_screen.dart';
import 'logs_screen.dart';
import 'claim_main_tab.dart';
import 'sync_status_screen.dart';
import '../main.dart';
import '../utils/debug_utils.dart';
import '../services/hive_service.dart';
import '../models/user_model.dart';
import '../services/api_service.dart';

class DashboardScreen extends StatefulWidget {
  const DashboardScreen({super.key});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  int _selectedIndex = 0;
  User? _user;

  final List<Widget> _pages = [
    const OverviewTab(),
    DoTab(),
    ReportTab(),
  ];

  @override
  void initState() {
    super.initState();
    _loadUserData();
  }

  Future<void> _loadUserData() async {
    final user = HiveService.getUser();
    setState(() {
      _user = user;
    });
  }

  Future<void> _logout() async {
    try {
      final apiService = ApiService();
      await apiService.logout();
    } catch (e) {
      // Ignore errors on logout
    }
    
    // Clear local data
    await HiveService.clearAuth();
    await HiveService.clearUser();
    
    // Navigate to login screen
    Navigator.pushReplacement(context, MaterialPageRoute(builder: (_) => LoginScreen()));
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
                  CircleAvatar(
                    radius: 36,
                    backgroundColor: Colors.white,
                    child: _user != null && _user!.name.isNotEmpty
                      ? Text(
                          _user!.name[0].toUpperCase(),
                          style: TextStyle(fontSize: 30, color: PastelColors.primary),
                        )
                      : Icon(Icons.person, color: PastelColors.primary, size: 40),
                  ),
                  const SizedBox(height: 12),
                  Text(_user?.name ?? 'User Name', style: AppTextStyles.h2.copyWith(color: Colors.white)),
                  const SizedBox(height: 4),
                  Text(_user?.email ?? 'user@email.com', style: AppTextStyles.bodyMedium.copyWith(color: Colors.white70)),
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
          IconButton(
            icon: const Icon(Icons.bug_report, color: Colors.white),
            onPressed: () {
              showHiveDataDialog(context);
            },
          ),
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

class DoTab extends StatefulWidget {
  @override
  State<DoTab> createState() => _DoTabState();
}

class _DoTabState extends State<DoTab> {
  // Analytics data that can be refreshed
  List<FlSpot> checkInData = [
    FlSpot(0, 8), FlSpot(1, 10), FlSpot(2, 7), FlSpot(3, 12), 
    FlSpot(4, 11), FlSpot(5, 13), FlSpot(6, 12), FlSpot(7, 14), 
    FlSpot(8, 10), FlSpot(9, 15), FlSpot(10, 13), FlSpot(11, 17),
  ];
  
  List<FlSpot> checkOutData = [
    FlSpot(0, 6), FlSpot(1, 8), FlSpot(2, 5), FlSpot(3, 9), 
    FlSpot(4, 8), FlSpot(5, 10), FlSpot(6, 9), FlSpot(7, 11), 
    FlSpot(8, 8), FlSpot(9, 12), FlSpot(10, 10), FlSpot(11, 14),
  ];

  Future<void> _refreshData() async {
    // Simulate fetching new data
    await Future.delayed(Duration(seconds: 1));
    
    // Update with "new" data (for demo purposes)
    setState(() {
      // Slightly modify the data to simulate refresh
      for (int i = 0; i < checkInData.length; i++) {
        checkInData[i] = FlSpot(checkInData[i].x, checkInData[i].y + ([-0.5, 0, 0.5]..shuffle()).first);
        checkOutData[i] = FlSpot(checkOutData[i].x, checkOutData[i].y + ([-0.5, 0, 0.5]..shuffle()).first);
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return RefreshIndicator(
      onRefresh: _refreshData,
      child: SingleChildScrollView(
        physics: const AlwaysScrollableScrollPhysics(),
        child: Padding(
          padding: const EdgeInsets.all(8),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Comprehensive Analytics Card
              Card(
                color: Colors.white,
                elevation: 3,
                shadowColor: PastelColors.primary.withOpacity(0.15),
                margin: const EdgeInsets.all(3),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(3),
                  side: BorderSide(color: PastelColors.border, width: 1),
                ),
                child: Padding(
                  padding: const EdgeInsets.all(12),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        children: [
                          Icon(Icons.analytics, color: PastelColors.primary, size: 20),
                          const SizedBox(width: 8),
                          Text('Comprehensive Analytics', style: AppTextStyles.h2),
                        ],
                      ),
                      const SizedBox(height: 8),
                      SizedBox(
                        height: 160,
                        child: LineChart(
                          LineChartData(
                            gridData: FlGridData(show: true, drawVerticalLine: false, horizontalInterval: 1, getDrawingHorizontalLine: (value) => FlLine(color: PastelColors.divider, strokeWidth: 1)),
                            titlesData: FlTitlesData(
                              leftTitles: AxisTitles(
                                sideTitles: SideTitles(showTitles: true, reservedSize: 28, getTitlesWidget: (value, meta) => Text(value.toInt().toString(), style: AppTextStyles.bodySmall)),
                              ),
                              bottomTitles: AxisTitles(
                                sideTitles: SideTitles(showTitles: true, reservedSize: 28, getTitlesWidget: (value, meta) {
                                  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                                  return Text(months[value.toInt() % 12], style: AppTextStyles.bodySmall);
                                }),
                              ),
                              rightTitles: AxisTitles(sideTitles: SideTitles(showTitles: false)),
                              topTitles: AxisTitles(sideTitles: SideTitles(showTitles: false)),
                            ),
                            borderData: FlBorderData(show: false),
                            lineBarsData: [
                              LineChartBarData(
                                spots: checkInData,
                                isCurved: true,
                                color: PastelColors.success, // Check In = Green card
                                barWidth: 3,
                                dotData: FlDotData(show: false),
                                belowBarData: BarAreaData(show: false),
                              ),
                              LineChartBarData(
                                spots: checkOutData,
                                isCurved: true,
                                color: PastelColors.warning, // Check Out = Yellow card
                                barWidth: 3,
                                dotData: FlDotData(show: false),
                                belowBarData: BarAreaData(show: false),
                              ),
                            ],
                            lineTouchData: LineTouchData(enabled: true),
                            minY: 0,
                            maxY: 20,
                          ),
                        ),
                      ),
                      const SizedBox(height: 8),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                        children: [
                          _buildLegend('Check In', PastelColors.success),
                          _buildLegend('Check Out', PastelColors.warning),
                        ],
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 8),
              // 2x2 Grid of Action Cards
              GridView.count(
                crossAxisCount: 2,
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                mainAxisSpacing: 6,
                crossAxisSpacing: 6,
                childAspectRatio: 1.2,
                children: [
                  // Check In: Green card, white icon
                  _buildActionCard(
                    Icons.login,
                    'Check In',
                    Colors.white, // icon color
                    textColor: Colors.white, // text color
                    bgColor: PastelColors.success,
                    borderColor: PastelColors.successText,
                    onTap: () {
                      Navigator.push(context, MaterialPageRoute(builder: (_) => CheckInScreen()));
                    },
                  ),
                  // Check Out: Yellow card, white icon
                  _buildActionCard(
                    Icons.logout,
                    'Check Out',
                    Colors.white, // icon color
                    textColor: Colors.white, // text color
                    bgColor: PastelColors.warning,
                    borderColor: PastelColors.warningText,
                    onTap: () {
                      Navigator.push(context, MaterialPageRoute(builder: (_) => CheckOutScreen()));
                    },
                  ),
                  // Claim: Blue card, white icon
                  _buildActionCard(
                    Icons.receipt_long,
                    'Claim',
                    Colors.white, // icon color
                    textColor: Colors.white, // text color
                    bgColor: PastelColors.info,
                    borderColor: PastelColors.infoText,
                    onTap: () {
                      Navigator.push(context, MaterialPageRoute(builder: (_) => ClaimMainTab()));
                    },
                  ),
                  // Logs: Pink card, white icon
                  _buildActionCard(
                    Icons.list_alt,
                    'Logs',
                    Colors.white, // icon color
                    textColor: Colors.white, // text color
                    bgColor: PastelColors.error,
                    borderColor: PastelColors.errorText,
                    onTap: () {
                      Navigator.push(context, MaterialPageRoute(builder: (_) => LogsScreen()));
                    },
                  ),
                ],
              ),
              // Add a small padding at the bottom to ensure pull-to-refresh works well
              const SizedBox(height: 20),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildLegend(String label, Color color) {
    return Row(
      children: [
        Container(width: 12, height: 12, decoration: BoxDecoration(color: color, shape: BoxShape.circle)),
        const SizedBox(width: 4),
        Text(label, style: AppTextStyles.bodyMedium),
      ],
    );
  }

  Widget _buildActionCard(IconData icon, String label, Color color, {Color? bgColor, Color? borderColor, Color? textColor, VoidCallback? onTap}) {
    return _InteractiveCard(
      icon: icon,
      label: label,
      color: color,
      textColor: textColor ?? color,
      bgColor: bgColor ?? Colors.white,
      borderColor: borderColor ?? PastelColors.border,
      onTap: onTap,
    );
  }
}

class _InteractiveCard extends StatefulWidget {
  final IconData icon;
  final String label;
  final Color color;
  final Color textColor;
  final Color bgColor;
  final Color borderColor;
  final VoidCallback? onTap;
  const _InteractiveCard({super.key, required this.icon, required this.label, required this.color, required this.textColor, required this.bgColor, required this.borderColor, this.onTap});

  @override
  State<_InteractiveCard> createState() => _InteractiveCardState();
}

class _InteractiveCardState extends State<_InteractiveCard> {
  bool _hovering = false;

  @override
  Widget build(BuildContext context) {
    return MouseRegion(
      onEnter: (_) => setState(() => _hovering = true),
      onExit: (_) => setState(() => _hovering = false),
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 150),
        curve: Curves.easeInOut,
        decoration: BoxDecoration(
          color: widget.bgColor,
          borderRadius: BorderRadius.circular(3),
          border: Border.all(color: widget.borderColor, width: 1),
          boxShadow: _hovering
              ? [BoxShadow(color: widget.color.withOpacity(0.25), blurRadius: 12, offset: const Offset(0, 4))]
              : [BoxShadow(color: widget.color.withOpacity(0.10), blurRadius: 4, offset: const Offset(0, 2))],
        ),
        child: InkWell(
          borderRadius: BorderRadius.circular(3),
          onTap: widget.onTap,
          child: Center(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                Icon(
                  widget.icon,
                  color: _hovering ? widget.color.withOpacity(0.85) : widget.color,
                  size: _hovering ? 48 : 40,
                  shadows: [Shadow(color: Colors.black26, blurRadius: 2, offset: Offset(0, 1))],
                ),
                const SizedBox(height: 10),
                Text(
                  widget.label,
                  style: AppTextStyles.bodyLarge.copyWith(
                    color: widget.textColor,
                    fontWeight: FontWeight.w600,
                    shadows: [Shadow(color: Colors.black26, blurRadius: 2, offset: Offset(0, 1))],
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

class ReportTab extends StatefulWidget {
  @override
  State<ReportTab> createState() => _ReportTabState();
}

class _ReportTabState extends State<ReportTab> {
  // Dummy data for Vehicle, Cost, Driver
  final List<Map<String, String>> vehicleData = List.generate(15, (i) => {
    'noPlate': 'QAA${1000 + i}',
    'program': 'Program ${String.fromCharCode(65 + (i % 3))}',
    'location': ['Kuching', 'Sibu', 'Miri'][i % 3],
    'distance': (80 + i * 5).toString(),
  });
  final List<Map<String, String>> costData = List.generate(13, (i) => {
    'date': '2024-07-${(i+1).toString().padLeft(2, '0')}',
    'vehicle': 'QAA${1000 + (i % 5)}',
    'program': 'Program ${String.fromCharCode(65 + (i % 3))}',
    'amount': (30 + i * 7).toStringAsFixed(2),
  });
  final List<Map<String, String>> driverData = List.generate(7, (i) => {
    'programs': (3 + i).toString(),
    'checkin': (7 + i * 2).toString(),
    'checkout': (7 + i * 2 - (i % 2)).toString(),
    'status': i < 6 ? 'Active' : 'Retired',
  });

  // Pagination state
  int vehicleShown = 10;
  int costShown = 10;
  int driverShown = 7;

  // Date filter state
  DateTime? vehicleFrom, vehicleTo;
  DateTime? costFrom, costTo;
  DateTime? driverFrom, driverTo;

  @override
  Widget build(BuildContext context) {
    return DefaultTabController(
      length: 4,
      child: Column(
        children: [
          Container(
            color: PastelColors.background,
            child: const TabBar(
              labelColor: PastelColors.primary,
              unselectedLabelColor: PastelColors.textSecondary,
              indicatorColor: PastelColors.primary,
              tabs: [
                Tab(text: 'Vehicle'),
                Tab(text: 'Cost'),
                Tab(text: 'Driver'),
                Tab(text: 'Help'),
              ],
            ),
          ),
          Expanded(
            child: TabBarView(
              children: [
                // Vehicle Tab
                RefreshIndicator(
                  onRefresh: () async {
                    // Simulate fetching new vehicle data
                    await Future.delayed(Duration(seconds: 1));
                    if (mounted) {
                      setState(() {
                        // Refresh vehicle data (simulate for demo)
                        vehicleData.shuffle();
                      });
                    }
                  },
                  child: Padding(
                    padding: EdgeInsets.all(12),
                    child: Card(
                      color: PastelColors.cardBackground,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(3),
                        side: BorderSide(color: PastelColors.border, width: 1),
                      ),
                      child: Column(
                        children: [
                          // Search From/To and Generate Report
                          Row(
                            children: [
                              Expanded(
                                child: Padding(
                                  padding: const EdgeInsets.only(left: 8, top: 12),
                                  child: _DatePickerField(
                                    hint: 'Search From',
                                    selected: vehicleFrom,
                                    onTap: () async {
                                      DateTime? picked = await showDatePicker(
                                        context: context,
                                        initialDate: vehicleFrom ?? DateTime.now(),
                                        firstDate: DateTime(2020),
                                        lastDate: DateTime(2100),
                                      );
                                      if (picked != null) setState(() => vehicleFrom = picked);
                                    },
                                  ),
                                ),
                              ),
                              const SizedBox(width: 8),
                              Expanded(
                                child: Padding(
                                  padding: const EdgeInsets.only(top: 12),
                                  child: _DatePickerField(
                                    hint: 'Search To',
                                    selected: vehicleTo,
                                    onTap: () async {
                                      DateTime? picked = await showDatePicker(
                                        context: context,
                                        initialDate: vehicleTo ?? DateTime.now(),
                                        firstDate: DateTime(2020),
                                        lastDate: DateTime(2100),
                                      );
                                      if (picked != null) setState(() => vehicleTo = picked);
                                    },
                                  ),
                                ),
                              ),
                              const SizedBox(width: 8),
                              Padding(
                                padding: const EdgeInsets.only(right: 8, top: 12),
                                child: ElevatedButton(
                                  onPressed: () {},
                                  style: ElevatedButton.styleFrom(
                                    backgroundColor: PastelColors.primary,
                                    foregroundColor: Colors.white,
                                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                                    textStyle: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600),
                                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(3)),
                                  ),
                                  child: const Text('Generate Report'),
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 12),
                          Expanded(
                            child: ListView(
                              physics: const AlwaysScrollableScrollPhysics(),
                              children: [
                                DataTable(
                                  columns: const [
                                    DataColumn(label: Text('No Plate')),
                                    DataColumn(label: Text('Program')),
                                    DataColumn(label: Text('Location')),
                                    DataColumn(label: Text('KM')),
                                  ],
                                  rows: [
                                    for (var v in vehicleData.take(vehicleShown))
                                      DataRow(cells: [
                                        DataCell(Text(v['noPlate']!)),
                                        DataCell(Text(v['program']!)),
                                        DataCell(Text(v['location']!)),
                                        DataCell(Text(v['distance']!)),
                                      ]),
                                  ],
                                ),
                                const SizedBox(height: 8),
                                Align(
                                  alignment: Alignment.center,
                                  child: ElevatedButton(
                                    onPressed: vehicleShown < vehicleData.length
                                        ? () => setState(() => vehicleShown = (vehicleShown + 10).clamp(0, vehicleData.length))
                                        : null,
                                    style: ElevatedButton.styleFrom(
                                      backgroundColor: vehicleShown < vehicleData.length ? PastelColors.primary : PastelColors.textLight,
                                      foregroundColor: Colors.white,
                                      padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
                                      textStyle: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600),
                                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(3)),
                                    ),
                                    child: Text(vehicleShown < vehicleData.length ? 'Load More' : 'Finish'),
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
                // Cost Tab
                RefreshIndicator(
                  onRefresh: () async {
                    // Simulate fetching new cost data
                    await Future.delayed(Duration(seconds: 1));
                    if (mounted) {
                      setState(() {
                        // Refresh cost data (simulate for demo)
                        costData.shuffle();
                      });
                    }
                  },
                  child: Padding(
                    padding: EdgeInsets.all(12),
                    child: Card(
                      color: PastelColors.cardBackground,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(3),
                        side: BorderSide(color: PastelColors.border, width: 1),
                      ),
                      child: Column(
                        children: [
                          Row(
                            children: [
                              Expanded(
                                child: Padding(
                                  padding: const EdgeInsets.only(left: 8, top: 12),
                                  child: _DatePickerField(
                                    hint: 'Search From',
                                    selected: costFrom,
                                    onTap: () async {
                                      DateTime? picked = await showDatePicker(
                                        context: context,
                                        initialDate: costFrom ?? DateTime.now(),
                                        firstDate: DateTime(2020),
                                        lastDate: DateTime(2100),
                                      );
                                      if (picked != null) setState(() => costFrom = picked);
                                    },
                                  ),
                                ),
                              ),
                              const SizedBox(width: 8),
                              Expanded(
                                child: Padding(
                                  padding: const EdgeInsets.only(top: 12),
                                  child: _DatePickerField(
                                    hint: 'Search To',
                                    selected: costTo,
                                    onTap: () async {
                                      DateTime? picked = await showDatePicker(
                                        context: context,
                                        initialDate: costTo ?? DateTime.now(),
                                        firstDate: DateTime(2020),
                                        lastDate: DateTime(2100),
                                      );
                                      if (picked != null) setState(() => costTo = picked);
                                    },
                                  ),
                                ),
                              ),
                              const SizedBox(width: 8),
                              Padding(
                                padding: const EdgeInsets.only(right: 8, top: 12),
                                child: ElevatedButton(
                                  onPressed: () {},
                                  style: ElevatedButton.styleFrom(
                                    backgroundColor: PastelColors.primary,
                                    foregroundColor: Colors.white,
                                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                                    textStyle: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600),
                                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(3)),
                                  ),
                                  child: const Text('Generate Report'),
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 12),
                          Expanded(
                            child: ListView(
                              physics: const AlwaysScrollableScrollPhysics(),
                              children: [
                                DataTable(
                                  columns: const [
                                    DataColumn(label: Text('Cost Date')),
                                    DataColumn(label: Text('Vehicle')),
                                    DataColumn(label: Text('Program')),
                                    DataColumn(label: Text('RM')),
                                  ],
                                  rows: [
                                    for (var c in costData.take(costShown))
                                      DataRow(cells: [
                                        DataCell(Text(c['date']!)),
                                        DataCell(Text(c['vehicle']!)),
                                        DataCell(Text(c['program']!)),
                                        DataCell(Text(c['amount']!)),
                                      ]),
                                  ],
                                ),
                                const SizedBox(height: 8),
                                Align(
                                  alignment: Alignment.center,
                                  child: ElevatedButton(
                                    onPressed: costShown < costData.length
                                        ? () => setState(() => costShown = (costShown + 10).clamp(0, costData.length))
                                        : null,
                                    style: ElevatedButton.styleFrom(
                                      backgroundColor: costShown < costData.length ? PastelColors.primary : PastelColors.textLight,
                                      foregroundColor: Colors.white,
                                      padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
                                      textStyle: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600),
                                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(3)),
                                    ),
                                    child: Text(costShown < costData.length ? 'Load More' : 'Finish'),
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
                // Driver Tab
                RefreshIndicator(
                  onRefresh: () async {
                    // Simulate fetching new driver data
                    await Future.delayed(Duration(seconds: 1));
                    if (mounted) {
                      setState(() {
                        // Refresh driver data (simulate for demo)
                        driverData.shuffle();
                      });
                    }
                  },
                  child: Padding(
                    padding: EdgeInsets.all(12),
                    child: Card(
                      color: PastelColors.cardBackground,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(3),
                        side: BorderSide(color: PastelColors.border, width: 1),
                      ),
                      child: Column(
                        children: [
                          Row(
                            children: [
                              Expanded(
                                child: Padding(
                                  padding: const EdgeInsets.only(left: 8, top: 12),
                                  child: _DatePickerField(
                                    hint: 'Search From',
                                    selected: driverFrom,
                                    onTap: () async {
                                      DateTime? picked = await showDatePicker(
                                        context: context,
                                        initialDate: driverFrom ?? DateTime.now(),
                                        firstDate: DateTime(2020),
                                        lastDate: DateTime(2100),
                                      );
                                      if (picked != null) setState(() => driverFrom = picked);
                                    },
                                  ),
                                ),
                              ),
                              const SizedBox(width: 8),
                              Expanded(
                                child: Padding(
                                  padding: const EdgeInsets.only(top: 12),
                                  child: _DatePickerField(
                                    hint: 'Search To',
                                    selected: driverTo,
                                    onTap: () async {
                                      DateTime? picked = await showDatePicker(
                                        context: context,
                                        initialDate: driverTo ?? DateTime.now(),
                                        firstDate: DateTime(2020),
                                        lastDate: DateTime(2100),
                                      );
                                      if (picked != null) setState(() => driverTo = picked);
                                    },
                                  ),
                                ),
                              ),
                              const SizedBox(width: 8),
                              Padding(
                                padding: const EdgeInsets.only(right: 8, top: 12),
                                child: ElevatedButton(
                                  onPressed: () {},
                                  style: ElevatedButton.styleFrom(
                                    backgroundColor: PastelColors.primary,
                                    foregroundColor: Colors.white,
                                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                                    textStyle: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600),
                                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(3)),
                                  ),
                                  child: const Text('Generate Report'),
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 12),
                          Expanded(
                            child: ListView(
                              physics: const AlwaysScrollableScrollPhysics(),
                              children: [
                                DataTable(
                                  columns: const [
                                    DataColumn(label: Text('Program')),
                                    DataColumn(label: Text('Check In')),
                                    DataColumn(label: Text('Check Out')),
                                    DataColumn(label: Text('Status')),
                                  ],
                                  rows: [
                                    for (var d in driverData.take(driverShown))
                                      DataRow(cells: [
                                        DataCell(Text(d['programs']!)),
                                        DataCell(Text(d['checkin']!)),
                                        DataCell(Text(d['checkout']!)),
                                        DataCell(Text(d['status']!)),
                                      ]),
                                  ],
                                ),
                                const SizedBox(height: 8),
                                Align(
                                  alignment: Alignment.center,
                                  child: ElevatedButton(
                                    onPressed: null,
                                    style: ElevatedButton.styleFrom(
                                      backgroundColor: PastelColors.textLight,
                                      foregroundColor: Colors.white,
                                      padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
                                      textStyle: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600),
                                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(3)),
                                    ),
                                    child: const Text('Finish'),
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
                // Help Tab
                RefreshIndicator(
                  onRefresh: () async {
                    // Simulate fetching new help data
                    await Future.delayed(Duration(seconds: 1));
                    // We can't directly modify the _faq in _HelpExpansionTileListState
                    // Instead, we'll just refresh the UI
                    if (mounted) {
                      setState(() {
                        // Just trigger a rebuild
                      });
                    }
                  },
                  child: Padding(
                    padding: EdgeInsets.all(12),
                    child: Card(
                      color: PastelColors.cardBackground,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(3),
                        side: BorderSide(color: PastelColors.border, width: 1),
                      ),
                      child: ListView(
                        physics: const AlwaysScrollableScrollPhysics(),
                        children: [
                          // FAQ Accordion
                          _HelpExpansionTileList(),
                          const SizedBox(height: 24),
                          // Submit Report Form
                          Padding(
                            padding: EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                            child: Text('Submit Report', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: PastelColors.primary)),
                          ),
                          Padding(
                            padding: EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                            child: TextField(
                              style: AppTextStyles.bodyMedium,
                              decoration: InputDecoration(
                                labelText: 'Subject',
                                labelStyle: AppTextStyles.bodyLarge,
                                border: OutlineInputBorder(borderRadius: BorderRadius.circular(3)),
                                isDense: true,
                              ),
                            ),
                          ),
                          Padding(
                            padding: EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                            child: TextField(
                              style: AppTextStyles.bodyMedium,
                              decoration: InputDecoration(
                                labelText: 'Category',
                                labelStyle: AppTextStyles.bodyLarge,
                                border: OutlineInputBorder(borderRadius: BorderRadius.circular(3)),
                                isDense: true,
                              ),
                            ),
                          ),
                          Padding(
                            padding: EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                            child: DropdownButtonFormField<String>(
                              dropdownColor: PastelColors.success,
                              decoration: InputDecoration(
                                labelText: 'Status',
                                labelStyle: AppTextStyles.bodyLarge,
                                border: OutlineInputBorder(borderRadius: BorderRadius.circular(3)),
                                isDense: true,
                              ),
                              items: const [
                                DropdownMenuItem(value: 'Low', child: Text('Low')),
                                DropdownMenuItem(value: 'High', child: Text('High')),
                                DropdownMenuItem(value: 'Critical', child: Text('Critical')),
                              ],
                              onChanged: (v) {},
                              style: AppTextStyles.bodyMedium,
                            ),
                          ),
                          Padding(
                            padding: EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                            child: TextField(
                              style: AppTextStyles.bodyMedium,
                              minLines: 3,
                              maxLines: 5,
                              decoration: InputDecoration(
                                labelText: 'Message',
                                labelStyle: AppTextStyles.bodyLarge,
                                border: OutlineInputBorder(borderRadius: BorderRadius.circular(3)),
                                isDense: true,
                              ),
                            ),
                          ),
                          Padding(
                            padding: EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                            child: ElevatedButton(
                              onPressed: () {},
                              style: ElevatedButton.styleFrom(
                                backgroundColor: PastelColors.primary,
                                foregroundColor: Colors.white,
                                padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 14),
                                textStyle: const TextStyle(fontSize: 14, fontWeight: FontWeight.w600),
                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(3)),
                              ),
                              child: const Text('Submit'),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _DatePickerField extends StatelessWidget {
  final String hint;
  final DateTime? selected;
  final VoidCallback onTap;
  const _DatePickerField({required this.hint, required this.selected, required this.onTap});

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(3),
      child: InputDecorator(
        decoration: InputDecoration(
          // labelText: label, // REMOVE label
          hintText: hint, // Use hint as placeholder
          border: OutlineInputBorder(borderRadius: BorderRadius.circular(3)),
          isDense: true,
          contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
        ),
        child: Text(
          selected != null ? "${selected!.year}-${selected!.month.toString().padLeft(2, '0')}-${selected!.day.toString().padLeft(2, '0')}" : hint,
          style: const TextStyle(fontSize: 13),
        ),
      ),
    );
  }
}

class _HelpExpansionTileList extends StatefulWidget {
  @override
  State<_HelpExpansionTileList> createState() => _HelpExpansionTileListState();
}

class _HelpExpansionTileListState extends State<_HelpExpansionTileList> {
  final List<Map<String, String>> _faq = [
    {
      'q': 'How to check in?',
      'a': 'To check in, go to the Do tab and tap the Check In card. You will be prompted to confirm your location and time. Make sure your GPS is enabled.\n\nSteps:\n• Open Do tab\n• Tap Check In\n• Confirm details\n• Submit',
    },
    {
      'q': 'How to claim cost?',
      'a': 'To claim cost, go to the Do tab and tap the Claim card. Fill in the required details and upload your receipt if needed.\n\nTips:\n• Ensure all fields are filled\n• Attach clear photo of receipt',
    },
    {
      'q': 'Who to contact for issues?',
      'a': 'For technical issues, please contact RISDA admin at 03-8888 8888 or email support@risda.gov.my.\n\nSupport hours: 8am - 5pm (Mon-Fri)',
    },
    {
      'q': 'How to view my program history?',
      'a': 'You can view your program history in the Overview or Report tab. Filter by date to see past programs.\n\n• Overview tab: quick stats\n• Report tab: detailed list',
    },
    {
      'q': 'How to reset my password?',
      'a': 'Go to Profile > Settings and select Reset Password. Follow the instructions sent to your email.\n\nIf you do not receive an email, check your spam folder.',
    },
  ];
  List<bool> _expanded = [false, false, false, false, false];

  @override
  Widget build(BuildContext context) {
    return ListView.builder(
      shrinkWrap: true,
      physics: NeverScrollableScrollPhysics(),
      itemCount: _faq.length,
      itemBuilder: (context, i) {
        return Card(
          margin: const EdgeInsets.symmetric(vertical: 2, horizontal: 0),
          elevation: 0,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(3), side: BorderSide(color: PastelColors.border)),
          child: Theme(
            data: Theme.of(context).copyWith(dividerColor: Colors.transparent),
            child: ExpansionTile(
              tilePadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 0),
              childrenPadding: const EdgeInsets.only(left: 20, right: 16, bottom: 12, top: 0),
              title: Text(_faq[i]['q']!, style: AppTextStyles.bodyLarge.copyWith(fontWeight: FontWeight.w600)),
              trailing: AnimatedRotation(
                turns: _expanded[i] ? 0.5 : 0.0,
                duration: const Duration(milliseconds: 200),
                child: const Icon(Icons.keyboard_arrow_down_rounded, size: 22),
              ),
              onExpansionChanged: (expanded) {
                setState(() {
                  _expanded[i] = expanded;
                });
              },
              initiallyExpanded: _expanded[i],
              children: [
                Text(_faq[i]['a']!, style: AppTextStyles.bodyMedium),
              ],
            ),
          ),
        );
      },
    );
  }
} 