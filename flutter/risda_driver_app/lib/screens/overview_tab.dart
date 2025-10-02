import 'package:flutter/material.dart';
import 'package:fl_chart/fl_chart.dart';
import '../theme/pastel_colors.dart';
import '../theme/text_styles.dart';
import '../main.dart';
import '../utils/debug_utils.dart';

class OverviewTab extends StatelessWidget {
  const OverviewTab({super.key});

  @override
  Widget build(BuildContext context) {
    return RefreshIndicator(
      onRefresh: () async {
        // Dummy refresh, can be replaced with real data fetch
        await Future.delayed(const Duration(seconds: 1));
      },
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
                        children: [
                          Icon(Icons.bar_chart, color: PastelColors.primary, size: 20),
                          const SizedBox(width: 8),
                          Text('Analytics', style: AppTextStyles.h2),
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
                                spots: [
                                  FlSpot(0, 10), FlSpot(1, 12), FlSpot(2, 8), FlSpot(3, 15), FlSpot(4, 13), FlSpot(5, 17), FlSpot(6, 14), FlSpot(7, 16), FlSpot(8, 12), FlSpot(9, 18), FlSpot(10, 15), FlSpot(11, 20),
                                ],
                                isCurved: true,
                                color: PastelColors.success, // Claim = Green card
                                barWidth: 3,
                                dotData: FlDotData(show: false),
                                belowBarData: BarAreaData(show: false),
                              ),
                              LineChartBarData(
                                spots: [
                                  FlSpot(0, 5), FlSpot(1, 7), FlSpot(2, 6), FlSpot(3, 8), FlSpot(4, 7), FlSpot(5, 9), FlSpot(6, 8), FlSpot(7, 10), FlSpot(8, 7), FlSpot(9, 11), FlSpot(10, 9), FlSpot(11, 13),
                                ],
                                isCurved: true,
                                color: PastelColors.warning, // Total Kos = Yellow card
                                barWidth: 3,
                                dotData: FlDotData(show: false),
                                belowBarData: BarAreaData(show: false),
                              ),
                            ],
                            lineTouchData: LineTouchData(enabled: true),
                            minY: 0,
                            maxY: 25,
                          ),
                        ),
                      ),
                      const SizedBox(height: 8),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                        children: [
                          _buildLegend('Claim', PastelColors.success),
                          _buildLegend('Total Kos', PastelColors.warning),
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
                    value: '124',
                    icon: Icons.directions_car,
                    color: PastelColors.success,
                    subtitle: '+12% from last month',
                  ),
                  _buildStatCard(
                    title: 'Total Distance',
                    value: '1,432 km',
                    icon: Icons.map,
                    color: PastelColors.warning,
                    subtitle: '+8% from last month',
                  ),
                  _buildStatCard(
                    title: 'Fuel Cost',
                    value: 'RM 1,245',
                    icon: Icons.local_gas_station,
                    color: PastelColors.info,
                    subtitle: '-3% from last month',
                  ),
                  _buildStatCard(
                    title: 'Maintenance',
                    value: 'RM 450',
                    icon: Icons.build,
                    color: PastelColors.error,
                    subtitle: '+5% from last month',
                  ),
                ],
              ),
              const SizedBox(height: 8),
              // Program Card
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
                  padding: const EdgeInsets.all(8),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        children: [
                          Icon(Icons.event_note, color: PastelColors.primary, size: 20),
                          const SizedBox(width: 8),
                          Text('Program', style: AppTextStyles.h2),
                        ],
                      ),
                      const SizedBox(height: 8),
                      DefaultTabController(
                        length: 3,
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.stretch,
                          children: [
                            TabBar(
                              labelColor: PastelColors.primary,
                              unselectedLabelColor: PastelColors.textLight,
                              indicatorColor: PastelColors.primary,
                              tabs: const [
                                Tab(text: 'Current'),
                                Tab(text: 'Ongoing'),
                                Tab(text: 'Past'),
                              ],
                            ),
                            SizedBox(
                              height: 160,
                              child: TabBarView(
                                children: [
                                  _buildProgramList([
                                    ProgramDummy(
                                      name: 'Program Jelajah Madani',
                                      status: 'Aktif',
                                      statusColor: PastelColors.primary,
                                      date: '11-7-2025 9:00AM - 13-7-2025, 16:00PM',
                                    ),
                                    ProgramDummy(
                                      name: 'Program Inovasi Desa',
                                      status: 'Aktif',
                                      statusColor: PastelColors.primary,
                                      date: '15-7-2025 8:00AM - 16-7-2025, 17:00PM',
                                    ),
                                  ]),
                                  _buildProgramList([
                                    ProgramDummy(
                                      name: 'Program Komuniti Hijau',
                                      status: 'Tertunda',
                                      statusColor: PastelColors.warning,
                                      date: '20-7-2025 10:00AM - 21-7-2025, 15:00PM',
                                    ),
                                    ProgramDummy(
                                      name: 'Program Sukan Rakyat',
                                      status: 'Tertunda',
                                      statusColor: PastelColors.warning,
                                      date: '22-7-2025 9:00AM - 23-7-2025, 16:00PM',
                                    ),
                                  ]),
                                  _buildProgramList([
                                    ProgramDummy(
                                      name: 'Program Gotong Royong',
                                      status: 'Selesai',
                                      statusColor: PastelColors.textLight,
                                      date: '1-7-2025 8:00AM - 2-7-2025, 12:00PM',
                                    ),
                                    ProgramDummy(
                                      name: 'Program Derma Darah',
                                      status: 'Selesai',
                                      statusColor: PastelColors.textLight,
                                      date: '3-7-2025 9:00AM - 3-7-2025, 13:00PM',
                                    ),
                                  ]),
                                ],
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
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
        Container(width: 12, height: 12, decoration: BoxDecoration(color: color, shape: BoxShape.circle)),
        const SizedBox(width: 4),
        Text(label, style: AppTextStyles.bodyMedium),
      ],
    );
  }

  Widget _buildProgramList(List<ProgramDummy> programs) {
    return ListView.separated(
      padding: const EdgeInsets.symmetric(vertical: 3, horizontal: 0),
      itemCount: programs.length,
      separatorBuilder: (_, __) => const SizedBox(height: 3),
      itemBuilder: (context, i) {
        final p = programs[i];
        return Card(
          margin: EdgeInsets.zero,
          elevation: 0.5,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(6)),
          color: PastelColors.background,
          child: Padding(
            padding: const EdgeInsets.all(8),
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        children: [
                          Expanded(
                            child: Text(p.name, style: AppTextStyles.h3),
                          ),
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                            decoration: BoxDecoration(
                              color: p.status == 'Selesai'
                                  ? PastelColors.error.withOpacity(0.15)
                                  : p.statusColor.withOpacity(0.15),
                              borderRadius: BorderRadius.circular(8),
                            ),
                            child: Text(
                              p.status,
                              style: AppTextStyles.bodySmall.copyWith(
                                color: p.status == 'Selesai'
                                    ? PastelColors.errorText
                                    : p.statusColor,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 4),
                      Text(p.date, style: AppTextStyles.bodyMedium),
                    ],
                  ),
                ),
              ],
            ),
          ),
        );
      },
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

class ProgramDummy {
  final String name;
  final String status;
  final Color statusColor;
  final String date;
  ProgramDummy({required this.name, required this.status, required this.statusColor, required this.date});
} 