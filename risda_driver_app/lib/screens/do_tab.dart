import 'package:flutter/material.dart';
import 'package:fl_chart/fl_chart.dart';
import '../theme/pastel_colors.dart';
import '../theme/text_styles.dart';
import 'checkin_screen.dart';
import 'checkout_screen.dart';
import 'claim_main_tab.dart';
import 'logs_screen.dart';

class DoTab extends StatefulWidget {
  const DoTab({super.key});

  @override
  State<DoTab> createState() => _DoTabState();
}

class _DoTabState extends State<DoTab> {
  // Analytics data that can be refreshed
  List<FlSpot> checkInData = [
    const FlSpot(0, 8),
    const FlSpot(1, 10),
    const FlSpot(2, 7),
    const FlSpot(3, 12),
    const FlSpot(4, 11),
    const FlSpot(5, 13),
    const FlSpot(6, 12),
    const FlSpot(7, 14),
    const FlSpot(8, 10),
    const FlSpot(9, 15),
    const FlSpot(10, 13),
    const FlSpot(11, 17),
  ];

  List<FlSpot> checkOutData = [
    const FlSpot(0, 6),
    const FlSpot(1, 8),
    const FlSpot(2, 5),
    const FlSpot(3, 9),
    const FlSpot(4, 8),
    const FlSpot(5, 10),
    const FlSpot(6, 9),
    const FlSpot(7, 11),
    const FlSpot(8, 8),
    const FlSpot(9, 12),
    const FlSpot(10, 10),
    const FlSpot(11, 14),
  ];

  Future<void> _refreshData() async {
    // Simulate fetching new data
    await Future.delayed(const Duration(seconds: 1));

    // Update with "new" data (for demo purposes)
    setState(() {
      // Slightly modify the data to simulate refresh
      for (int i = 0; i < checkInData.length; i++) {
        final randomChange = ([-0.5, 0, 0.5]..shuffle()).first;
        checkInData[i] = FlSpot(checkInData[i].x, checkInData[i].y + randomChange);
        checkOutData[i] = FlSpot(checkOutData[i].x, checkOutData[i].y + randomChange);
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
                                    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                                    return Text(
                                      months[value.toInt() % 12],
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
                                spots: checkInData,
                                isCurved: true,
                                color: PastelColors.success, // Check In = Green card
                                barWidth: 3,
                                dotData: const FlDotData(show: false),
                                belowBarData: BarAreaData(show: false),
                              ),
                              LineChartBarData(
                                spots: checkOutData,
                                isCurved: true,
                                color: PastelColors.warning, // Check Out = Yellow card
                                barWidth: 3,
                                dotData: const FlDotData(show: false),
                                belowBarData: BarAreaData(show: false),
                              ),
                            ],
                            lineTouchData: const LineTouchData(enabled: true),
                            minY: 0,
                            maxY: 20,
                          ),
                        ),
                      ),
                      const SizedBox(height: 8),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                        children: [
                          _buildLegend('Start Journey', PastelColors.success),
                          _buildLegend('End Journey', PastelColors.warning),
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
                  // Start Journey: Green card, white icon
                  _buildActionCard(
                    Icons.play_arrow,
                    'Start Journey',
                    Colors.white, // icon color
                    textColor: Colors.white, // text color
                    bgColor: PastelColors.success,
                    borderColor: PastelColors.successText,
                    onTap: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(builder: (_) => const CheckInScreen()),
                      );
                    },
                  ),
                  // End Journey: Yellow card, white icon
                  _buildActionCard(
                    Icons.stop,
                    'End Journey',
                    Colors.white, // icon color
                    textColor: Colors.white, // text color
                    bgColor: PastelColors.warning,
                    borderColor: PastelColors.warningText,
                    onTap: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(builder: (_) => const CheckOutScreen()),
                      );
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
                      Navigator.push(
                        context,
                        MaterialPageRoute(builder: (_) => const ClaimMainTab()),
                      );
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
                      Navigator.push(
                        context,
                        MaterialPageRoute(builder: (_) => const LogsScreen()),
                      );
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

  Widget _buildActionCard(
    IconData icon,
    String label,
    Color color, {
    Color? bgColor,
    Color? borderColor,
    Color? textColor,
    VoidCallback? onTap,
  }) {
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

  const _InteractiveCard({
    required this.icon,
    required this.label,
    required this.color,
    required this.textColor,
    required this.bgColor,
    required this.borderColor,
    this.onTap,
  });

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
              ? [
                  BoxShadow(
                    color: widget.color.withOpacity(0.25),
                    blurRadius: 12,
                    offset: const Offset(0, 4),
                  )
                ]
              : [
                  BoxShadow(
                    color: widget.color.withOpacity(0.10),
                    blurRadius: 4,
                    offset: const Offset(0, 2),
                  )
                ],
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
                  shadows: const [
                    Shadow(
                      color: Colors.black26,
                      blurRadius: 2,
                      offset: Offset(0, 1),
                    ),
                  ],
                ),
                const SizedBox(height: 10),
                Text(
                  widget.label,
                  style: AppTextStyles.bodyLarge.copyWith(
                    color: widget.textColor,
                    fontWeight: FontWeight.w600,
                    shadows: const [
                      Shadow(
                        color: Colors.black26,
                        blurRadius: 2,
                        offset: Offset(0, 1),
                      ),
                    ],
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
