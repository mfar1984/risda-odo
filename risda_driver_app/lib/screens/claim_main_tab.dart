import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import 'package:uuid/uuid.dart';
import 'dart:io';
import '../theme/pastel_colors.dart';
import '../theme/text_styles.dart';
import '../services/api_service.dart';
import '../services/hive_service.dart';
import '../services/connectivity_service.dart';
import '../services/sync_service.dart';
import '../models/claim_hive_model.dart';
import '../core/api_client.dart';
import '../core/constants.dart';
import 'claim_screen.dart';
 

class ClaimMainTab extends StatefulWidget {
  const ClaimMainTab({super.key});

  @override
  State<ClaimMainTab> createState() => _ClaimMainTabState();
}

class _ClaimMainTabState extends State<ClaimMainTab> with SingleTickerProviderStateMixin {
  late TabController _tabController;
  final ApiService _apiService = ApiService(ApiClient());
  
  bool isLoading = true;
  List<Map<String, dynamic>>? allClaims;
  List<Map<String, dynamic>>? pendingClaims;
  List<Map<String, dynamic>>? rejectedClaims;
  List<Map<String, dynamic>>? approvedClaims;
  bool _isSyncingClaims = false;
  bool _didBackgroundSync = false;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 4, vsync: this);
    _loadClaims();
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  Future<void> _loadClaims({bool triggerBackgroundSync = true}) async {
    if (!mounted) return;
    setState(() => isLoading = true);
    
    try {
      // ============================================
      // OFFLINE-FIRST: Load from Hive first
      // ============================================
      final hiveClaims = HiveService.getAllClaims();
      // Preload journeys and programs to enrich claim display
      final journeys = HiveService.getAllJourneys();
      final programs = HiveService.getAllPrograms();
      final journeyById = {for (var j in journeys) if (j.id != null) j.id!: j};
      final programById = {for (var p in programs) p.id: p};
      
      // Convert ClaimHive to Map format
      final claims = hiveClaims.map((c) {
        // Enrich with local journey + program info when available
        Map<String, dynamic>? programMap;
        Map<String, dynamic>? logMap;
        if (c.logPemanduId != null && journeyById.containsKey(c.logPemanduId)) {
          final j = journeyById[c.logPemanduId]!;
          if (j.programId != null && programById.containsKey(j.programId)) {
            final p = programById[j.programId]!;
            programMap = {
              'id': p.id,
              'nama_program': p.namaProgram,
              'lokasi_program': p.lokasi ?? '-',
              'permohonan_dari': null, // not available offline yet
            };
          }
          // Compose a readable datetime for the journey
          final tarikh = j.tarikhPerjalanan.toIso8601String().split('T')[0];
          final tarikhMasa = '${tarikh} ${j.masaKeluar}';
          logMap = {
            'id': j.id,
            'tarikh_masa_perjalanan': tarikhMasa,
          };
        }
        return {
          'id': c.id,
          'log_pemandu_id': c.logPemanduId,
          'kategori': c.kategori,
          'kategori_label': _getKategoriLabel(c.kategori),
          'jumlah': c.jumlah,
          'keterangan': c.catatan,
          'resit_server': c.resit,      // explicit server path
          'resit_local': c.resitLocal,  // explicit local path
          'resit': c.resit ?? c.resitLocal, // legacy field
          'status': c.status,
          'status_label': _getStatusLabel(c.status),
          'alasan_tolak': c.alasanTolak,
          'alasan_gantung': c.alasanGantung,
          'created_at': c.createdAt?.toIso8601String(),
          'tarikh_diproses': c.tarikhDiproses?.toIso8601String(),
          'can_edit': c.status == 'ditolak',
          'is_synced': c.isSynced,  // Track sync status
          'local_id': c.localId,
          'diproses_oleh': null,  // Simplified for now
          'program': programMap,  // from local Hive if available
          'log_pemandu': logMap,  // from local Hive if available
        };
      }).toList();
      
      
      
      if (!mounted) return;
      setState(() {
        allClaims = claims;
        pendingClaims = claims.where((c) => c['status'] == 'pending').toList();
        rejectedClaims = claims.where((c) => c['status'] == 'ditolak').toList();
        approvedClaims = claims.where((c) => c['status'] == 'diluluskan').toList();
        isLoading = false;
      });
      
      // If online, sync fresh data from server in background (one-shot)
      final connectivity = context.read<ConnectivityService>();
      if (triggerBackgroundSync && connectivity.isOnline && !_isSyncingClaims && !_didBackgroundSync) {
        
        await _syncClaimsInBackground();
      }
    } catch (e) {
      
      setState(() {
        allClaims = [];
        pendingClaims = [];
        rejectedClaims = [];
        approvedClaims = [];
        isLoading = false;
      });
    }
  }
  
  /// Sync claims in background (if online)
  Future<void> _syncClaimsInBackground() async {
    try {
      if (_isSyncingClaims) return;
      _isSyncingClaims = true;
      final response = await _apiService.getClaims();
      if (response['success'] == true && mounted) {
        // Update Hive with fresh data
        final serverClaims = List<Map<String, dynamic>>.from(response['data'] ?? []);
        
        // Keep offline claims, update synced ones
        final unsyncedClaims = HiveService.getPendingSyncClaims();
        
        await HiveService.claimBox.clear();
        
        // Add server claims
        for (var claim in serverClaims) {
          const uuid = Uuid();
          final claimHive = ClaimHive.fromJson(claim, localId: uuid.v4());
          await HiveService.saveClaim(claimHive);
        }
        
        // Re-add unsynced offline claims
        for (var claim in unsyncedClaims) {
          await HiveService.saveClaim(claim);
        }
        
        // Reload UI from Hive without triggering another background sync
        if (!mounted) return;
        await _loadClaims(triggerBackgroundSync: false);
      }
    } catch (e) {
      
      // Silently fail - user already has Hive data
    } finally {
      _isSyncingClaims = false;
      _didBackgroundSync = true;
    }
  }
  
  String _getKategoriLabel(String kategori) {
    const kategoriMap = {
      'tol': 'Tol',
      'parking': 'Parking',
      'f&b': 'Food & Beverage',
      'accommodation': 'Accommodation',
      'fuel': 'Fuel',
      'car_maintenance': 'Car Maintenance',
      'others': 'Others',
    };
    return kategoriMap[kategori] ?? kategori;
  }
  
  String _getStatusLabel(String status) {
    const statusMap = {
      'pending': 'Pending',
      'diluluskan': 'Diluluskan',
      'ditolak': 'Ditolak',
      'digantung': 'Dibatalkan',
    };
    return statusMap[status] ?? status;
  }

  String _toText(dynamic value) {
    if (value == null) return 'N/A';
    if (value is String) return value;
    if (value is Map) {
      final map = Map<String, dynamic>.from(value as Map);
      return map['nama_penuh']?.toString() ??
             map['nama']?.toString() ??
             map['name']?.toString() ??
             map['label']?.toString() ??
             map['title']?.toString() ??
             map.values.first?.toString() ?? 'N/A';
    }
    return value.toString();
  }

  Future<void> _navigateToCreateClaim() async {
    final result = await Navigator.push(
      context,
      MaterialPageRoute(builder: (_) => const ClaimScreen()),
    );
    
    // Reload if claim was created
    if (result == true) {
      _loadClaims();
    }
  }

  Future<void> _navigateToEditClaim(Map<String, dynamic> claim) async {
    final result = await Navigator.push(
      context,
      MaterialPageRoute(builder: (_) => ClaimScreen(existingClaim: claim)),
    );
    
    // Reload if claim was updated
    if (result == true) {
      _loadClaims();
    }
  }

  void _showClaimDetails(Map<String, dynamic> claim) {
    final program = claim['program'] as Map<String, dynamic>?;
    final logPemandu = claim['log_pemandu'] as Map<String, dynamic>?;

    // Enrich program details from cache/API before showing
    if (program != null && program['id'] != null) {
      final programId = program['id'] as int;
      try {
        // Try cached details first
        final cached = HiveService.settingsBox.get('program_detail_$programId');
        if (cached is Map) {
          final cachedMap = Map<String, dynamic>.from(cached);
          program['lokasi_program'] ??= cachedMap['lokasi_program'] ?? cachedMap['lokasi'];
          program['permohonan_dari'] ??= cachedMap['permohonan_dari'];
        }

        // If still missing and online, fetch live detail then cache
        final connectivity = context.read<ConnectivityService>();
        if (connectivity.isOnline && (program['lokasi_program'] == null || program['permohonan_dari'] == null)) {
          _apiService.getProgramDetail(programId).then((resp) {
            if (resp['success'] == true && resp['data'] != null) {
              final data = Map<String, dynamic>.from(resp['data']);
              program['lokasi_program'] ??= data['lokasi_program'] ?? data['lokasi'];
              program['permohonan_dari'] ??= data['permohonan_dari'];
              // Cache for future offline use
              HiveService.settingsBox.put('program_detail_$programId', data);
              if (mounted) setState(() {});
            }
          }).catchError((_) {});
        }
      } catch (_) {}
    }
    
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
            
            // Header with close button
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 20),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Row(
                    children: [
                      Text('Butiran Tuntutan', style: AppTextStyles.h1),
                      const SizedBox(width: 12),
                      _buildStatusBadge(claim['status']),
                    ],
                  ),
                  IconButton(
                    onPressed: () => Navigator.pop(context),
                    icon: const Icon(Icons.close),
                    color: Colors.grey[600],
                  ),
                ],
              ),
            ),
            
            const Divider(height: 1),
            
            // Content
            Expanded(
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(20),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Program Section
                    _buildSectionTitle('Maklumat Program'),
                    _buildDetailContainer([
                      _buildDetailRowWithIcon(
                        Icons.event_note,
                        'Program',
                        program?['nama_program'] ?? 'N/A',
                      ),
                      _buildDetailRowWithIcon(
                        Icons.location_on,
                        'Lokasi',
                        program?['lokasi_program'] ?? 'N/A',
                      ),
                      _buildDetailRowWithIcon(
                        Icons.person_outline,
                        'Dimohon Oleh',
                        _toText(program?['permohonan_dari']),
                      ),
                      if (logPemandu?['tarikh_masa_perjalanan'] != null)
                        _buildDetailRowWithIcon(
                          Icons.calendar_today,
                          'Tarikh Perjalanan',
                          logPemandu!['tarikh_masa_perjalanan'],
                        ),
                    ]),
                    
                    const SizedBox(height: 16),
                    
                    // Claim Section
                    _buildSectionTitle('Maklumat Tuntutan'),
                    _buildDetailContainer([
                      _buildDetailRowWithIcon(
                        Icons.category,
                        'Kategori',
                        claim['kategori_label'] ?? 'N/A',
                      ),
                      _buildDetailRowWithIcon(
                        Icons.attach_money,
                        'Jumlah',
                        'RM ${claim['jumlah']?.toStringAsFixed(2) ?? '0.00'}',
                        isHighlighted: true,
                      ),
                      _buildDetailRowWithIcon(
                        Icons.access_time,
                        'Tarikh Dibuat',
                        _formatDateTime(claim['created_at']),
                      ),
                      if (claim['tarikh_diproses'] != null) ...[
                        _buildDetailRowWithIcon(
                          Icons.check_circle,
                          'Tarikh Diproses',
                          _formatDateTime(claim['tarikh_diproses']),
                        ),
                        if (claim['diproses_oleh'] != null)
                          _buildDetailRowWithIcon(
                            Icons.person,
                            _getProcessedByLabel(claim['status']),
                            claim['diproses_oleh']['nama_penuh'] ?? claim['diproses_oleh']['name'] ?? 'N/A',
                          ),
                      ],
                    ]),
                    
                    // Keterangan Section
                    if (claim['keterangan'] != null && claim['keterangan'].toString().isNotEmpty) ...[
                      const SizedBox(height: 16),
                      _buildSectionTitle('Keterangan'),
                      _buildDetailContainer([
                        Text(
                          claim['keterangan'],
                          style: AppTextStyles.bodyMedium,
                        ),
                      ]),
                    ],
                    
                    // Rejection Reason Section
                    if (claim['alasan_tolak'] != null && claim['alasan_tolak'].toString().isNotEmpty) ...[
                      const SizedBox(height: 16),
                      _buildSectionTitle('Alasan Ditolak'),
                      Container(
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: Colors.red.shade50,
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(color: Colors.red.shade200, width: 1),
                        ),
                        child: Row(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Icon(Icons.warning_amber_rounded, color: Colors.red.shade700, size: 20),
                            const SizedBox(width: 12),
                            Expanded(
                              child: Text(
                                claim['alasan_tolak'],
                                style: AppTextStyles.bodyMedium.copyWith(
                                  color: Colors.red.shade900,
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                    
                    // Cancellation Reason Section
                    if (claim['alasan_gantung'] != null && claim['alasan_gantung'].toString().isNotEmpty) ...[
                      const SizedBox(height: 16),
                      _buildSectionTitle('Alasan Dibatalkan'),
                      Container(
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: Colors.grey.shade100,
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(color: Colors.grey.shade300, width: 1),
                        ),
                        child: Row(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Icon(Icons.cancel, color: Colors.grey.shade700, size: 20),
                            const SizedBox(width: 12),
                            Expanded(
                              child: Text(
                                claim['alasan_gantung'],
                                style: AppTextStyles.bodyMedium.copyWith(
                                  color: Colors.grey.shade900,
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                    
                    // Receipt Image Section
                    if ((claim['resit_server'] != null && claim['resit_server'].toString().isNotEmpty) ||
                        (claim['resit_local'] != null && claim['resit_local'].toString().isNotEmpty)) ...[
                      const SizedBox(height: 16),
                      _buildSectionTitle('Resit'),
                      GestureDetector(
                        onTap: () => _showImagePreview(context, claim['resit_local'] ?? claim['resit_server'], isLocal: claim['resit_local'] != null),
                        child: Container(
                          height: 200,
                          decoration: BoxDecoration(
                            color: Colors.grey[100],
                            borderRadius: BorderRadius.circular(12),
                            border: Border.all(color: Colors.grey[300]!, width: 1),
                          ),
                          child: ClipRRect(
                            borderRadius: BorderRadius.circular(12),
                            child: (claim['resit_local'] != null)
                                ? Image.file(
                                    File(claim['resit_local']),
                                    fit: BoxFit.cover,
                                    errorBuilder: (context, error, stackTrace) {
                                      return Center(
                                        child: Column(
                                          mainAxisAlignment: MainAxisAlignment.center,
                                          children: [
                                            Icon(Icons.broken_image, size: 48, color: Colors.grey[400]),
                                            const SizedBox(height: 8),
                                            Text('Gagal memuatkan gambar', style: AppTextStyles.bodySmall.copyWith(color: Colors.grey[600])),
                                          ],
                                        ),
                                      );
                                    },
                                  )
                                : Image.network(
                                    ApiConstants.buildStorageUrl(claim['resit_server']),
                                    fit: BoxFit.cover,
                                    loadingBuilder: (context, child, loadingProgress) {
                                      if (loadingProgress == null) return child;
                                      return Center(
                                        child: CircularProgressIndicator(
                                          value: loadingProgress.expectedTotalBytes != null
                                              ? loadingProgress.cumulativeBytesLoaded / loadingProgress.expectedTotalBytes!
                                              : null,
                                        ),
                                      );
                                    },
                                    errorBuilder: (context, error, stackTrace) {
                                      return Center(
                                        child: Column(
                                          mainAxisAlignment: MainAxisAlignment.center,
                                          children: [
                                            Icon(Icons.broken_image, size: 48, color: Colors.grey[400]),
                                            const SizedBox(height: 8),
                                            Text('Gagal memuatkan gambar', style: AppTextStyles.bodySmall.copyWith(color: Colors.grey[600])),
                                          ],
                                        ),
                                      );
                                    },
                                  ),
                          ),
                        ),
                      ),
                      const SizedBox(height: 8),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(Icons.touch_app, size: 14, color: Colors.grey[600]),
                          const SizedBox(width: 4),
                          Text(
                            'Tap untuk lihat penuh',
                            style: AppTextStyles.bodySmall.copyWith(color: Colors.grey[600], fontStyle: FontStyle.italic),
                          ),
                        ],
                      ),
                    ],
                    
                    // Action buttons for rejected claims
                    if (claim['status'] == 'ditolak') ...[
                      const SizedBox(height: 24),
                      SizedBox(
                        width: double.infinity,
                        child: ElevatedButton.icon(
                          onPressed: () {
                            Navigator.pop(context);
                            _navigateToEditClaim(claim);
                          },
                          icon: const Icon(Icons.edit, color: Colors.white),
                          label: const Text('EDIT & HANTAR SEMULA'),
                          style: ElevatedButton.styleFrom(
                            backgroundColor: PastelColors.primary,
                            foregroundColor: Colors.white,
                            padding: const EdgeInsets.symmetric(vertical: 16),
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                            elevation: 2,
                          ),
                        ),
                      ),
                    ],
                    
                    const SizedBox(height: 20),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  String _getProcessedByLabel(String status) {
    switch (status) {
      case 'diluluskan':
        return 'Diluluskan Oleh';
      case 'ditolak':
        return 'Ditolak Oleh';
      case 'digantung':
        return 'Dibatalkan Oleh';
      default:
        return 'Diproses Oleh';
    }
  }

  String _formatDateTime(String? dateTimeStr) {
    if (dateTimeStr == null || dateTimeStr.isEmpty) return 'N/A';
    
    try {
      // Parse the datetime string (handles both UTC and local)
      DateTime dateTime = DateTime.parse(dateTimeStr);
      
      // If it's UTC (ends with Z), convert to local Malaysia time
      if (dateTimeStr.endsWith('Z')) {
        dateTime = dateTime.toLocal();
      }
      
      // Format: 02-10-2025 01:02:00 pm
      final formatter = DateFormat('dd-MM-yyyy hh:mm:ss a');
      return formatter.format(dateTime);
    } catch (e) {
      return dateTimeStr; // Return original if parsing fails
    }
  }

  void _showImagePreview(BuildContext context, String imagePath, {bool isLocal = false}) {
    final imageUrl = isLocal ? null : ApiConstants.buildStorageUrl(imagePath);

    showDialog(
      context: context,
      builder: (context) => Dialog(
        backgroundColor: Colors.black,
        insetPadding: EdgeInsets.zero,
        child: Stack(
          children: [
            Center(
              child: InteractiveViewer(
                panEnabled: true,
                minScale: 0.5,
                maxScale: 4.0,
                child: isLocal
                    ? Image.file(
                        File(imagePath),
                        fit: BoxFit.contain,
                        errorBuilder: (context, error, stackTrace) {
                          return const Center(
                            child: Icon(Icons.error, color: Colors.white, size: 64),
                          );
                        },
                      )
                    : Image.network(
                        imageUrl!,
                        fit: BoxFit.contain,
                        loadingBuilder: (context, child, loadingProgress) {
                          if (loadingProgress == null) return child;
                          return const Center(
                            child: CircularProgressIndicator(color: Colors.white),
                          );
                        },
                        errorBuilder: (context, error, stackTrace) {
                          return const Center(
                            child: Icon(Icons.error, color: Colors.white, size: 64),
                          );
                        },
                      ),
              ),
            ),
            Positioned(
              top: 40,
              right: 16,
              child: IconButton(
                icon: const Icon(Icons.close, color: Colors.white, size: 32),
                onPressed: () => Navigator.pop(context),
              ),
            ),
            Positioned(
              bottom: 40,
              left: 0,
              right: 0,
              child: Center(
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                  decoration: BoxDecoration(
                    color: Colors.black54,
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Text(
                    imagePath.split('/').last,
                    style: const TextStyle(color: Colors.white, fontSize: 12),
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSectionTitle(String title) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: Text(
        title,
        style: AppTextStyles.h2.copyWith(
          color: PastelColors.primary,
          fontWeight: FontWeight.w600,
        ),
      ),
    );
  }

  Widget _buildDetailContainer(List<Widget> children) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.grey[50],
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: Colors.grey[200]!, width: 1),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: children,
      ),
    );
  }

  Widget _buildDetailRowWithIcon(IconData icon, String label, String value, {bool isHighlighted = false}) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, size: 18, color: isHighlighted ? PastelColors.primary : Colors.grey[600]),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  label,
                  style: AppTextStyles.bodySmall.copyWith(
                    color: Colors.grey[600],
                    fontWeight: FontWeight.w500,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  value,
                  style: AppTextStyles.bodyMedium.copyWith(
                    fontWeight: isHighlighted ? FontWeight.w700 : FontWeight.w500,
                    color: isHighlighted ? PastelColors.primary : Colors.black87,
                    fontSize: isHighlighted ? 18 : 14,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStatusBadge(String status) {
    Color bgColor;
    Color textColor;
    String label;

    switch (status) {
      case 'pending':
        bgColor = Colors.orange.shade50;
        textColor = Colors.orange.shade700;
        label = 'Pending';
        break;
      case 'diluluskan':
        bgColor = Colors.green.shade50;
        textColor = Colors.green.shade700;
        label = 'Diluluskan';
        break;
      case 'ditolak':
        bgColor = Colors.red.shade50;
        textColor = Colors.red.shade700;
        label = 'Ditolak';
        break;
      case 'digantung':
        bgColor = Colors.grey.shade200;
        textColor = Colors.grey.shade700;
        label = 'Dibatalkan';
        break;
      default:
        bgColor = Colors.grey.shade100;
        textColor = Colors.grey.shade600;
        label = status;
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
      decoration: BoxDecoration(
        color: bgColor,
        borderRadius: BorderRadius.circular(12),
      ),
      child: Text(
        label,
        style: AppTextStyles.bodySmall.copyWith(
          color: textColor,
          fontWeight: FontWeight.w600,
        ),
      ),
    );
  }

  Widget _buildClaimCard(Map<String, dynamic> claim) {
    final bool isSynced = claim['is_synced'] == true;
    final bool hasError = claim['sync_error'] != null && claim['sync_error'].toString().isNotEmpty;
    final String? localId = claim['local_id']?.toString();
    return Card(
      color: Colors.white,
      elevation: 2,
      margin: const EdgeInsets.only(bottom: 12),
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(8),
        side: BorderSide(color: PastelColors.border),
      ),
      child: InkWell(
        onTap: () => _showClaimDetails(claim),
        borderRadius: BorderRadius.circular(8),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Expanded(
                    child: Text(
                      claim['kategori_label'] ?? 'N/A',
                      style: AppTextStyles.h2,
                    ),
                  ),
                  _buildStatusBadge(claim['status']),
                ],
              ),
              const SizedBox(height: 8),
              Row(
                children: [
                  Icon(Icons.calendar_today, size: 14, color: Colors.grey[600]),
                  const SizedBox(width: 6),
                  Text(
                    _formatDateTime(claim['created_at']),
                    style: AppTextStyles.bodySmall.copyWith(color: Colors.grey[600]),
                  ),
                ],
              ),
              const SizedBox(height: 6),
              Row(
                children: [
                  Icon(Icons.location_on, size: 14, color: Colors.grey[600]),
                  const SizedBox(width: 6),
                  Expanded(
                    child: Text(
                      claim['program']?['nama_program'] ?? 'N/A',
                      style: AppTextStyles.bodySmall.copyWith(color: Colors.grey[600]),
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 12),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    'RM ${claim['jumlah']?.toStringAsFixed(2) ?? '0.00'}',
                    style: AppTextStyles.h1.copyWith(color: PastelColors.primary),
                  ),
                  Row(children: [
                    if (!isSynced) Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                      decoration: BoxDecoration(
                        color: Colors.orange.shade50,
                        borderRadius: BorderRadius.circular(8),
                        border: Border.all(color: Colors.orange.shade200),
                      ),
                      child: Row(children: const [
                        Icon(Icons.sync, size: 14, color: Colors.orange),
                        SizedBox(width: 4),
                        Text('Pending Sync', style: TextStyle(fontSize: 11, color: Colors.orange)),
                      ]),
                    ),
                    if (hasError) ...[
                      const SizedBox(width: 8),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                        decoration: BoxDecoration(
                          color: Colors.red.shade50,
                          borderRadius: BorderRadius.circular(8),
                          border: Border.all(color: Colors.red.shade200),
                        ),
                        child: Row(children: const [
                          Icon(Icons.error_outline, size: 14, color: Colors.red),
                          SizedBox(width: 4),
                          Text('Sync Failed', style: TextStyle(fontSize: 11, color: Colors.red)),
                        ]),
                      ),
                    ],
                  ]),
                ],
              ),
              if (hasError && localId != null) ...[
                const SizedBox(height: 8),
                Align(
                  alignment: Alignment.centerRight,
                  child: OutlinedButton.icon(
                    onPressed: () async {
                      final connectivity = context.read<ConnectivityService>();
                      if (!connectivity.isOnline) {
                        ScaffoldMessenger.of(context).showSnackBar(
                          const SnackBar(content: Text('Masih offline. Cuba lagi selepas online.')),
                        );
                        return;
                      }
                      final syncService = context.read<SyncService>();
                      final res = await syncService.syncSingleClaimByLocalId(localId);
                      if (mounted) {
                        if (res['success'] == true) {
                          ScaffoldMessenger.of(context).showSnackBar(
                            const SnackBar(content: Text('Sync berjaya')),
                          );
                          _loadClaims();
                        } else {
                          ScaffoldMessenger.of(context).showSnackBar(
                            SnackBar(content: Text('Gagal sync: ${res['message'] ?? 'Ralat'}')),
                          );
                        }
                      }
                    },
                    icon: const Icon(Icons.refresh, size: 16),
                    label: const Text('Cuba Sync Semula'),
                    style: OutlinedButton.styleFrom(
                      foregroundColor: PastelColors.primary,
                      side: BorderSide(color: PastelColors.primary),
                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                    ),
                  ),
                ),
              ],
              if (claim['status'] == 'ditolak')
                    TextButton.icon(
                      onPressed: () => _navigateToEditClaim(claim),
                      icon: const Icon(Icons.edit, size: 16),
                      label: const Text('Edit'),
                      style: TextButton.styleFrom(
                        foregroundColor: PastelColors.primary,
                        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                      ),
                    ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildClaimsList(List<Map<String, dynamic>>? claims) {
    if (isLoading) {
      return const Center(
        child: Padding(
          padding: EdgeInsets.all(40),
          child: CircularProgressIndicator(),
        ),
      );
    }

    if (claims == null || claims.isEmpty) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(40),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(Icons.receipt_long, size: 64, color: Colors.grey[400]),
              const SizedBox(height: 16),
              Text(
                'Tiada Tuntutan',
                style: AppTextStyles.h2.copyWith(color: Colors.grey[600]),
              ),
            ],
          ),
        ),
      );
    }

    return RefreshIndicator(
      onRefresh: _loadClaims,
      child: ListView.builder(
        padding: const EdgeInsets.all(16),
        itemCount: claims.length,
        itemBuilder: (context, index) => _buildClaimCard(claims[index]),
      ),
    );
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
            const Icon(Icons.receipt_long, color: Colors.white, size: 20),
            const SizedBox(width: 8),
            Text('Claim', style: AppTextStyles.h2.copyWith(color: Colors.white)),
          ],
        ),
        actions: [
          Padding(
            padding: const EdgeInsets.only(right: 12),
            child: ElevatedButton.icon(
              onPressed: _navigateToCreateClaim,
              icon: const Icon(Icons.add, size: 18, color: Colors.white),
              label: Text('Create Claim', style: AppTextStyles.bodyLarge.copyWith(color: Colors.white)),
              style: ElevatedButton.styleFrom(
                backgroundColor: PastelColors.accent,
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
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
          tabs: [
            Tab(text: 'Total (${allClaims?.length ?? 0})'),
            Tab(text: 'Pending (${pendingClaims?.length ?? 0})'),
            Tab(text: 'Ditolak (${rejectedClaims?.length ?? 0})'),
            Tab(text: 'Diluluskan (${approvedClaims?.length ?? 0})'),
          ],
        ),
      ),
      body: TabBarView(
        controller: _tabController,
        children: [
          _buildClaimsList(allClaims),
          _buildClaimsList(pendingClaims),
          _buildClaimsList(rejectedClaims),
          _buildClaimsList(approvedClaims),
        ],
      ),
    );
  }
}
