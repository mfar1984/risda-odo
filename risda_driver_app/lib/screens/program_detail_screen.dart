import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';
import '../theme/pastel_colors.dart';
import '../theme/text_styles.dart';
import '../services/api_service.dart';
import '../core/api_client.dart';
import 'package:provider/provider.dart';
import '../services/connectivity_service.dart';
import '../services/hive_service.dart';
import '../models/program_hive_model.dart';
 

class ProgramDetailScreen extends StatefulWidget {
  final int programId;

  const ProgramDetailScreen({
    super.key,
    required this.programId,
  });

  @override
  State<ProgramDetailScreen> createState() => _ProgramDetailScreenState();
}

class _ProgramDetailScreenState extends State<ProgramDetailScreen> {
  late final ApiService _apiService;
  Map<String, dynamic>? _programData;
  bool _isLoading = true;
  String? _errorMessage;

  @override
  void initState() {
    super.initState();
    _apiService = ApiService(ApiClient());
    _loadProgramDetail();
  }

  Future<void> _loadProgramDetail() async {
    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    try {
      final isOnline = mounted ? context.read<ConnectivityService>().isOnline : true;
      if (isOnline) {
        final response = await _apiService.getProgramDetail(widget.programId);
        if (response['success'] == true && response['data'] != null) {
          setState(() {
            _programData = response['data'];
            _isLoading = false;
          });
          // Cache detail for offline usage
          try {
            HiveService.settingsBox.put('program_detail_${widget.programId}', response['data']);
          } catch (_) {}
          return;
        }
        // If API error, fall through to offline fallback
      }

      // OFFLINE or API failed → fallback to Hive cache
      Map<String, dynamic>? cachedDetail;
      try {
        final c = HiveService.settingsBox.get('program_detail_${widget.programId}');
        if (c is Map) {
          cachedDetail = Map<String, dynamic>.from(c);
        }
      } catch (_) {}

      ProgramHive? p;
      try {
        p = HiveService.getAllPrograms().firstWhere((e) => e.id == widget.programId);
      } catch (_) {
        p = null;
      }

      if (cachedDetail != null) {
        // Enrich cached detail with vehicle info from Hive if missing
        if ((cachedDetail['kenderaan'] == null || cachedDetail['kenderaan'] is! Map) && p != null) {
          try {
            final vehicles = HiveService.getAllVehicles();
            int? vid;
            if (p.kenderaanId != null) {
              final s = p.kenderaanId!.split(',').map((e) => int.tryParse(e.trim())).whereType<int>().toList();
              if (s.isNotEmpty) vid = s.first;
            }
            if (vid != null && vehicles.isNotEmpty) {
              final matched = vehicles.where((x) => x.id == vid).toList();
              final v = matched.isNotEmpty ? matched.first : vehicles.first;
              cachedDetail['kenderaan'] = {
                'id': v.id,
                'no_plat': v.noPendaftaran,
                'jenama': v.jenisKenderaan,
                'model': v.model,
              };
            }
          } catch (_) {}
        }
        setState(() {
          _programData = cachedDetail;
          _isLoading = false;
        });
      } else if (p != null) {
        final lp = p; // non-null local
        final statusLabel = lp.status == 'sedang_berlangsung'
            ? 'Aktif'
            : (lp.status == 'selesai' ? 'Selesai' : lp.status);
        setState(() {
          _programData = {
            'id': lp.id,
            'nama_program': lp.namaProgram,
            'status_label': statusLabel ?? 'N/A',
            'tarikh_mula': lp.tarikhMula?.toIso8601String(),
            'tarikh_selesai': lp.tarikhTamat?.toIso8601String(),
            'lokasi_program': lp.lokasi,
            'jarak_anggaran': lp.jarakAnggaran,
            'penerangan': lp.peneranganProgram,
            // Minimal related info from cache if needed
            'kenderaan': null,
            'permohonan_dari': null,
            'pemandu': null,
          };
          _isLoading = false;
        });
      } else {
        throw Exception('Butiran program tiada dalam cache (offline)');
      }
    } catch (e) {
      setState(() {
        _isLoading = false;
        _errorMessage = e.toString();
      });
    }
  }

  Future<void> _openGoogleMaps() async {
    if (_programData == null) return;

    final lat = _programData!['lokasi_lat'];
    final long = _programData!['lokasi_long'];
    final location = _programData!['lokasi_program'] ?? 'Location';

    // Google Maps URL with coordinates
    final url = Uri.parse(
      'https://www.google.com/maps/search/?api=1&query=$lat,$long',
    );

    try {
      if (await canLaunchUrl(url)) {
        await launchUrl(url, mode: LaunchMode.externalApplication);
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('Tidak dapat membuka Google Maps')),
          );
        }
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error: $e')),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white, // Clean white background
      appBar: AppBar(
        title: const Text('Butiran Program'),
        backgroundColor: PastelColors.primary,
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _errorMessage != null
              ? _buildErrorState()
              : _buildProgramDetail(),
    );
  }

  Widget _buildErrorState() {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(20.0),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.error_outline, size: 64, color: PastelColors.error),
            const SizedBox(height: 16),
            Text(
              'Gagal memuatkan butiran program',
              style: AppTextStyles.h2,
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 8),
            Text(
              _errorMessage ?? 'Unknown error',
              style: AppTextStyles.bodyMedium,
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 16),
            ElevatedButton(
              onPressed: _loadProgramDetail,
              style: ElevatedButton.styleFrom(
                backgroundColor: PastelColors.primary,
                foregroundColor: Colors.white,
              ),
              child: const Text('Cuba Lagi'),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildProgramDetail() {
    if (_programData == null) return const SizedBox.shrink();

    final programName = _programData!['nama_program'] ?? 'N/A';
    final statusLabel = _programData!['status_label'] ?? 'N/A';
    final startDate = _programData!['tarikh_mula'] ?? '';
    final endDate = _programData!['tarikh_selesai'] ?? '';
    final location = _programData!['lokasi_program'] ?? 'N/A';
    final distance = _programData!['jarak_anggaran']?.toString() ?? '0';
    final description = _programData!['penerangan'] ?? '-';
    final arahanKhas = (_programData!['arahan_khas_pengguna_kenderaan'] ?? '').toString().trim();
    
    final vehicle = _programData!['kenderaan'];
    final requestor = _programData!['permohonan_dari'];
    final driver = _programData!['pemandu'];

    // Determine status color
    Color statusColor = PastelColors.primary;
    if (statusLabel.toLowerCase().contains('aktif')) {
      statusColor = PastelColors.success;
    } else if (statusLabel.toLowerCase().contains('selesai')) {
      statusColor = PastelColors.textLight;
    }

    return SingleChildScrollView(
      child: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Program Header Card
            Container(
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: Colors.grey.shade200),
                boxShadow: [
                  BoxShadow(
                    color: Colors.grey.shade100,
                    blurRadius: 10,
                    offset: const Offset(0, 2),
                  ),
                ],
              ),
              child: Padding(
                padding: const EdgeInsets.all(16.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        Expanded(
                          child: Text(
                            programName,
                            style: AppTextStyles.h2,
                          ),
                        ),
                        Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 12,
                            vertical: 6,
                          ),
                          decoration: BoxDecoration(
                            color: statusColor.withOpacity(0.15),
                            borderRadius: BorderRadius.circular(20),
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
                    const SizedBox(height: 16),
                    _buildInfoRow(
                      Icons.calendar_today,
                      'Tarikh Mula',
                      startDate,
                    ),
                    const SizedBox(height: 8),
                    _buildInfoRow(
                      Icons.event,
                      'Tarikh Selesai',
                      endDate,
                    ),
                    const SizedBox(height: 8),
                    _buildInfoRow(
                      Icons.straighten,
                      'Jarak Anggaran',
                      '$distance KM',
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 16),

            // Actual Dates Card (NEW)
            Container(
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: Colors.grey.shade200),
                boxShadow: [
                  BoxShadow(
                    color: Colors.grey.shade100,
                    blurRadius: 10,
                    offset: const Offset(0, 2),
                  ),
                ],
              ),
              child: Padding(
                padding: const EdgeInsets.all(16.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        Icon(Icons.access_time, color: PastelColors.primary),
                        const SizedBox(width: 8),
                        Text('Tarikh Sebenar', style: AppTextStyles.h3),
                      ],
                    ),
                    const SizedBox(height: 12),
                    _buildInfoRow(
                      Icons.check_circle,
                      'Tarikh Kelulusan',
                      _formatDateTime(_programData!['tarikh_kelulusan']),
                    ),
                    const SizedBox(height: 8),
                    _buildInfoRow(
                      Icons.play_arrow,
                      'Tarikh Mula Aktif',
                      _formatDateTime(_programData!['tarikh_mula_aktif']),
                    ),
                    const SizedBox(height: 8),
                    _buildInfoRow(
                      Icons.flag,
                      'Tarikh Sebenar Selesai',
                      _formatDateTime(_programData!['tarikh_sebenar_selesai']),
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 16),

            // Location Card with Google Maps button
            Container(
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: Colors.grey.shade200),
                boxShadow: [
                  BoxShadow(
                    color: Colors.grey.shade100,
                    blurRadius: 10,
                    offset: const Offset(0, 2),
                  ),
                ],
              ),
              child: Padding(
                padding: const EdgeInsets.all(16.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        Icon(Icons.location_on, color: PastelColors.error),
                        const SizedBox(width: 8),
                        Text('Lokasi Program', style: AppTextStyles.h3),
                      ],
                    ),
                    const SizedBox(height: 12),
                    Text(location, style: AppTextStyles.bodyMedium),
                    const SizedBox(height: 12),
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton.icon(
                        onPressed: _openGoogleMaps,
                        icon: const Icon(Icons.map, size: 20),
                        label: const Text('Buka di Google Maps'),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: PastelColors.primary,
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(vertical: 12),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(8),
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 16),

            // Vehicle Information Card
            if (vehicle != null) ...[
              Container(
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: Colors.grey.shade200),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.grey.shade100,
                      blurRadius: 10,
                      offset: const Offset(0, 2),
                    ),
                  ],
                ),
                child: Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        children: [
                          Icon(Icons.directions_car, color: PastelColors.primary),
                          const SizedBox(width: 8),
                          Text('Kenderaan', style: AppTextStyles.h3),
                        ],
                      ),
                      const SizedBox(height: 12),
                      _buildInfoRow(
                        Icons.confirmation_number,
                        'No. Plat',
                        vehicle['no_plat'] ?? 'N/A',
                      ),
                      const SizedBox(height: 8),
                      _buildInfoRow(
                        Icons.card_travel,
                        'Jenama & Model',
                        '${vehicle['jenama'] ?? 'N/A'} ${vehicle['model'] ?? ''}',
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 16),
            ],

            // Requestor Information Card
            if (requestor != null) ...[
              Container(
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: Colors.grey.shade200),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.grey.shade100,
                      blurRadius: 10,
                      offset: const Offset(0, 2),
                    ),
                  ],
                ),
                child: Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        children: [
                          Icon(Icons.person, color: PastelColors.warning),
                          const SizedBox(width: 8),
                          Text('Permohonan Dari', style: AppTextStyles.h3),
                        ],
                      ),
                      const SizedBox(height: 12),
                      _buildInfoRow(
                        Icons.badge,
                        'Nama',
                        requestor['nama_penuh'] ?? 'N/A',
                      ),
                      const SizedBox(height: 8),
                      _buildInfoRow(
                        Icons.phone,
                        'No. Telefon',
                        requestor['no_telefon'] ?? 'N/A',
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 16),
            ],

            // Description Card
            Container(
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: Colors.grey.shade200),
                boxShadow: [
                  BoxShadow(
                    color: Colors.grey.shade100,
                    blurRadius: 10,
                    offset: const Offset(0, 2),
                  ),
                ],
              ),
              child: Padding(
                padding: const EdgeInsets.all(16.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        Icon(Icons.description, color: PastelColors.primary),
                        const SizedBox(width: 8),
                        Text('Penerangan', style: AppTextStyles.h3),
                      ],
                    ),
                    const SizedBox(height: 12),
                    Text(description, style: AppTextStyles.bodyMedium),
                  ],
                ),
              ),
            ),

            // Arahan Khas Pengguna Kenderaan (only if available)
            if (arahanKhas.isNotEmpty) ...[
              const SizedBox(height: 16),
              Container(
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: Colors.grey.shade200),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.grey.shade100,
                      blurRadius: 10,
                      offset: const Offset(0, 2),
                    ),
                  ],
                ),
                child: Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        children: [
                          Icon(Icons.rule, color: PastelColors.primary),
                          const SizedBox(width: 8),
                          Text('Arahan Khas Pengguna Kenderaan', style: AppTextStyles.h3),
                        ],
                      ),
                      const SizedBox(height: 12),
                      Text(
                        arahanKhas,
                        style: AppTextStyles.bodyMedium.copyWith(fontWeight: FontWeight.w600),
                      ),
                    ],
                  ),
                ),
              ),
            ],
          ],
        ),
      ),
    );
  }

  String _formatDateTime(dynamic dateTimeStr) {
    if (dateTimeStr == null || dateTimeStr == '') {
      return '-';
    }

    try {
      // Parse UTC datetime from server
      final utcDateTime = DateTime.parse(dateTimeStr);
      
      // Convert to Malaysia timezone (UTC+8)
      final malaysiaDateTime = utcDateTime.add(const Duration(hours: 8));
      
      // Format: dd/MM/yyyy HH:mm
      final day = malaysiaDateTime.day.toString().padLeft(2, '0');
      final month = malaysiaDateTime.month.toString().padLeft(2, '0');
      final year = malaysiaDateTime.year.toString();
      final hour = malaysiaDateTime.hour.toString().padLeft(2, '0');
      final minute = malaysiaDateTime.minute.toString().padLeft(2, '0');
      
      return '$day/$month/$year $hour:$minute';
    } catch (e) {
      
      return '-';
    }
  }

  Widget _buildInfoRow(IconData icon, String label, String value) {
    return Row(
      children: [
        Icon(icon, size: 18, color: PastelColors.textLight),
        const SizedBox(width: 8),
        Text(
          '$label: ',
          style: AppTextStyles.bodyMedium.copyWith(
            color: PastelColors.textLight,
          ),
        ),
        Expanded(
          child: Text(
            value,
            style: AppTextStyles.bodyMedium.copyWith(
              fontWeight: FontWeight.w600,
            ),
          ),
        ),
      ],
    );
  }
}

