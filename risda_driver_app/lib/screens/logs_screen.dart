import 'package:flutter/material.dart';
import 'package:fl_chart/fl_chart.dart';
import '../theme/pastel_colors.dart';
import '../theme/text_styles.dart';

class LogsScreen extends StatefulWidget {
  @override
  State<LogsScreen> createState() => _LogsScreenState();
}

class _LogsScreenState extends State<LogsScreen> {
  DateTime? fromDate;
  DateTime? toDate;

  Future<void> _pickDate(BuildContext context, bool isFrom) async {
    final picked = await showDatePicker(
      context: context,
      initialDate: DateTime.now(),
      firstDate: DateTime(2020),
      lastDate: DateTime(2100),
    );
    if (picked != null) {
      setState(() {
        if (isFrom) {
          fromDate = picked;
        } else {
          toDate = picked;
        }
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: PastelColors.primary,
        title: Text('Logs', style: AppTextStyles.h2.copyWith(color: Colors.white)),
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      backgroundColor: PastelColors.background,
      body: RefreshIndicator(
        onRefresh: () async {
          // Refresh data
          setState(() {
            // In a real app, this would fetch new data
          });
        },
        child: SingleChildScrollView(
          physics: AlwaysScrollableScrollPhysics(),
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 24),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Analytics Line Chart
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
                                  FlSpot(0, 8), FlSpot(1, 10), FlSpot(2, 7), FlSpot(3, 12), FlSpot(4, 11), FlSpot(5, 13), FlSpot(6, 12), FlSpot(7, 14), FlSpot(8, 10), FlSpot(9, 15), FlSpot(10, 13), FlSpot(11, 17),
                                ],
                                isCurved: true,
                                color: PastelColors.primary,
                                barWidth: 3,
                                dotData: FlDotData(show: false),
                                belowBarData: BarAreaData(show: false),
                              ),
                              LineChartBarData(
                                spots: [
                                  FlSpot(0, 6), FlSpot(1, 8), FlSpot(2, 5), FlSpot(3, 9), FlSpot(4, 8), FlSpot(5, 10), FlSpot(6, 9), FlSpot(7, 11), FlSpot(8, 8), FlSpot(9, 12), FlSpot(10, 10), FlSpot(11, 14),
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
                            maxY: 20,
                          ),
                        ),
                      ),
                      const SizedBox(height: 8),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                        children: [
                          _buildLegend('Trip', PastelColors.primary),
                          _buildLegend('Cost', PastelColors.accent),
                        ],
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 12),
              // Search From/To and Generate Log
              Row(
                children: [
                  Expanded(
                    child: GestureDetector(
                      onTap: () => _pickDate(context, true),
                      child: AbsorbPointer(
                        child: TextField(
                          decoration: InputDecoration(
                            labelText: 'From',
                            hintText: 'Select date',
                            border: OutlineInputBorder(borderRadius: BorderRadius.circular(3)),
                            isDense: true,
                            contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                            prefixIcon: Icon(Icons.date_range, color: PastelColors.primary, size: 20),
                          ),
                          controller: TextEditingController(text: fromDate == null ? '' : '${fromDate!.year}-${fromDate!.month.toString().padLeft(2, '0')}-${fromDate!.day.toString().padLeft(2, '0')}'),
                          style: AppTextStyles.bodyLarge,
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: GestureDetector(
                      onTap: () => _pickDate(context, false),
                      child: AbsorbPointer(
                        child: TextField(
                          decoration: InputDecoration(
                            labelText: 'To',
                            hintText: 'Select date',
                            border: OutlineInputBorder(borderRadius: BorderRadius.circular(3)),
                            isDense: true,
                            contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                            prefixIcon: Icon(Icons.date_range, color: PastelColors.primary, size: 20),
                          ),
                          controller: TextEditingController(text: toDate == null ? '' : '${toDate!.year}-${toDate!.month.toString().padLeft(2, '0')}-${toDate!.day.toString().padLeft(2, '0')}'),
                          style: AppTextStyles.bodyLarge,
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  ElevatedButton(
                    onPressed: () {},
                    style: ElevatedButton.styleFrom(
                      backgroundColor: PastelColors.primary,
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 16),
                      textStyle: AppTextStyles.bodyLarge,
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(3)),
                    ),
                    child: Text('Generate Log', style: AppTextStyles.bodyLarge.copyWith(color: Colors.white)),
                  ),
                ],
              ),
              const SizedBox(height: 18),
              // Table
              Card(
                color: Colors.white,
                elevation: 2,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(3), side: BorderSide(color: PastelColors.border)),
                child: Padding(
                  padding: const EdgeInsets.all(12),
                  child: SingleChildScrollView(
                    scrollDirection: Axis.horizontal,
                    child: DataTable(
                      columns: const [
                        DataColumn(label: Text('Total Trips')),
                        DataColumn(label: Text('KM')),
                        DataColumn(label: Text('Hours')),
                        DataColumn(label: Text('Cost')),
                      ],
                      rows: [
                        DataRow(cells: [
                          DataCell(Text('8')),
                          DataCell(Text('320')),
                          DataCell(Text('14')),
                          DataCell(Text('RM 210.00')),
                        ]),
                      ],
                    ),
                  ),
                ),
              ),
              const SizedBox(height: 18),
              // Dummy Log Cards
              Text('Log Details', style: AppTextStyles.h2),
              const SizedBox(height: 8),
              ...List.generate(3, (i) => Card(
                color: PastelColors.cardBackground,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(6), side: BorderSide(color: PastelColors.border)),
                elevation: 0,
                margin: const EdgeInsets.only(bottom: 10),
                child: ListTile(
                  leading: Icon(Icons.directions_car, color: PastelColors.primary),
                  title: Text('Trip #${i + 1}', style: AppTextStyles.bodyLarge),
                  subtitle: Text('2024-07-1${i + 1} | 40 KM | 2h | RM 30.00'),
                  trailing: Icon(Icons.chevron_right, color: PastelColors.textLight),
                  onTap: () {},
                ),
              )),
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
        const SizedBox(width: 6),
        Text(label, style: AppTextStyles.bodySmall),
      ],
    );
  }
} 