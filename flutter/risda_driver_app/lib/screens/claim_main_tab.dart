import 'package:flutter/material.dart';
import 'package:fl_chart/fl_chart.dart';
import '../theme/pastel_colors.dart';
import '../theme/text_styles.dart';
import 'claim_screen.dart';

class ClaimMainTab extends StatefulWidget {
  const ClaimMainTab({super.key});

  @override
  State<ClaimMainTab> createState() => _ClaimMainTabState();
}

class _ClaimMainTabState extends State<ClaimMainTab> with SingleTickerProviderStateMixin {
  late TabController _tabController;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 4, vsync: this);
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: PastelColors.background,
      appBar: AppBar(
        backgroundColor: PastelColors.primary,
        elevation: 0,
        title: Row(
          children: [
            Icon(Icons.receipt_long, color: Colors.white, size: 20),
            const SizedBox(width: 8),
            Text('Claim', style: AppTextStyles.h2.copyWith(color: Colors.white)),
          ],
        ),
        actions: [
          Padding(
            padding: const EdgeInsets.only(right: 12),
            child: ElevatedButton.icon(
              onPressed: () {
                Navigator.push(context, MaterialPageRoute(builder: (_) => ClaimScreen()));
              },
              icon: Icon(Icons.add, size: 18, color: Colors.white),
              label: Text('Create Claim', style: AppTextStyles.bodyLarge.copyWith(color: Colors.white)),
              style: ElevatedButton.styleFrom(
                backgroundColor: PastelColors.accent,
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                textStyle: AppTextStyles.bodyLarge,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(3)),
                elevation: 0,
              ),
            ),
          ),
        ],
        bottom: TabBar(
          controller: _tabController,
          labelColor: Colors.white,
          unselectedLabelColor: Colors.white70,
          indicatorColor: Colors.white,
          tabs: const [
            Tab(text: 'Total'),
            Tab(text: 'Pending'),
            Tab(text: 'Amend'),
            Tab(text: 'Approve'),
          ],
        ),
      ),
      body: Column(
        children: [
          // Analytics Line Chart (reuse from OverviewTab)
          Padding(
            padding: const EdgeInsets.all(12),
            child: Card(
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
                              color: PastelColors.primary,
                              barWidth: 3,
                              dotData: FlDotData(show: false),
                              belowBarData: BarAreaData(show: false),
                            ),
                            LineChartBarData(
                              spots: [
                                FlSpot(0, 5), FlSpot(1, 7), FlSpot(2, 6), FlSpot(3, 8), FlSpot(4, 7), FlSpot(5, 9), FlSpot(6, 8), FlSpot(7, 10), FlSpot(8, 7), FlSpot(9, 11), FlSpot(10, 9), FlSpot(11, 13),
                              ],
                              isCurved: true,
                              color: PastelColors.accent,
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
                        _buildLegend('Claim', PastelColors.primary),
                        _buildLegend('Total Kos', PastelColors.accent),
                      ],
                    ),
                  ],
                ),
              ),
            ),
          ),
          // TabView for claims
          Expanded(
            child: TabBarView(
              controller: _tabController,
              children: [
                _buildClaimList('Total'),
                _buildClaimList('Pending'),
                _buildClaimList('Amend'),
                _buildClaimList('Approve'),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildLegend(String label, Color color) {
    return Row(
      children: [
        Container(width: 12, height: 12, decoration: BoxDecoration(color: color, shape: BoxShape.circle)),
        const SizedBox(width: 6),
        Text(label, style: AppTextStyles.bodySmall),
      ],
    );
  }

  Widget _buildClaimList(String type) {
    // Dummy data for claims
    final claims = List.generate(4, (i) => {
      'title': 'Claim #${i + 1} ($type)',
      'amount': 'RM ${(i + 1) * 50}.00',
      'status': type,
      'date': '2024-07-1${i + 1}',
    });
    return ListView.separated(
      padding: const EdgeInsets.all(12),
      itemCount: claims.length,
      separatorBuilder: (_, __) => const SizedBox(height: 8),
      itemBuilder: (context, i) {
        final claim = claims[i];
        return Card(
          color: PastelColors.cardBackground,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(6), side: BorderSide(color: PastelColors.border)),
          elevation: 0,
          child: ListTile(
            leading: Icon(Icons.receipt, color: PastelColors.primary),
            title: Text(claim['title']!, style: AppTextStyles.bodyLarge),
            subtitle: Text('Date: ${claim['date']}'),
            trailing: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Text(claim['amount']!, style: AppTextStyles.bodyLarge.copyWith(color: PastelColors.primary)),
                Text(claim['status']!, style: AppTextStyles.bodySmall),
              ],
            ),
            onTap: () {
              // TODO: Navigate to claim detail or edit
            },
          ),
        );
      },
    );
  }
} 