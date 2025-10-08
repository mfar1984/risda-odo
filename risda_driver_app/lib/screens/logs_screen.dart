import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:fl_chart/fl_chart.dart';
import '../theme/pastel_colors.dart';
import '../theme/text_styles.dart';
import '../services/api_service.dart';
import '../core/api_client.dart';
import 'package:provider/provider.dart';
import '../services/connectivity_service.dart';
import '../services/hive_service.dart';
import '../models/journey_hive_model.dart';
import '../models/program_hive_model.dart';
import '../models/vehicle_hive_model.dart';

class LogsScreen extends StatefulWidget {
  const LogsScreen({super.key});

  @override
  State<LogsScreen> createState() => _LogsScreenState();
}

class _LogsScreenState extends State<LogsScreen> {
  final ApiService _apiService = ApiService(ApiClient());
  
  List<Map<String, dynamic>>? completedLogs;
  bool isLoading = true;
  String? errorMessage;
  
  // Date filters
  DateTime? fromDate;
  DateTime? toDate;
  
  // Pagination
  int logsShown = 10; // Display 10 logs initially
  
  @override
  void initState() {
    super.initState();
    _loadLogs();
  }
  
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
  
  // Helper to safely parse string/num to double
  double _parseDouble(dynamic value) {
    if (value == null) return 0.0;
    if (value is double) return value;
    if (value is int) return value.toDouble();
    if (value is String) {
      return double.tryParse(value) ?? 0.0;
    }
    return 0.0;
  }
  
  Future<void> _loadLogs() async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });
    
    try {
      final isOnline = mounted ? context.read<ConnectivityService>().isOnline : true;
      if (isOnline) {
        // Try API first
        final response = await _apiService.getLogs();
        if (response['success'] == true) {
          List<Map<String, dynamic>> allLogs = List<Map<String, dynamic>>.from(response['data'] ?? []);
          // Client-side date filter
          if (fromDate != null && toDate != null) {
            allLogs = allLogs.where((log) {
              if (log['tarikh_perjalanan'] == null) return false;
              try {
                final logDate = DateTime.parse(log['tarikh_perjalanan']);
                return logDate.isAfter(fromDate!.subtract(const Duration(days: 1))) &&
                       logDate.isBefore(toDate!.add(const Duration(days: 1)));
              } catch (e) {
                return false;
              }
            }).toList();
          }
          setState(() {
            completedLogs = allLogs;
            logsShown = 10;
            isLoading = false;
          });
          return;
        }
        // If API returns error, fall through to offline fallback
      }

      // OFFLINE or API failed → build logs from Hive cache
      final journeys = HiveService.getAllJourneys();
      final programs = {for (final p in HiveService.getAllPrograms()) p.id: p};
      final vehicles = {for (final v in HiveService.getAllVehicles()) v.id: v};

      List<Map<String, dynamic>> mapped = journeys.map((JourneyHive j) {
        // Compose ISO datetime for times
        DateTime startDt;
        try {
          final hm = (j.masaKeluar).split(':');
          startDt = DateTime(j.tarikhPerjalanan.year, j.tarikhPerjalanan.month, j.tarikhPerjalanan.day,
              int.tryParse(hm[0]) ?? 0, int.tryParse(hm.length > 1 ? hm[1] : '0') ?? 0);
        } catch (_) {
          startDt = DateTime(j.tarikhPerjalanan.year, j.tarikhPerjalanan.month, j.tarikhPerjalanan.day, 0, 0);
        }
        DateTime? endDt;
        if (j.masaMasuk != null) {
          try {
            final hm = j.masaMasuk!.split(':');
            endDt = DateTime(j.tarikhPerjalanan.year, j.tarikhPerjalanan.month, j.tarikhPerjalanan.day,
                int.tryParse(hm[0]) ?? 0, int.tryParse(hm.length > 1 ? hm[1] : '0') ?? 0);
          } catch (_) {}
        }

        final p = programs[j.programId];
        Map<String, dynamic>? programMap = p == null
            ? null
            : {
                'id': p.id,
                'nama_program': p.namaProgram,
                'lokasi_program': p.lokasi,
              };

        // Fallback to cached program detail if needed (location/vehicle)
        if (programMap == null || programMap['lokasi_program'] == null) {
          try {
            final c = HiveService.settingsBox.get('program_detail_${j.programId}');
            if (c is Map) {
              final cm = Map<String, dynamic>.from(c);
              programMap ??= {};
              programMap!['id'] = j.programId;
              programMap!['nama_program'] ??= cm['nama_program'];
              programMap!['lokasi_program'] ??= cm['lokasi_program'];
            }
          } catch (_) {}
        }

        Map<String, dynamic>? vehicleMap;
        final v = vehicles[j.kenderaanId];
        if (v != null) {
          vehicleMap = {
            'id': v.id,
            'no_plat': v.noPendaftaran,
            'jenama': v.jenisKenderaan,
            'model': v.model,
          };
        } else {
          // If vehicle not in vehicleBox, try program_detail cache
          try {
            final c = HiveService.settingsBox.get('program_detail_${j.programId}');
            if (c is Map && c['kenderaan'] is Map) {
              final kv = Map<String, dynamic>.from(c['kenderaan']);
              vehicleMap = {
                'id': kv['id'],
                'no_plat': kv['no_plat'],
                'jenama': kv['jenama'],
                'model': kv['model'],
              };
            }
          } catch (_) {}
        }

        return {
          'id': j.id ?? j.localId,
          'program': programMap,
          'kenderaan': vehicleMap,
          'tarikh_perjalanan': j.tarikhPerjalanan.toIso8601String(),
          'masa_keluar': startDt.toIso8601String(),
          if (endDt != null) 'masa_masuk': endDt.toIso8601String(),
          'status': j.status,
          'odometer_keluar': j.odometerKeluar,
          'odometer_masuk': j.odometerMasuk,
          'kos_minyak': j.kosMinyak,
          'liter_minyak': j.literMinyak,
          'stesen_minyak': j.stesenMinyak,
          'catatan': j.catatan,
        };
      }).toList();

      // Client-side filter by date
      if (fromDate != null && toDate != null) {
        mapped = mapped.where((log) {
          if (log['tarikh_perjalanan'] == null) return false;
          try {
            final logDate = DateTime.parse(log['tarikh_perjalanan']);
            return logDate.isAfter(fromDate!.subtract(const Duration(days: 1))) &&
                   logDate.isBefore(toDate!.add(const Duration(days: 1)));
          } catch (e) {
            return false;
          }
        }).toList();
      }

      setState(() {
        completedLogs = mapped;
        logsShown = 10;
        isLoading = false;
        errorMessage = null; // no error banner offline
      });
    } catch (e) {
      setState(() {
        // As final fallback, show nothing but no scary error
        completedLogs = [];
        errorMessage = null;
        isLoading = false;
      });
    }
  }
  
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: PastelColors.primary,
        title: Text('Log Perjalanan', style: AppTextStyles.h2.copyWith(color: Colors.white)),
        iconTheme: const IconThemeData(color: Colors.white),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      backgroundColor: PastelColors.background,
      body: Column(
        children: [
          // Analytics Chart
          Card(
            color: Colors.white,
            elevation: 3,
            shadowColor: PastelColors.primary.withOpacity(0.15),
            margin: const EdgeInsets.all(12),
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
                          rightTitles: AxisTitles(sideTitles: SideTitles(showTitles: false)),
                          topTitles: AxisTitles(sideTitles: SideTitles(showTitles: false)),
                        ),
                        borderData: FlBorderData(show: false),
                        lineBarsData: [
                          LineChartBarData(
                            spots: [
                              FlSpot(0, 8), FlSpot(1, 10), FlSpot(2, 7), FlSpot(3, 12),
                              FlSpot(4, 11), FlSpot(5, 13), FlSpot(6, 12), FlSpot(7, 14),
                              FlSpot(8, 10), FlSpot(9, 15), FlSpot(10, 13), FlSpot(11, 17),
                            ],
                            isCurved: true,
                            color: PastelColors.primary,
                            barWidth: 3,
                            dotData: FlDotData(show: false),
                            belowBarData: BarAreaData(show: false),
                          ),
                          LineChartBarData(
                            spots: [
                              FlSpot(0, 6), FlSpot(1, 8), FlSpot(2, 5), FlSpot(3, 9),
                              FlSpot(4, 8), FlSpot(5, 10), FlSpot(6, 9), FlSpot(7, 11),
                              FlSpot(8, 8), FlSpot(9, 12), FlSpot(10, 10), FlSpot(11, 14),
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
          
          // Date Filters + Generate Log
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 12),
            child: Row(
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
                        controller: TextEditingController(
                          text: fromDate == null
                              ? ''
                              : '${fromDate!.year}-${fromDate!.month.toString().padLeft(2, '0')}-${fromDate!.day.toString().padLeft(2, '0')}',
                        ),
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
                        controller: TextEditingController(
                          text: toDate == null
                              ? ''
                              : '${toDate!.year}-${toDate!.month.toString().padLeft(2, '0')}-${toDate!.day.toString().padLeft(2, '0')}',
                        ),
                        style: AppTextStyles.bodyLarge,
                      ),
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                ElevatedButton(
                  onPressed: () {
                    if (fromDate != null && toDate != null) {
                      _loadLogs();
                    } else {
                      ScaffoldMessenger.of(context).showSnackBar(
                        const SnackBar(
                          content: Text('Sila pilih tarikh mula dan tamat'),
                          backgroundColor: Colors.orange,
                        ),
                      );
                    }
                  },
                  style: ElevatedButton.styleFrom(
                    backgroundColor: PastelColors.primary,
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 16),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(3)),
                  ),
                  child: Text('Generate', style: AppTextStyles.bodyLarge.copyWith(color: Colors.white)),
                ),
              ],
            ),
          ),
          const SizedBox(height: 12),
          
          // List Content
          Expanded(
            child: isLoading
                ? Center(
                    child: CircularProgressIndicator(
                      color: PastelColors.primary,
                    ),
                  )
                : errorMessage != null
                    ? Center(
                        child: Padding(
                          padding: const EdgeInsets.all(16.0),
                          child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(
                                Icons.error_outline,
                                size: 60,
                                color: PastelColors.error,
                              ),
                              const SizedBox(height: 16),
                              Text(
                                errorMessage!,
                                textAlign: TextAlign.center,
                                style: AppTextStyles.bodyLarge.copyWith(
                                  color: PastelColors.error,
                                ),
                              ),
                              const SizedBox(height: 16),
                              ElevatedButton(
                                onPressed: _loadLogs,
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: PastelColors.primary,
                                  foregroundColor: Colors.white,
                                ),
                                child: const Text('Cuba Lagi'),
                              ),
                            ],
                          ),
                        ),
                      )
                    : (completedLogs == null || completedLogs!.isEmpty)
                        ? Center(
                            child: Column(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                Icon(
                                  Icons.inbox_outlined,
                                  size: 60,
                                  color: Colors.grey[400],
                                ),
                                const SizedBox(height: 16),
                                Text(
                                  'Tiada log perjalanan',
                                  style: AppTextStyles.bodyLarge.copyWith(
                                    color: PastelColors.textSecondary,
                                  ),
                                ),
                              ],
                            ),
                          )
                        : RefreshIndicator(
                            onRefresh: _loadLogs,
                            color: PastelColors.primary,
                            child: ListView(
                              padding: const EdgeInsets.all(16),
                              children: [
                                // Display logs (limited by logsShown)
                                ...completedLogs!.take(logsShown).map((log) => _buildLogCard(log)),
                                
                                const SizedBox(height: 16),
                                
                                // Load More / Finish Button
                                Center(
                                  child: ElevatedButton(
                                    onPressed: logsShown < completedLogs!.length
                                        ? () => setState(() => logsShown = (logsShown + 10).clamp(0, completedLogs!.length))
                                        : null,
                                    style: ElevatedButton.styleFrom(
                                      backgroundColor: logsShown < completedLogs!.length 
                                          ? PastelColors.primary 
                                          : PastelColors.textLight,
                                      foregroundColor: Colors.white,
                                      padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
                                      textStyle: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600),
                                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(3)),
                                    ),
                                    child: Text(logsShown < completedLogs!.length ? 'Load More' : 'Finish'),
                                  ),
                                ),
                                
                                const SizedBox(height: 16),
                              ],
                            ),
                          ),
          ),
        ],
      ),
    );
  }
  
  Widget _buildLogCard(Map<String, dynamic> log) {
    final program = log['program'] as Map<String, dynamic>?;
    final kenderaan = log['kenderaan'] as Map<String, dynamic>?;
    
    final tarikh = log['tarikh_perjalanan'] != null 
        ? DateFormat('dd/MM/yyyy').format(DateTime.parse(log['tarikh_perjalanan']))
        : '-';
    
    final masaKeluar = log['masa_keluar'] != null
        ? DateFormat('HH:mm').format(DateTime.parse(log['masa_keluar']))
        : '-';
    
    final masaMasuk = log['masa_masuk'] != null
        ? DateFormat('HH:mm').format(DateTime.parse(log['masa_masuk']))
        : '-';
    
    final statusColor = _getStatusColor(log['status']);
    final statusText = _getStatusText(log['status']);
    
    final odometerKeluar = log['odometer_keluar'] ?? 0;
    final odometerMasuk = log['odometer_masuk'] ?? 0;
    final jarakSebenar = odometerMasuk - odometerKeluar;
    
    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      color: PastelColors.cardBackground,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(8),
        side: BorderSide(color: PastelColors.border),
      ),
      elevation: 0,
      child: InkWell(
        onTap: () => _showLogDetails(log),
        borderRadius: BorderRadius.circular(8),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Header: Program Name + Status
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Expanded(
                    child: Text(
                      program?['nama_program'] ?? 'Tiada Program',
                      style: AppTextStyles.h3.copyWith(
                        color: PastelColors.textPrimary,
                      ),
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(
                      color: statusColor.withOpacity(0.1),
                      borderRadius: BorderRadius.circular(3),
                    ),
                    child: Text(
                      statusText,
                      style: AppTextStyles.bodySmall.copyWith(
                        color: statusColor,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ),
                ],
              ),
              
              const SizedBox(height: 12),
              
              // Info Grid
              Row(
                children: [
                  Expanded(
                    child: _buildInfoItem(
                      Icons.calendar_today,
                      'Tarikh',
                      tarikh,
                    ),
                  ),
                  Expanded(
                    child: _buildInfoItem(
                      Icons.access_time,
                      'Masa',
                      '$masaKeluar - $masaMasuk',
                    ),
                  ),
                ],
              ),
              
              const SizedBox(height: 8),
              
              Row(
                children: [
                  Expanded(
                    child: _buildInfoItem(
                      Icons.directions_car,
                      'Kenderaan',
                      kenderaan?['no_plat'] ?? '-',
                    ),
                  ),
                  Expanded(
                    child: _buildInfoItem(
                      Icons.speed,
                      'Jarak',
                      jarakSebenar > 0 ? '${jarakSebenar.toStringAsFixed(1)} km' : '-',
                    ),
                  ),
                ],
              ),
              
              // Cost & Fuel Info (if available)
              if (log['kos_minyak'] != null || log['liter_minyak'] != null) ...[
                const SizedBox(height: 8),
                Container(
                  padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(
                    color: Colors.blue.withOpacity(0.05),
                    borderRadius: BorderRadius.circular(4),
                    border: Border.all(color: Colors.blue.withOpacity(0.1)),
                  ),
                  child: Row(
                    children: [
                      Icon(Icons.local_gas_station, size: 16, color: Colors.blue[700]),
                      const SizedBox(width: 8),
                      Text(
                        'Kos: RM ${_parseDouble(log['kos_minyak']).toStringAsFixed(2)}',
                        style: AppTextStyles.bodySmall.copyWith(
                          color: Colors.blue[700],
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                      const SizedBox(width: 16),
                      Text(
                        'Liter: ${_parseDouble(log['liter_minyak']).toStringAsFixed(2)} L',
                        style: AppTextStyles.bodySmall.copyWith(
                          color: Colors.blue[700],
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ],
          ),
        ),
      ),
    );
  }
  
  Widget _buildInfoItem(IconData icon, String label, String value) {
    return Row(
      children: [
        Icon(icon, size: 14, color: PastelColors.textSecondary),
        const SizedBox(width: 6),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                label,
                style: AppTextStyles.bodySmall.copyWith(
                  color: PastelColors.textSecondary,
                  fontSize: 10,
                ),
              ),
              Text(
                value,
                style: AppTextStyles.bodySmall.copyWith(
                  color: PastelColors.textPrimary,
                  fontWeight: FontWeight.w600,
                ),
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
              ),
            ],
          ),
        ),
      ],
    );
  }
  
  Color _getStatusColor(String? status) {
    switch (status) {
      case 'selesai':
        return PastelColors.success;
      case 'dalam_perjalanan':
        return PastelColors.warning;
      case 'dibatalkan':
        return PastelColors.error;
      default:
        return Colors.grey;
    }
  }
  
  String _getStatusText(String? status) {
    switch (status) {
      case 'selesai':
        return 'Selesai';
      case 'dalam_perjalanan':
        return 'Dalam Perjalanan';
      case 'dibatalkan':
        return 'Dibatalkan';
      default:
        return 'Tidak Diketahui';
    }
  }
  
  void _showLogDetails(Map<String, dynamic> log) {
    final program = log['program'] as Map<String, dynamic>?;
    final kenderaan = log['kenderaan'] as Map<String, dynamic>?;
    
    final tarikh = log['tarikh_perjalanan'] != null 
        ? DateFormat('dd/MM/yyyy').format(DateTime.parse(log['tarikh_perjalanan']))
        : '-';
    
    final masaKeluar = log['masa_keluar'] != null
        ? DateFormat('HH:mm').format(DateTime.parse(log['masa_keluar']))
        : '-';
    
    final masaMasuk = log['masa_masuk'] != null
        ? DateFormat('HH:mm').format(DateTime.parse(log['masa_masuk']))
        : '-';
    
    final odometerKeluar = log['odometer_keluar'] ?? 0;
    final odometerMasuk = log['odometer_masuk'] ?? 0;
    final jarakSebenar = odometerMasuk - odometerKeluar;
    
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) => Container(
        height: MediaQuery.of(context).size.height * 0.75,
        decoration: BoxDecoration(
          color: PastelColors.cardBackground,
          borderRadius: const BorderRadius.vertical(top: Radius.circular(20)),
        ),
        child: Column(
          children: [
            // Handle bar
            Container(
              margin: const EdgeInsets.symmetric(vertical: 12),
              width: 40,
              height: 4,
              decoration: BoxDecoration(
                color: Colors.grey[300],
                borderRadius: BorderRadius.circular(2),
              ),
            ),
            
            // Header
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 20),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    'Butiran Log',
                    style: AppTextStyles.h2.copyWith(
                      color: PastelColors.textPrimary,
                    ),
                  ),
                  IconButton(
                    onPressed: () => Navigator.pop(context),
                    icon: const Icon(Icons.close),
                    color: PastelColors.textSecondary,
                  ),
                ],
              ),
            ),
            
            const Divider(),
            
            // Content
            Expanded(
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(20),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    _buildDetailSection('Program', [
                      _buildDetailRow('Nama Program', program?['nama_program'] ?? '-'),
                      _buildDetailRow('Lokasi', program?['lokasi_program'] ?? '-'),
                    ]),
                    
                    const SizedBox(height: 16),
                    
                    _buildDetailSection('Kenderaan', [
                      _buildDetailRow('No. Plat', kenderaan?['no_plat'] ?? '-'),
                      _buildDetailRow('Jenama/Model', '${kenderaan?['jenama'] ?? ''} ${kenderaan?['model'] ?? ''}'.trim()),
                    ]),
                    
                    const SizedBox(height: 16),
                    
                    _buildDetailSection('Perjalanan', [
                      _buildDetailRow('Tarikh', tarikh),
                      _buildDetailRow('Masa Keluar', masaKeluar),
                      _buildDetailRow('Masa Masuk', masaMasuk),
                      _buildDetailRow('Odometer Keluar', '$odometerKeluar km'),
                      _buildDetailRow('Odometer Masuk', '$odometerMasuk km'),
                      _buildDetailRow('Jarak Sebenar', '${jarakSebenar.toStringAsFixed(1)} km'),
                    ]),
                    
                    if (log['kos_minyak'] != null || log['liter_minyak'] != null) ...[
                      const SizedBox(height: 16),
                      _buildDetailSection('Bahan Api', [
                        _buildDetailRow('Kos Minyak', 'RM ${_parseDouble(log['kos_minyak']).toStringAsFixed(2)}'),
                        _buildDetailRow('Liter Minyak', '${_parseDouble(log['liter_minyak']).toStringAsFixed(2)} L'),
                        if (log['stesen_minyak'] != null) _buildDetailRow('Stesen Minyak', log['stesen_minyak']),
                      ]),
                    ],
                    
                    if (log['catatan'] != null && log['catatan'].toString().isNotEmpty) ...[
                      const SizedBox(height: 16),
                      _buildDetailSection('Catatan', [
                        Text(
                          log['catatan'],
                          style: AppTextStyles.bodyLarge.copyWith(
                            color: PastelColors.textPrimary,
                          ),
                        ),
                      ]),
                    ],
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
  
  Widget _buildDetailSection(String title, List<Widget> children) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          title,
          style: AppTextStyles.h3.copyWith(
            color: PastelColors.textPrimary,
          ),
        ),
        const SizedBox(height: 8),
        Container(
          padding: const EdgeInsets.all(12),
          decoration: BoxDecoration(
            color: Colors.grey[50],
            borderRadius: BorderRadius.circular(8),
            border: Border.all(color: PastelColors.border),
          ),
          child: Column(
            children: children,
          ),
        ),
      ],
    );
  }
  
  Widget _buildDetailRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 6),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
            width: 120,
            child: Text(
              label,
              style: AppTextStyles.bodyMedium.copyWith(
                color: PastelColors.textSecondary,
              ),
            ),
          ),
          const SizedBox(width: 8),
          Expanded(
            child: Text(
              value,
              style: AppTextStyles.bodyMedium.copyWith(
                color: PastelColors.textPrimary,
                fontWeight: FontWeight.w600,
              ),
            ),
          ),
        ],
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
        const SizedBox(width: 6),
        Text(label, style: AppTextStyles.bodySmall),
      ],
    );
  }
}
