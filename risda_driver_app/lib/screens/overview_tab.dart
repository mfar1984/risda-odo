import 'package:flutter/material.dart';
import 'package:fl_chart/fl_chart.dart';
import 'package:provider/provider.dart';
import '../theme/pastel_colors.dart';
import '../theme/text_styles.dart';
import '../services/api_service.dart';
import '../services/connectivity_service.dart';
import '../core/api_client.dart';
 

class OverviewTab extends StatefulWidget {
  const OverviewTab({super.key});

  @override
  State<OverviewTab> createState() => _OverviewTabState();
}

class _OverviewTabState extends State<OverviewTab> {
  late final ApiService _apiService;
  
  // Dashboard Stats
  int _totalTrips = 0;
  double _totalDistance = 0.0;
  double _fuelCost = 0.0;
  double _maintenanceCost = 0.0;
  double _parkingCost = 0.0;
  double _fnbCost = 0.0;
  double _accommodationCost = 0.0;
  double _othersCost = 0.0;
  double _tripsChange = 0.0;
  double _distanceChange = 0.0;
  double _fuelCostChange = 0.0;
  double _maintenanceChange = 0.0;
  double _parkingChange = 0.0;
  double _fnbChange = 0.0;
  double _accommodationChange = 0.0;
  double _othersChange = 0.0;
  
  // Chart Data
  String _chartPeriod = '6months';
  List<FlSpot> _fuelChartData = [];
  List<FlSpot> _claimsChartData = [];
  List<String> _chartLabels = [];
  double _chartMaxY = 100;
  
  bool _isLoading = true;
  ConnectivityService? _connectivityService;
  VoidCallback? _connectivityListener;

  @override
  void initState() {
    super.initState();
    _apiService = ApiService(ApiClient());
    
    // Load data after frame built (to access context)
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _setupConnectivityListener();
      _loadData();
    });
  }
  
  /// Setup listener untuk connectivity changes
  void _setupConnectivityListener() {
    // Detach previous if any (defensive against hot reloads)
    if (_connectivityService != null && _connectivityListener != null) {
      _connectivityService!.removeListener(_connectivityListener!);
    }
    final svc = context.read<ConnectivityService>();
    _connectivityService = svc;
    _connectivityListener = () {
      if (!mounted) return; // Guard against unmounted state
      if (svc.isOnline && _isLoading) {
        // Back online while still loading - retry
        _loadData();
      }
    };
    svc.addListener(_connectivityListener!);
  }

  @override
  void dispose() {
    // Remove connectivity listener to avoid callbacks after unmount
    if (_connectivityService != null && _connectivityListener != null) {
      _connectivityService!.removeListener(_connectivityListener!);
    }
    super.dispose();
  }

  Future<void> _loadData() async {
    // Check online first
    final connectivity = context.read<ConnectivityService>();
    if (!connectivity.isOnline) {
      if (mounted) setState(() => _isLoading = false);
      return;
    }
    if (mounted) setState(() => _isLoading = true);
    
    await Future.wait([
      _loadDashboardStats(),
      _loadChartData(),
    ]);
    if (mounted) setState(() => _isLoading = false);
  }

  Future<void> _loadDashboardStats() async {
    try {
      final response = await _apiService.getDashboardStatistics();
      
      if (response['success'] == true && response['data'] != null) {
        final data = response['data'];
        
        if (mounted) {
          setState(() {
            _totalTrips = data['total_trips'] ?? 0;
            _totalDistance = (data['total_distance'] ?? 0.0).toDouble();
            _fuelCost = (data['fuel_cost'] ?? 0.0).toDouble();
            _maintenanceCost = (data['maintenance_cost'] ?? 0.0).toDouble();
            _parkingCost = (data['parking_cost'] ?? 0.0).toDouble();
            _fnbCost = (data['fnb_cost'] ?? 0.0).toDouble();
            _accommodationCost = (data['accommodation_cost'] ?? 0.0).toDouble();
            _othersCost = (data['others_cost'] ?? 0.0).toDouble();
            _tripsChange = (data['total_trips_change'] ?? 0.0).toDouble();
            _distanceChange = (data['total_distance_change'] ?? 0.0).toDouble();
            _fuelCostChange = (data['fuel_cost_change'] ?? 0.0).toDouble();
            _maintenanceChange = (data['maintenance_change'] ?? 0.0).toDouble();
            _parkingChange = (data['parking_change'] ?? 0.0).toDouble();
            _fnbChange = (data['fnb_change'] ?? 0.0).toDouble();
            _accommodationChange = (data['accommodation_change'] ?? 0.0).toDouble();
            _othersChange = (data['others_change'] ?? 0.0).toDouble();
          });
        }
      }
    } catch (e) {
      // Don't rethrow, just log - stats are optional
    }
  }

  Future<void> _loadChartData() async {
    try {
      final response = await _apiService.getOverviewChartData(period: _chartPeriod);
      
      if (response['success'] == true && response['data'] != null) {
        final data = response['data'];
        final chartData = data['chart_data'] as List;
        
        if (mounted) {
          setState(() {
            _chartLabels = chartData.map((item) => item['label'] as String).toList();
            
            _fuelChartData = chartData.asMap().entries.map((entry) {
              final value = double.tryParse(entry.value['fuel_cost'].toString()) ?? 0.0;
              return FlSpot(entry.key.toDouble(), value);
            }).toList();
            
            _claimsChartData = chartData.asMap().entries.map((entry) {
              final value = double.tryParse(entry.value['claims'].toString()) ?? 0.0;
              return FlSpot(entry.key.toDouble(), value);
            }).toList();
            
            // Calculate max Y for chart scaling
            final maxFuel = _fuelChartData.map((spot) => spot.y).reduce((a, b) => a > b ? a : b);
            final maxClaims = _claimsChartData.map((spot) => spot.y).reduce((a, b) => a > b ? a : b);
            _chartMaxY = (maxFuel > maxClaims ? maxFuel : maxClaims) * 1.2; // Add 20% padding
            if (_chartMaxY < 100) _chartMaxY = 100; // Minimum scale
          });
        }
      }
    } catch (e) {
      // Use empty data on error
    }
  }

  void _toggleChartPeriod() {
    setState(() {
      _chartPeriod = _chartPeriod == '6months' ? '1month' : '6months';
    });
    _loadChartData();
  }

  @override
  Widget build(BuildContext context) {
    return RefreshIndicator(
      onRefresh: _loadData,
      child: SingleChildScrollView(
        physics: const AlwaysScrollableScrollPhysics(),
        child: Padding(
          padding: const EdgeInsets.all(8),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Analytics Card
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
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Row(
                            children: [
                              Icon(Icons.bar_chart, color: PastelColors.primary, size: 20),
                              const SizedBox(width: 8),
                              Text('Cost Analytics (RM)', style: AppTextStyles.h2),
                            ],
                          ),
                          OutlinedButton(
                            onPressed: _toggleChartPeriod,
                            style: OutlinedButton.styleFrom(
                              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                              minimumSize: Size.zero,
                              tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                            ),
                            child: Text(
                              _chartPeriod == '6months' ? '6 Months' : '1 Month',
                              style: AppTextStyles.bodySmall,
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 8),
                      SizedBox(
                        height: 160,
                        child: LineChart(
                          LineChartData(
                            gridData: FlGridData(
                              show: true,
                              drawVerticalLine: false,
                              horizontalInterval: 1,
                              getDrawingHorizontalLine: (value) => FlLine(
                                color: PastelColors.divider,
                                strokeWidth: 1,
                              ),
                            ),
                            titlesData: FlTitlesData(
                              leftTitles: AxisTitles(
                                sideTitles: SideTitles(
                                  showTitles: true,
                                  reservedSize: 28,
                                  getTitlesWidget: (value, meta) => Text(
                                    value.toInt().toString(),
                                    style: AppTextStyles.bodySmall,
                                  ),
                                ),
                              ),
                              bottomTitles: AxisTitles(
                                sideTitles: SideTitles(
                                  showTitles: true,
                                  reservedSize: 28,
                                  getTitlesWidget: (value, meta) {
                                    if (value.toInt() < 0 || value.toInt() >= _chartLabels.length) {
                                      return const Text('');
                                    }
                                    return Text(
                                      _chartLabels[value.toInt()],
                                      style: AppTextStyles.bodySmall,
                                    );
                                  },
                                ),
                              ),
                              rightTitles: const AxisTitles(
                                sideTitles: SideTitles(showTitles: false),
                              ),
                              topTitles: const AxisTitles(
                                sideTitles: SideTitles(showTitles: false),
                              ),
                            ),
                            borderData: FlBorderData(show: false),
                            lineBarsData: [
                              LineChartBarData(
                                spots: _fuelChartData,
                                isCurved: true,
                                color: Colors.orange, // Fuel Cost = Orange
                                barWidth: 3,
                                dotData: const FlDotData(show: false),
                                belowBarData: BarAreaData(show: false),
                              ),
                              LineChartBarData(
                                spots: _claimsChartData,
                                isCurved: true,
                                color: PastelColors.success, // Claims = Green
                                barWidth: 3,
                                dotData: const FlDotData(show: false),
                                belowBarData: BarAreaData(show: false),
                              ),
                            ],
                            lineTouchData: const LineTouchData(enabled: true),
                            minY: 0,
                            maxY: _chartMaxY,
                          ),
                        ),
                      ),
                      const SizedBox(height: 8),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                        children: [
                          _buildLegend('Fuel Cost', Colors.orange),
                          _buildLegend('Total Claims', PastelColors.success),
                        ],
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 8),
              // Stats Cards
              GridView.count(
                crossAxisCount: 2,
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                mainAxisSpacing: 6,
                crossAxisSpacing: 6,
                childAspectRatio: 1.5,
                children: [
                  _buildStatCard(
                    title: 'Total Trips',
                    value: _totalTrips.toString(),
                    icon: Icons.directions_car,
                    color: PastelColors.success,
                    subtitle: '${_tripsChange >= 0 ? '+' : ''}${_tripsChange.toStringAsFixed(1)}% from last month',
                  ),
                  _buildStatCard(
                    title: 'Total Distance',
                    value: '${_totalDistance.toStringAsFixed(0)} km',
                    icon: Icons.map,
                    color: PastelColors.warning,
                    subtitle: '${_distanceChange >= 0 ? '+' : ''}${_distanceChange.toStringAsFixed(1)}% from last month',
                  ),
                  _buildStatCard(
                    title: 'Fuel Cost',
                    value: 'RM ${_fuelCost.toStringAsFixed(2)}',
                    icon: Icons.local_gas_station,
                    color: PastelColors.info,
                    subtitle: '${_fuelCostChange >= 0 ? '+' : ''}${_fuelCostChange.toStringAsFixed(1)}% from last month',
                  ),
                  _buildStatCard(
                    title: 'Maintenance',
                    value: 'RM ${_maintenanceCost.toStringAsFixed(2)}',
                    icon: Icons.build,
                    color: PastelColors.error,
                    subtitle: '${_maintenanceChange >= 0 ? '+' : ''}${_maintenanceChange.toStringAsFixed(1)}% from last month',
                  ),
                  _buildStatCard(
                    title: 'Parking',
                    value: 'RM ${_parkingCost.toStringAsFixed(2)}',
                    icon: Icons.local_parking,
                    color: Color(0xFF9C27B0), // Purple
                    subtitle: '${_parkingChange >= 0 ? '+' : ''}${_parkingChange.toStringAsFixed(1)}% from last month',
                  ),
                  _buildStatCard(
                    title: 'F&B',
                    value: 'RM ${_fnbCost.toStringAsFixed(2)}',
                    icon: Icons.restaurant,
                    color: Color(0xFFFF9800), // Orange
                    subtitle: '${_fnbChange >= 0 ? '+' : ''}${_fnbChange.toStringAsFixed(1)}% from last month',
                  ),
                  _buildStatCard(
                    title: 'Accommodation',
                    value: 'RM ${_accommodationCost.toStringAsFixed(2)}',
                    icon: Icons.hotel,
                    color: Color(0xFF00BCD4), // Cyan
                    subtitle: '${_accommodationChange >= 0 ? '+' : ''}${_accommodationChange.toStringAsFixed(1)}% from last month',
                  ),
                  _buildStatCard(
                    title: 'Others',
                    value: 'RM ${_othersCost.toStringAsFixed(2)}',
                    icon: Icons.more_horiz,
                    color: Color(0xFF607D8B), // Blue Grey
                    subtitle: '${_othersChange >= 0 ? '+' : ''}${_othersChange.toStringAsFixed(1)}% from last month',
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildLegend(String label, Color color) {
    return Row(
      children: [
        Container(
          width: 12,
          height: 12,
          decoration: BoxDecoration(
            color: color,
            shape: BoxShape.circle,
          ),
        ),
        const SizedBox(width: 4),
        Text(label, style: AppTextStyles.bodyMedium),
      ],
    );
  }
  
  Widget _buildStatCard({
    required String title,
    required String value,
    required IconData icon,
    required Color color,
    required String subtitle,
  }) {
    return Card(
      color: Colors.white,
      elevation: 2,
      shadowColor: color.withOpacity(0.1),
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(8),
        side: BorderSide(color: color.withOpacity(0.3), width: 1),
      ),
      child: Padding(
        padding: const EdgeInsets.all(12),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Row(
              children: [
                Icon(icon, color: color, size: 20),
                const SizedBox(width: 8),
                Text(title, style: AppTextStyles.bodyMedium),
              ],
            ),
            const SizedBox(height: 8),
            Text(value, style: AppTextStyles.h2),
            const SizedBox(height: 4),
            Text(
              subtitle,
              style: AppTextStyles.bodySmall.copyWith(
                color: PastelColors.textLight,
              ),
            ),
          ],
        ),
      ),
    );
  }
}