import 'package:flutter/material.dart';
import 'package:fl_chart/fl_chart.dart';
import 'package:provider/provider.dart';
import '../theme/pastel_colors.dart';
import '../theme/text_styles.dart';
import '../services/api_service.dart';
import '../services/hive_service.dart';
import '../services/auth_service.dart';
import '../models/program_hive_model.dart';
import 'package:intl/intl.dart';
import '../services/connectivity_service.dart';
import '../core/api_client.dart';
import 'checkin_screen.dart';
import 'checkout_screen.dart';
import 'claim_main_tab.dart';
import 'logs_screen.dart';
import 'program_detail_screen.dart';
 

class DoTab extends StatefulWidget {
  const DoTab({super.key});

  @override
  State<DoTab> createState() => _DoTabState();
}

class _DoTabState extends State<DoTab> {
  late final ApiService _apiService;
  
  List<dynamic> _currentPrograms = [];
  List<dynamic> _ongoingPrograms = [];
  List<dynamic> _pastPrograms = [];
  
  bool _isLoading = true;
  String? _errorMessage;
  
  // Chart Data
  String _chartPeriod = '6months';
  List<FlSpot> _startJourneyData = [];
  List<FlSpot> _endJourneyData = [];
  List<String> _chartLabels = [];
  double _chartMaxY = 20;

  @override
  void initState() {
    super.initState();
    _apiService = ApiService(ApiClient());
    
    // Load after frame built
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _loadData();
    });
  }

  Future<void> _loadData() async {
    // Check online first
    final connectivity = context.read<ConnectivityService>();
    if (!connectivity.isOnline) {
      await _loadProgramsOffline();
      // Chart stays empty offline for now
      return;
    }

    await Future.wait([
      _loadPrograms(),
      _loadChartData(),
    ]);
  }

  Future<void> _loadPrograms() async {
    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    try {
      // Fetch all three program categories
      final currentResponse = await _apiService.getPrograms(status: 'current');
      final ongoingResponse = await _apiService.getPrograms(status: 'ongoing');
      final pastResponse = await _apiService.getPrograms(status: 'past');

      if (currentResponse['success'] == true) {
        _currentPrograms = currentResponse['data'] ?? [];
      }

      if (ongoingResponse['success'] == true) {
        _ongoingPrograms = ongoingResponse['data'] ?? [];
      }

      if (pastResponse['success'] == true) {
        _pastPrograms = pastResponse['data'] ?? [];
      }

      if (mounted) {
        setState(() {
          _isLoading = false;
        });
      }
    } catch (e) {
      // Fallback to offline cache
      await _loadProgramsOffline();
    }
  }

  Future<void> _loadProgramsOffline() async {
    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    try {
      final auth = context.read<AuthService>();
      final now = DateTime.now();
      // Collect identifiers for assignment (user id and staf id)
      final Set<String> driverIdSet = {};
      if (auth.userId != null) driverIdSet.add(auth.userId!.toString());
      try {
        final user = auth.currentUser;
        final stafId = user['user']?['staf']?['id'] ?? user['staf']?['id'];
        if (stafId != null) driverIdSet.add(stafId.toString());
      } catch (_) {}

      List<ProgramHive> all = HiveService.getAllPrograms();
      // Only assigned to current driver
      all = all.where((p) {
        if (p.pemanduId == null || p.pemanduId!.trim().isEmpty) return false;
        final assignedIds = p.pemanduId!
            .split(',')
            .map((e) => e.trim())
            .where((e) => e.isNotEmpty)
            .toSet();
        return driverIdSet.any((id) => assignedIds.contains(id));
      }).toList();

      String? statusLabelFor(ProgramHive p) {
        try {
          final cached = HiveService.settingsBox.get('program_detail_${p.id}');
          if (cached is Map && cached['status_label'] is String) {
            return (cached['status_label'] as String);
          }
        } catch (_) {}
        return p.status; // fallback raw status
      }

      bool isOngoing(ProgramHive p) {
        final within = (p.tarikhMula == null || !now.isBefore(p.tarikhMula!)) &&
            (p.tarikhTamat == null || !now.isAfter(p.tarikhTamat!));
        final label = (statusLabelFor(p) ?? '').toLowerCase();
        return label.contains('aktif') || within;
      }

      bool isPast(ProgramHive p) {
        final label = (statusLabelFor(p) ?? '').toLowerCase();
        if (label.contains('selesai')) return true;
        if (p.tarikhTamat != null && now.isAfter(p.tarikhTamat!)) return true;
        return false;
      }

      String fmt(DateTime? d) => d == null ? '' : DateFormat('dd/MM/yyyy HH:mm').format(d);

      List<Map<String, dynamic>> mapForUI(Iterable<ProgramHive> list) {
        return list.map((p) {
          // Try to get start/end date from cached program detail first (more authoritative)
          String startStr = '';
          String endStr = '';
          try {
            final detail = HiveService.getSetting('program_detail_${p.id}');
            if (detail is Map) {
              DateTime? start;
              DateTime? end;
              // common keys
              if (detail['tarikh_mula'] != null) {
                start = DateTime.tryParse(detail['tarikh_mula'].toString());
              } else if (detail['tarikh_mula_aktif'] != null) {
                start = DateTime.tryParse(detail['tarikh_mula_aktif'].toString());
              }
              if (detail['tarikh_selesai'] != null) {
                end = DateTime.tryParse(detail['tarikh_selesai'].toString());
              } else if (detail['tarikh_sebenar_selesai'] != null) {
                end = DateTime.tryParse(detail['tarikh_sebenar_selesai'].toString());
              }
              startStr = fmt(start);
              endStr = fmt(end);
            }
          } catch (_) {}

          // Fallback to ProgramHive stored dates if cached detail missing
          if (startStr.isEmpty) startStr = fmt(p.tarikhMula);
          if (endStr.isEmpty) endStr = fmt(p.tarikhTamat);

          return {
            'id': p.id,
            'nama_program': p.namaProgram,
            'status_label': statusLabelFor(p) ?? '-',
            'tarikh_mula_formatted': startStr,
            'tarikh_selesai_formatted': endStr,
          };
        }).toList();
      }

      // If buckets from sync exist, prefer them to match server filter semantics
      List<int> bucketCurrent = List<int>.from(HiveService.getSetting('program_bucket_current', defaultValue: []) ?? []);
      List<int> bucketOngoing = List<int>.from(HiveService.getSetting('program_bucket_ongoing', defaultValue: []) ?? []);
      List<int> bucketPast = List<int>.from(HiveService.getSetting('program_bucket_past', defaultValue: []) ?? []);

      List<ProgramHive> current;
      List<ProgramHive> ongoing;
      List<ProgramHive> past;
      if (bucketCurrent.isNotEmpty || bucketOngoing.isNotEmpty || bucketPast.isNotEmpty) {
        final byId = {for (final p in all) p.id: p};
        current = bucketCurrent.map((id) => byId[id]).whereType<ProgramHive>().toList();
        ongoing = bucketOngoing.map((id) => byId[id]).whereType<ProgramHive>().toList();
        past = bucketPast.map((id) => byId[id]).whereType<ProgramHive>().toList();
      } else {
        ongoing = all.where(isOngoing).toList();
        past = all.where(isPast).toList();
        current = all.where((p) => !isOngoing(p) && !isPast(p)).toList();
      }

      if (mounted) {
        setState(() {
          _currentPrograms = mapForUI(current);
          _ongoingPrograms = mapForUI(ongoing);
          _pastPrograms = mapForUI(past);
          _isLoading = false;
          _errorMessage = null;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _isLoading = false;
          _errorMessage = 'Offline cache not available';
        });
      }
    }
  }

  Future<void> _loadChartData() async {
    // Check connectivity first
    final connectivity = context.read<ConnectivityService>();
    if (!connectivity.isOnline) {
      
      return;
    }
    
    try {
      final response = await _apiService.getDoActivityChartData(period: _chartPeriod);
      
      if (response['success'] == true && response['data'] != null) {
        final data = response['data'];
        final chartData = data['chart_data'] as List;
        
        if (mounted) {
          setState(() {
            _chartLabels = chartData.map((item) => item['label'] as String).toList();
            
            _startJourneyData = chartData.asMap().entries.map((entry) {
              final value = (entry.value['start_journey'] as num).toDouble();
              return FlSpot(entry.key.toDouble(), value);
            }).toList();
            
            _endJourneyData = chartData.asMap().entries.map((entry) {
              final value = (entry.value['end_journey'] as num).toDouble();
              return FlSpot(entry.key.toDouble(), value);
            }).toList();
            
            // Calculate max Y for chart scaling
            final maxStart = _startJourneyData.isNotEmpty 
                ? _startJourneyData.map((spot) => spot.y).reduce((a, b) => a > b ? a : b) 
                : 0;
            final maxEnd = _endJourneyData.isNotEmpty 
                ? _endJourneyData.map((spot) => spot.y).reduce((a, b) => a > b ? a : b) 
                : 0;
            _chartMaxY = (maxStart > maxEnd ? maxStart : maxEnd) * 1.2; // Add 20% padding
            if (_chartMaxY < 10) _chartMaxY = 10; // Minimum scale
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

  Future<void> _refreshData() async {
    await _loadData();
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
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Row(
                            children: [
                              Icon(Icons.analytics, color: PastelColors.primary, size: 20),
                              const SizedBox(width: 8),
                              Text('Trip Activity', style: AppTextStyles.h2),
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
                                spots: _startJourneyData,
                                isCurved: true,
                                color: PastelColors.success, // Start Journey = Green
                                barWidth: 3,
                                dotData: const FlDotData(show: false),
                                belowBarData: BarAreaData(show: false),
                              ),
                              LineChartBarData(
                                spots: _endJourneyData,
                                isCurved: true,
                                color: Colors.orange, // End Journey = Orange
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
                          _buildLegend('Start Journey', PastelColors.success),
                          _buildLegend('End Journey', Colors.orange),
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
                              child: _isLoading
                                  ? const Center(
                                      child: Padding(
                                        padding: EdgeInsets.all(20.0),
                                        child: CircularProgressIndicator(),
                                      ),
                                    )
                                  : _errorMessage != null
                                      ? SingleChildScrollView(
                                          padding: const EdgeInsets.all(12.0),
                                          child: Column(
                                            mainAxisAlignment: MainAxisAlignment.center,
                                            children: [
                                              Icon(Icons.error_outline, size: 32, color: PastelColors.error),
                                              const SizedBox(height: 8),
                                              Text(
                                                'Gagal memuatkan program',
                                                style: AppTextStyles.bodyMedium.copyWith(fontWeight: FontWeight.bold),
                                                textAlign: TextAlign.center,
                                              ),
                                              const SizedBox(height: 8),
                                              ElevatedButton(
                                                onPressed: _loadPrograms,
                                                style: ElevatedButton.styleFrom(
                                                  backgroundColor: PastelColors.primary,
                                                  foregroundColor: Colors.white,
                                                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                                                ),
                                                child: const Text('Cuba Lagi', style: TextStyle(fontSize: 12)),
                                              ),
                                            ],
                                          ),
                                        )
                                      : TabBarView(
                                          children: [
                                            _buildProgramListFromApi(_currentPrograms),
                                            _buildProgramListFromApi(_ongoingPrograms),
                                            _buildProgramListFromApi(_pastPrograms),
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

  Widget _buildProgramListFromApi(List<dynamic> programs) {
    if (programs.isEmpty) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(12.0),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(Icons.event_busy, size: 32, color: PastelColors.textLight),
              const SizedBox(height: 8),
              Text(
                'Tiada program dijumpai',
                style: AppTextStyles.bodySmall,
                textAlign: TextAlign.center,
              ),
            ],
          ),
        ),
      );
    }

    return ListView.separated(
      padding: const EdgeInsets.symmetric(vertical: 3, horizontal: 0),
      itemCount: programs.length,
      separatorBuilder: (_, __) => const SizedBox(height: 3),
      itemBuilder: (context, i) {
        final program = programs[i];
        final statusLabel = program['status_label'] ?? program['status'] ?? 'N/A';
        final programName = program['nama_program'] ?? 'Program Tidak Dikenali';
        final startDate = program['tarikh_mula_formatted'] ?? '';
        final endDate = program['tarikh_selesai_formatted'] ?? '';
        
        // Determine status color based on status
        Color statusColor = PastelColors.primary;
        if (statusLabel.toLowerCase().contains('selesai') || statusLabel.toLowerCase().contains('completed')) {
          statusColor = PastelColors.textLight;
        } else if (statusLabel.toLowerCase().contains('aktif') || statusLabel.toLowerCase().contains('active')) {
          statusColor = PastelColors.success;
        } else if (statusLabel.toLowerCase().contains('tertunda') || statusLabel.toLowerCase().contains('pending')) {
          statusColor = PastelColors.warning;
        }

        return GestureDetector(
          onTap: () {
            // Navigate to program detail screen
            Navigator.push(
              context,
              MaterialPageRoute(
                builder: (context) => ProgramDetailScreen(
                  programId: program['id'],
                ),
              ),
            );
          },
          child: Card(
            margin: EdgeInsets.zero,
            elevation: 0.5,
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(6),
            ),
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
                              child: Text(programName, style: AppTextStyles.h3),
                            ),
                            Container(
                              padding: const EdgeInsets.symmetric(
                                horizontal: 8,
                                vertical: 2,
                              ),
                              decoration: BoxDecoration(
                                color: statusColor.withOpacity(0.15),
                                borderRadius: BorderRadius.circular(8),
                              ),
                              child: Text(
                                statusLabel,
                                style: AppTextStyles.bodySmall.copyWith(
                                  color: statusColor,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ),
                          ],
                        ),
                        if (startDate.isNotEmpty && endDate.isNotEmpty)
                          Padding(
                            padding: const EdgeInsets.only(top: 4),
                            child: Row(
                              children: [
                                Icon(
                                  Icons.calendar_today,
                                  size: 10,
                                  color: PastelColors.textLight,
                                ),
                                const SizedBox(width: 4),
                                Text(
                                  '$startDate - $endDate',
                                  style: AppTextStyles.bodySmall,
                                ),
                              ],
                            ),
                          ),
                        if (program['pemohon'] != null)
                          Padding(
                            padding: const EdgeInsets.only(top: 4),
                            child: Row(
                              children: [
                                Icon(
                                  Icons.person_outline,
                                  size: 10,
                                  color: PastelColors.primary,
                                ),
                                const SizedBox(width: 4),
                                Expanded(
                                  child: Text(
                                    'Ketuk untuk lihat butiran',
                                    style: AppTextStyles.bodySmall.copyWith(
                                      color: PastelColors.primary,
                                      fontStyle: FontStyle.italic,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          ),
                      ],
                    ),
                  ),
                  Icon(
                    Icons.chevron_right,
                    color: PastelColors.textLight,
                    size: 20,
                  ),
                ],
              ),
            ),
          ),
        );
      },
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
