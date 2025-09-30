import 'package:flutter/material.dart';
import 'package:fl_chart/fl_chart.dart';
import 'package:intl/intl.dart';
import '../theme/pastel_colors.dart';
import '../theme/text_styles.dart';

class LogsScreen extends StatefulWidget {
  const LogsScreen({super.key});

  @override
  State<LogsScreen> createState() => _LogsScreenState();
}

class _LogsScreenState extends State<LogsScreen> {
  DateTime? fromDate;
  DateTime? toDate;
  bool isGenerating = false;

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

  Future<void> _generateLog() async {
    if (fromDate == null || toDate == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Sila pilih tarikh mula dan tamat'),
          backgroundColor: Colors.orange,
        ),
      );
      return;
    }

    setState(() {
      isGenerating = true;
    });

    // 🎨 DUMMY MODE - Simulate generating log
    await Future.delayed(const Duration(seconds: 2));

    if (mounted) {
      setState(() {
        isGenerating = false;
      });

      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Log berjaya dijana! (Dummy Mode)'),
          backgroundColor: Colors.green,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final fromDateFormatted = fromDate == null
        ? ''
        : DateFormat('yyyy-MM-dd').format(fromDate!);
    final toDateFormatted = toDate == null
        ? ''
        : DateFormat('yyyy-MM-dd').format(toDate!);

    return Scaffold(
      appBar: AppBar(
        backgroundColor: PastelColors.primary,
        title: Text('Logs', style: AppTextStyles.h2.copyWith(color: Colors.white)),
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      backgroundColor: PastelColors.background,
      body: RefreshIndicator(
        onRefresh: () async {
          // 🎨 DUMMY MODE - Refresh data
          setState(() {});
        },
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
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
                                spots: const [
                                  FlSpot(0, 8),
                                  FlSpot(1, 10),
                                  FlSpot(2, 7),
                                  FlSpot(3, 12),
                                  FlSpot(4, 11),
                                  FlSpot(5, 13),
                                  FlSpot(6, 12),
                                  FlSpot(7, 14),
                                  FlSpot(8, 10),
                                  FlSpot(9, 15),
                                  FlSpot(10, 13),
                                  FlSpot(11, 17),
                                ],
                                isCurved: true,
                                color: PastelColors.primary,
                                barWidth: 3,
                                dotData: const FlDotData(show: false),
                                belowBarData: BarAreaData(show: false),
                              ),
                              LineChartBarData(
                                spots: const [
                                  FlSpot(0, 6),
                                  FlSpot(1, 8),
                                  FlSpot(2, 5),
                                  FlSpot(3, 9),
                                  FlSpot(4, 8),
                                  FlSpot(5, 10),
                                  FlSpot(6, 9),
                                  FlSpot(7, 11),
                                  FlSpot(8, 8),
                                  FlSpot(9, 12),
                                  FlSpot(10, 10),
                                  FlSpot(11, 14),
                                ],
                                isCurved: true,
                                color: PastelColors.accent,
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
                          _buildLegend('Trip', PastelColors.primary),
                          _buildLegend('Cost', PastelColors.accent),
                        ],
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 12),

              // Date Range Picker and Generate Log Button
              Row(
                children: [
                  // From Date
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
                          controller: TextEditingController(text: fromDateFormatted),
                          style: AppTextStyles.bodyLarge,
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  
                  // To Date
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
                          controller: TextEditingController(text: toDateFormatted),
                          style: AppTextStyles.bodyLarge,
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  
                  // Generate Log Button
                  ElevatedButton(
                    onPressed: isGenerating ? null : _generateLog,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: PastelColors.primary,
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 16),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(3)),
                    ),
                    child: isGenerating
                        ? const SizedBox(
                            height: 16,
                            width: 16,
                            child: CircularProgressIndicator(
                              strokeWidth: 2,
                              valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                            ),
                          )
                        : Text(
                            'Generate Log',
                            style: AppTextStyles.bodyLarge.copyWith(color: Colors.white),
                          ),
                  ),
                ],
              ),
              const SizedBox(height: 18),

              // Summary DataTable
              Card(
                color: Colors.white,
                elevation: 2,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(3),
                  side: BorderSide(color: PastelColors.border),
                ),
                child: Padding(
                  padding: const EdgeInsets.all(12),
                  child: SingleChildScrollView(
                    scrollDirection: Axis.horizontal,
                    child: DataTable(
                      headingTextStyle: AppTextStyles.bodyMedium.copyWith(
                        fontWeight: FontWeight.bold,
                        color: PastelColors.textPrimary,
                      ),
                      dataTextStyle: AppTextStyles.bodyMedium,
                      columns: const [
                        DataColumn(label: Text('Total Trips')),
                        DataColumn(label: Text('KM')),
                        DataColumn(label: Text('Hours')),
                        DataColumn(label: Text('Cost')),
                      ],
                      rows: const [
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

              // Log Details List
              Text('Log Details', style: AppTextStyles.h2),
              const SizedBox(height: 8),
              
              // 🎨 DUMMY DATA - Dummy Log Cards
              ...List.generate(
                3,
                (i) => Card(
                  color: PastelColors.cardBackground,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(6),
                    side: BorderSide(color: PastelColors.border),
                  ),
                  elevation: 0,
                  margin: const EdgeInsets.only(bottom: 10),
                  child: ListTile(
                    leading: Icon(Icons.directions_car, color: PastelColors.primary),
                    title: Text('Trip #${i + 1}', style: AppTextStyles.bodyLarge),
                    subtitle: Text(
                      '2024-07-1${i + 1} | 40 KM | 2h | RM 30.00',
                      style: AppTextStyles.bodyMedium,
                    ),
                    trailing: Icon(Icons.chevron_right, color: PastelColors.textLight),
                    onTap: () {
                      // TODO: Navigate to log detail
                    },
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
        Container(
          width: 12,
          height: 12,
          decoration: BoxDecoration(color: color, shape: BoxShape.circle),
        ),
        const SizedBox(width: 6),
        Text(label, style: AppTextStyles.bodySmall),
      ],
    );
  }
}