import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';
import '../theme/pastel_colors.dart';
import '../theme/text_styles.dart';
import '../services/api_service.dart';
import '../core/api_client.dart';
import 'dart:developer' as developer;

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
      developer.log('🔄 Loading program detail for ID: ${widget.programId}');
      
      final response = await _apiService.getProgramDetail(widget.programId);

      if (response['success'] == true && response['data'] != null) {
        setState(() {
          _programData = response['data'];
          _isLoading = false;
        });
        developer.log('✅ Program detail loaded successfully');
      } else {
        throw Exception('Failed to load program details');
      }
    } catch (e) {
      developer.log('❌ Error loading program detail: $e');
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
      developer.log('❌ Error opening Google Maps: $e');
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
      appBar: AppBar(
        title: const Text('Butiran Program'),
        backgroundColor: PastelColors.primary,
        foregroundColor: Colors.white,
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
            Card(
              elevation: 2,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(12),
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

            // Location Card with Google Maps button
            Card(
              elevation: 2,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(12),
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
              Card(
                elevation: 2,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12),
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
              Card(
                elevation: 2,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12),
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
            Card(
              elevation: 2,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(12),
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
          ],
        ),
      ),
    );
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

