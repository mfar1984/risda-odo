import 'dart:typed_data';
import 'dart:io';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:dio/dio.dart';
import 'package:provider/provider.dart';
import 'package:uuid/uuid.dart';
import 'package:path_provider/path_provider.dart';
import '../theme/pastel_colors.dart';
import '../theme/text_styles.dart';
import '../services/api_service.dart';
import '../services/connectivity_service.dart';
import '../services/hive_service.dart';
import '../services/auth_service.dart';
import '../core/api_client.dart';
import '../models/claim_hive_model.dart';
 

class ClaimScreen extends StatefulWidget {
  final Map<String, dynamic>? existingClaim; // For edit mode
  
  const ClaimScreen({super.key, this.existingClaim});

  @override
  State<ClaimScreen> createState() => _ClaimScreenState();
}

class _ClaimScreenState extends State<ClaimScreen> {
  final ApiService _apiService = ApiService(ApiClient());
  
  int? selectedLogId;
  String? selectedCategory;
  final TextEditingController amountController = TextEditingController();
  final TextEditingController descriptionController = TextEditingController();
  
  XFile? receiptImage;
  final ImagePicker _picker = ImagePicker();
  bool isSubmitting = false;
  bool isLoading = true;

  List<Map<String, dynamic>>? completedLogs;
  
  final categories = [
    {'value': 'tol', 'label': 'Tol'},
    {'value': 'parking', 'label': 'Parking'},
    {'value': 'f&b', 'label': 'Food & Beverage'},
    {'value': 'accommodation', 'label': 'Accommodation'},
    {'value': 'fuel', 'label': 'Fuel'},
    {'value': 'car_maintenance', 'label': 'Car Maintenance'},
    {'value': 'others', 'label': 'Others'},
  ];

  @override
  void initState() {
    super.initState();
    // Force reset to ensure clean state (especially after hot reload)
    selectedLogId = null;
    selectedCategory = null;
    completedLogs = null;
    isLoading = true;
    _loadData();
  }

  Future<void> _loadData() async {
    setState(() => isLoading = true);
    
    try {
      // ============================================
      // OFFLINE-FIRST: Load completed logs from Hive
      // ============================================
      final allJourneys = HiveService.getAllJourneys();
      final allPrograms = HiveService.getAllPrograms();
      final allVehicles = HiveService.getAllVehicles();
      final programById = {for (var p in allPrograms) p.id: p};
      final vehicleById = {for (var v in allVehicles) v.id: v};
      
      
      // Match online: Only completed journeys WITH server id
      final completedJourneys = allJourneys.where((j) => j.status == 'selesai' && j.id != null).toList();

      // Convert to Map format for dropdown (with vehicle + normalized datetime)
      final List<Map<String, dynamic>> logs = completedJourneys.map((j) {
        DateTime startDt;
        try {
          final hm = (j.masaKeluar).split(':');
          startDt = DateTime(j.tarikhPerjalanan.year, j.tarikhPerjalanan.month, j.tarikhPerjalanan.day,
              int.tryParse(hm[0]) ?? 0, int.tryParse(hm.length > 1 ? hm[1] : '0') ?? 0);
        } catch (_) {
          startDt = DateTime(j.tarikhPerjalanan.year, j.tarikhPerjalanan.month, j.tarikhPerjalanan.day);
        }
        final v = vehicleById[j.kenderaanId];
        return {
          'id': j.id, // non-null
          'program': j.programId != null
              ? {
                  'id': j.programId,
                  'nama_program': programById[j.programId]?.namaProgram ?? 'Program #${j.programId}',
                  'lokasi_program': programById[j.programId]?.lokasi,
                }
              : null,
          'kenderaan': v == null
              ? null
              : {
                  'id': v.id,
                  'no_plat': v.noPendaftaran,
                  'jenama': v.jenisKenderaan,
                  'model': v.model,
                },
          'tarikh_perjalanan': j.tarikhPerjalanan.toIso8601String().split('T')[0],
          'masa_keluar': j.masaKeluar,
          'start_dt': startDt.toIso8601String(),
          'is_synced': true,
        };
      }).toList();

      // Sort newest first
      logs.sort((a, b) {
        final ad = DateTime.tryParse(a['start_dt'] ?? '') ?? DateTime.fromMillisecondsSinceEpoch(0);
        final bd = DateTime.tryParse(b['start_dt'] ?? '') ?? DateTime.fromMillisecondsSinceEpoch(0);
        return bd.compareTo(ad);
      });
      
      
      
      // Debug: Print Hive storage stats
      final hiveStats = HiveService.getStorageStats();
      
      
      // If editing existing claim, populate fields after logs are loaded
      int? tempLogId;
      String? tempCategory;
      
      if (widget.existingClaim != null) {
        final claim = widget.existingClaim!;
        tempLogId = claim['log_pemandu_id'];
        tempCategory = claim['kategori'];
        amountController.text = claim['jumlah'].toString();
        descriptionController.text = claim['keterangan'] ?? '';
      }
      
      setState(() {
        completedLogs = logs;
        selectedCategory = tempCategory;
        // CRITICAL: Reset selectedLogId to null if not found in logs
        selectedLogId = (tempLogId != null && logs.any((log) => log['id'] == tempLogId)) 
            ? tempLogId 
            : null;
        isLoading = false;
      });
    } catch (e) {
    setState(() {
        completedLogs = [];
        selectedLogId = null;
        selectedCategory = null;
        isLoading = false;
      });
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error loading data: $e')),
        );
      }
    }
  }

  Future<void> _pickReceiptImage() async {
    showModalBottomSheet(
      context: context,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(16)),
      ),
      builder: (BuildContext context) {
        return Container(
          padding: const EdgeInsets.all(16),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              ListTile(
                leading: Icon(Icons.camera_alt, color: PastelColors.primary),
                title: const Text('Ambil Gambar'),
                subtitle: const Text('Gunakan camera'),
                onTap: () {
                  Navigator.pop(context);
                  _pickImageFromCamera();
                },
              ),
              ListTile(
                leading: Icon(Icons.photo_library, color: PastelColors.primary),
                title: const Text('Pilih dari Gallery'),
                subtitle: const Text('Pilih gambar sedia ada'),
                onTap: () {
                  Navigator.pop(context);
                  _pickImageFromGallery();
                },
              ),
            ],
          ),
        );
      },
    );
  }

  Future<void> _pickImageFromCamera() async {
    try {
      final XFile? image = await _picker.pickImage(
        source: ImageSource.camera,
        imageQuality: 80,
      );
      if (image != null) {
        setState(() => receiptImage = image);
      }
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Error mengambil gambar: $e')),
      );
    }
  }

  Future<void> _pickImageFromGallery() async {
    try {
      final XFile? image = await _picker.pickImage(
        source: ImageSource.gallery,
        imageQuality: 80,
      );
      if (image != null) {
        setState(() => receiptImage = image);
      }
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Error memilih gambar: $e')),
      );
    }
  }

  void _removeReceiptImage() {
    setState(() => receiptImage = null);
  }

  /// Save claim offline to Hive
  Future<void> _saveClaimOffline(double amount, int userId) async {
    try {
      // Generate unique local ID
      const uuid = Uuid();
      final localId = uuid.v4();
      
      // Save receipt photo to local storage
      String? localPhotoPath;
      if (receiptImage != null) {
        final appDir = await getApplicationDocumentsDirectory();
        final fileName = 'claim_${localId}_${DateTime.now().millisecondsSinceEpoch}.jpg';
        final localFile = File('${appDir.path}/receipts/$fileName');
        
        // Create directory if not exists
        await localFile.parent.create(recursive: true);
        
        // Copy photo to local storage
        final bytes = await receiptImage!.readAsBytes();
        await localFile.writeAsBytes(bytes);
        localPhotoPath = localFile.path;
        
        
      }
      
      // Create ClaimHive object
      final claim = ClaimHive(
        id: null,  // Will be set when synced
        logPemanduId: selectedLogId,
        kategori: selectedCategory!,
        jumlah: amount,
        catatan: descriptionController.text.isNotEmpty ? descriptionController.text : null,
        resit: null,  // Server path, will be set when synced
        resitLocal: localPhotoPath,  // Local device path
        status: 'pending',
        diciptaOleh: userId,
        localId: localId,
        isSynced: false,
        createdAt: DateTime.now(),
      );
      
      // Save to Hive
      await HiveService.saveClaim(claim);
      
      
      
      setState(() => isSubmitting = false);
      
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Row(
              children: [
                Icon(Icons.offline_pin, color: Colors.white),
                SizedBox(width: 8),
                Expanded(
                  child: Text('Tuntutan disimpan offline. Akan sync bila online.'),
                ),
              ],
            ),
            backgroundColor: Colors.orange,
            duration: Duration(seconds: 4),
          ),
        );
        Navigator.pop(context, true);  // Return success
      }
    } catch (e) {
      
      setState(() => isSubmitting = false);
      
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Gagal menyimpan offline: $e'),
            backgroundColor: Colors.red,
          ),
        );
      }
    }
  }

  /// Update existing rejected claim offline (edit & resubmit later)
  Future<void> _updateClaimOffline(double amount, int userId) async {
    try {
      // Identify existing claim in Hive
      final localId = widget.existingClaim?['local_id']?.toString();
      final claimId = widget.existingClaim?['id'];
      ClaimHive? existing;
      if (localId != null) {
        existing = HiveService.getClaimByLocalId(localId);
      }
      // Fallback search by server id if needed
      existing ??= HiveService.getAllClaims().firstWhere(
        (c) => c.id != null && c.id == claimId,
        orElse: () => ClaimHive(
          id: claimId,
          logPemanduId: selectedLogId,
          kategori: selectedCategory ?? 'others',
          jumlah: amount,
          diciptaOleh: userId,
          localId: const Uuid().v4(),
        ),
      );

      // Save receipt photo to local storage (if a new image is picked)
      String? localPhotoPath = existing.resitLocal;
      if (receiptImage != null) {
        final appDir = await getApplicationDocumentsDirectory();
        final fileName = 'claim_${existing.localId}_${DateTime.now().millisecondsSinceEpoch}.jpg';
        final localFile = File('${appDir.path}/receipts/$fileName');
        await localFile.parent.create(recursive: true);
        final bytes = await receiptImage!.readAsBytes();
        await localFile.writeAsBytes(bytes);
        localPhotoPath = localFile.path;
        
      }

      // Update fields
      existing.logPemanduId = selectedLogId;
      existing.kategori = selectedCategory ?? existing.kategori;
      existing.jumlah = amount;
      existing.catatan = descriptionController.text.isNotEmpty ? descriptionController.text : existing.catatan;
      existing.resitLocal = localPhotoPath; // may be unchanged
      existing.status = 'pending'; // resubmission
      existing.isSynced = false;   // needs sync
      existing.syncRetries = 0;
      existing.syncError = null;
      existing.lastSyncAttempt = null;
      existing.updatedAt = DateTime.now();

      await HiveService.updateClaim(existing);

      

      setState(() => isSubmitting = false);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Tuntutan dikemaskini offline. Akan sync bila online.'),
            backgroundColor: Colors.orange,
          ),
        );
        Navigator.pop(context, true);
      }
    } catch (e) {
      
      setState(() => isSubmitting = false);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Gagal kemaskini offline: $e'), backgroundColor: Colors.red),
        );
      }
    }
  }

  Future<void> _submitClaim() async {
    // Validation
    if (selectedLogId == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Sila pilih Log Perjalanan')),
      );
      return;
    }

    if (selectedCategory == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Sila pilih Kategori')),
      );
      return;
    }

    if (amountController.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Sila masukkan Jumlah')),
      );
      return;
    }

    final amount = double.tryParse(amountController.text);
    if (amount == null || amount <= 0) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Jumlah tidak sah')),
      );
      return;
    }

    setState(() => isSubmitting = true);

    // ============================================
    // CHECK CONNECTIVITY (Fresh check before submit!)
    // ============================================
    final connectivity = context.read<ConnectivityService>();
    final authService = context.read<AuthService>();
    
    // Perform fresh connectivity check (don't trust stale status)
    
    final isReallyOnline = await connectivity.checkConnection();
    
    if (!isReallyOnline) {
      // OFFLINE MODE
      
      final uid = authService.userId;
      if (uid == null) {
        setState(() => isSubmitting = false);
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('Error: User not authenticated'), backgroundColor: Colors.red),
          );
        }
        return;
      }
      if (widget.existingClaim != null) {
        
        await _updateClaimOffline(amount, uid);
      } else {
        
        await _saveClaimOffline(amount, uid);
      }
      return;
    }

    // ONLINE MODE - Upload to server (current behavior)
    
    try {
      // Prepare receipt image bytes if exists
      List<int>? resitBytes;
      String? resitFilename;
      
      if (receiptImage != null) {
        resitBytes = await receiptImage!.readAsBytes();
        resitFilename = receiptImage!.name;
      }

      Map<String, dynamic> response;
      
      // Create or Update
      if (widget.existingClaim != null) {
        // Update existing claim (edit mode)
        response = await _apiService.updateClaim(
          id: widget.existingClaim!['id'],
          kategori: selectedCategory!,
          jumlah: amount,
          keterangan: descriptionController.text.isNotEmpty ? descriptionController.text : null,
          resitBytes: resitBytes,
          resitFilename: resitFilename,
        );
      } else {
        // Create new claim
        response = await _apiService.createClaim(
          logPemanduId: selectedLogId!,
          kategori: selectedCategory!,
          jumlah: amount,
          keterangan: descriptionController.text.isNotEmpty ? descriptionController.text : null,
          resitBytes: resitBytes,
          resitFilename: resitFilename,
        );
      }

      setState(() => isSubmitting = false);

      if (response['success'] == true && mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(widget.existingClaim != null 
              ? 'Tuntutan berjaya dikemaskini!' 
              : 'Tuntutan berjaya dihantar!'),
            backgroundColor: Colors.green,
          ),
        );
        Navigator.pop(context, true); // Return true to indicate success
      }
    } on DioException catch (e) {
      // Check if it's a connection error (server down/timeout)
      final isConnectionError = e.type == DioExceptionType.connectionTimeout ||
                                 e.type == DioExceptionType.receiveTimeout ||
                                 e.type == DioExceptionType.connectionError ||
                                 e.type == DioExceptionType.unknown;
      
      if (isConnectionError) {
        // Connection error - fallback to offline mode
        
        
        // Mark as offline and save to Hive
        final connectivity = context.read<ConnectivityService>();
        connectivity.checkConnection();  // Trigger recheck (will update indicator)
        
        // Re-get authService in case scope changed
        final auth = context.read<AuthService>();
        if (auth.userId != null) {
          await _saveClaimOffline(amount, auth.userId!);
        } else {
          // No user ID - show error
          setState(() => isSubmitting = false);
          if (mounted) {
            ScaffoldMessenger.of(context).showSnackBar(
              const SnackBar(
                content: Text('Error: User not authenticated'),
                backgroundColor: Colors.red,
              ),
            );
          }
        }
        return;
      }
      
      // Validation errors (e.g., log_pemandu_id not exists) → offer offline save
      if (e.response?.statusCode == 422) {
        final auth = context.read<AuthService>();
        if (auth.userId != null) {
          await _saveClaimOffline(amount, auth.userId!);
          return;
        }
      }

      // Other errors (validation without offline, server 4xx/5xx) - show error
      setState(() => isSubmitting = false);
      String errorMessage = 'Ralat tidak diketahui';
      if (e.response != null) {
        if (e.response!.data is Map && e.response!.data['message'] != null) {
          errorMessage = e.response!.data['message'];
        } else if (e.response!.data is Map && e.response!.data['errors'] != null) {
          final errors = e.response!.data['errors'] as Map;
          errorMessage = errors.values.first[0];
        } else {
          errorMessage = e.response!.statusMessage ?? errorMessage;
        }
      } else {
        errorMessage = e.message ?? errorMessage;
      }
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error: $errorMessage'), backgroundColor: Colors.red),
        );
      }
    } catch (e) {
      setState(() => isSubmitting = false);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error: $e'), backgroundColor: Colors.red),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: PastelColors.background,
      appBar: AppBar(
        backgroundColor: PastelColors.primary,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: Colors.white),
          onPressed: () => Navigator.pop(context),
        ),
        title: Row(
          children: [
            const Icon(Icons.receipt, color: Colors.white, size: 20),
            const SizedBox(width: 8),
            Text(
              widget.existingClaim != null ? 'Edit Claim' : 'Create Claim',
              style: AppTextStyles.h2.copyWith(color: Colors.white),
            ),
          ],
        ),
      ),
      body: (isLoading || completedLogs == null)
          ? const Center(child: CircularProgressIndicator())
          : SingleChildScrollView(
              padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                  // Log Perjalanan Selection
                  Text('Log Perjalanan', style: AppTextStyles.h2),
                  const SizedBox(height: 8),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(8),
                      border: Border.all(color: PastelColors.border),
                    ),
                    child: DropdownButtonFormField<int>(
                            value: selectedLogId,
                            decoration: const InputDecoration(
                              border: InputBorder.none,
                              hintText: 'Pilih Log Perjalanan',
                            ),
                            selectedItemBuilder: (ctx) {
                              final items = completedLogs ?? [];
                              return items.map<Widget>((log) {
                                final programName = log['program']?['nama_program'] ?? 'Tiada Program';
                                return Align(
                                  alignment: Alignment.centerLeft,
                                  child: Text(programName, overflow: TextOverflow.ellipsis, maxLines: 1, style: AppTextStyles.bodyLarge),
                                );
                              }).toList();
                            },
                            items: completedLogs == null || completedLogs!.isEmpty 
                                ? [DropdownMenuItem<int>(
                                    value: null,
                                    enabled: false,
                                    child: Text('Tiada log perjalanan selesai', style: AppTextStyles.bodyMedium.copyWith(color: Colors.grey)),
                                  )]
                                : completedLogs!.map((log) {
                                    // Build display datetime from normalized start_dt
                                    String dateStr = '';
                                    try {
                                      final myDate = DateTime.parse(log['start_dt']);
                                      const months = ['January', 'February', 'March', 'April', 'May', 'June',
                                        'July', 'August', 'September', 'October', 'November', 'December'];
                                      final hour12 = myDate.hour == 0 ? 12 : (myDate.hour > 12 ? myDate.hour - 12 : myDate.hour);
                                      final ampm = myDate.hour >= 12 ? 'pm' : 'am';
                                      dateStr = '${myDate.day} ${months[myDate.month - 1]} ${myDate.year} - ${hour12.toString().padLeft(2, '0')}:${myDate.minute.toString().padLeft(2, '0')} $ampm';
                                    } catch (_) {
                                      dateStr = '${log['tarikh_perjalanan']} ${log['masa_keluar'] ?? ''}';
                                    }
                                    
                                    final isSynced = log['is_synced'] == true && (log['id'] ?? 0) != 0;
                                    return DropdownMenuItem<int>(
                                      value: isSynced ? (log['id'] as int) : null,
                                      enabled: isSynced,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                                          Text(
                                            log['program']?['nama_program'] ?? 'Tiada Program',
                                            style: AppTextStyles.bodyLarge.copyWith(fontWeight: FontWeight.w600),
                                          ),
                                          Text(
                                            isSynced ? '$dateStr' : 'Belum diselaraskan (tidak boleh hantar tuntutan)',
                                            style: AppTextStyles.bodySmall.copyWith(color: isSynced ? Colors.grey : Colors.orange),
                                          ),
                                        ],
                                      ),
                                    );
                                  }).toList(),
                            onChanged: widget.existingClaim != null ? null : (value) {
                              setState(() => selectedLogId = value);
                            },
                            isExpanded: true,
                          ),
                  ),

                  const SizedBox(height: 24),

                  // Category Selection
                  Text('Kategori', style: AppTextStyles.h2),
                  const SizedBox(height: 8),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(8),
                      border: Border.all(color: PastelColors.border),
                    ),
                    child: DropdownButtonFormField<String>(
                  value: selectedCategory,
                      decoration: const InputDecoration(
                        border: InputBorder.none,
                        hintText: 'Pilih Kategori',
                      ),
                      items: categories.map((cat) {
                        return DropdownMenuItem<String>(
                          value: cat['value'],
                          child: Text(cat['label']!),
                        );
                      }).toList(),
                      onChanged: (value) {
                        setState(() => selectedCategory = value);
                      },
                      isExpanded: true,
                    ),
                  ),

                  const SizedBox(height: 24),

                  // Amount
                  Text('Jumlah (RM)', style: AppTextStyles.h2),
                  const SizedBox(height: 8),
                TextField(
                  controller: amountController,
                    keyboardType: const TextInputType.numberWithOptions(decimal: true),
                  decoration: InputDecoration(
                      hintText: 'Contoh: 12.50',
                      prefixText: 'RM ',
                      filled: true,
                      fillColor: Colors.white,
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(8),
                        borderSide: BorderSide(color: PastelColors.border),
                      ),
                      enabledBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(8),
                        borderSide: BorderSide(color: PastelColors.border),
                      ),
                    ),
                  ),

                  const SizedBox(height: 24),

                  // Description
                  Text('Keterangan (Optional)', style: AppTextStyles.h2),
                  const SizedBox(height: 8),
                  TextField(
                    controller: descriptionController,
                    maxLines: 3,
                    decoration: InputDecoration(
                      hintText: 'Masukkan keterangan tambahan...',
                      filled: true,
                      fillColor: Colors.white,
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(8),
                        borderSide: BorderSide(color: PastelColors.border),
                      ),
                      enabledBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(8),
                        borderSide: BorderSide(color: PastelColors.border),
                      ),
                    ),
                  ),

                  const SizedBox(height: 24),

                  // Receipt Image
                  Text('Resit (Optional)', style: AppTextStyles.h2),
                  const SizedBox(height: 8),
                  
                  if (receiptImage == null)
                    GestureDetector(
                      onTap: _pickReceiptImage,
                      child: Container(
                        width: double.infinity,
                        height: 150,
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(8),
                          border: Border.all(color: PastelColors.border, width: 2, style: BorderStyle.solid),
                        ),
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Icon(Icons.add_photo_alternate, size: 48, color: PastelColors.primary),
                            const SizedBox(height: 8),
                            Text('Tap untuk muat naik resit', style: AppTextStyles.bodyMedium.copyWith(color: Colors.grey)),
                          ],
                        ),
                      ),
                    )
                  else
                    Stack(
                        children: [
                          Container(
                          width: double.infinity,
                          height: 200,
                            decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(8),
                              border: Border.all(color: PastelColors.border),
                          ),
                          child: ClipRRect(
                            borderRadius: BorderRadius.circular(8),
                            child: FutureBuilder<Uint8List>(
                              future: receiptImage!.readAsBytes(),
                              builder: (context, snapshot) {
                                if (snapshot.hasData) {
                                  return Image.memory(
                                    snapshot.data!,
                                    fit: BoxFit.cover,
                                  );
                                } else {
                                  return const Center(child: CircularProgressIndicator());
                                }
                              },
                            ),
                          ),
                          ),
                          Positioned(
                          top: 8,
                          right: 8,
                            child: IconButton(
                            icon: const Icon(Icons.close, color: Colors.red),
                            onPressed: _removeReceiptImage,
                            style: IconButton.styleFrom(
                              backgroundColor: Colors.white,
                              padding: const EdgeInsets.all(8),
                            ),
                          ),
                    ),
                  ],
                ),

                  const SizedBox(height: 32),

                  // Submit Button
                SizedBox(
                  width: double.infinity,
                    height: 50,
                  child: ElevatedButton(
                      onPressed: isSubmitting ? null : _submitClaim,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: PastelColors.primary,
                      foregroundColor: Colors.white,
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(8),
                        ),
                        elevation: 0,
                      ),
                      child: isSubmitting
                          ? const CircularProgressIndicator(color: Colors.white)
                          : Text(
                              widget.existingClaim != null ? 'KEMASKINI TUNTUTAN' : 'HANTAR TUNTUTAN',
                              style: AppTextStyles.h2.copyWith(color: Colors.white),
                            ),
                  ),
                ),
              ],
        ),
      ),
    );
  }
  }
