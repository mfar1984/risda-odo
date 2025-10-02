import 'dart:typed_data';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:dio/dio.dart';
import '../theme/pastel_colors.dart';
import '../theme/text_styles.dart';
import '../services/api_service.dart';
import '../core/api_client.dart';

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
      // Load completed logs first
      final logsResponse = await _apiService.getDriverLogs(status: 'selesai');
      final logs = List<Map<String, dynamic>>.from(logsResponse['data'] ?? []);
      
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
        
        // Debug logging
        print('✅ ClaimScreen: Loaded ${logs.length} logs');
        print('✅ ClaimScreen: selectedLogId = $selectedLogId');
        if (logs.isNotEmpty) {
          print('✅ ClaimScreen: First log ID = ${logs.first['id']}');
        }
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
                            items: completedLogs == null || completedLogs!.isEmpty 
                                ? [DropdownMenuItem<int>(
                                    value: null,
                                    enabled: false,
                                    child: Text('Tiada log perjalanan selesai', style: AppTextStyles.bodyMedium.copyWith(color: Colors.grey)),
                                  )]
                                : completedLogs!.map((log) {
                                    // Use masa_keluar (start journey time) instead of tarikh_perjalanan
                                    String dateStr = log['masa_keluar'] ?? '';
                                    if (dateStr.isNotEmpty) {
                                      try {
                                        final utcDate = DateTime.parse(dateStr);
                                        // Convert to Malaysia timezone (+8 hours)
                                        final myDate = utcDate.add(const Duration(hours: 8));
                                        
                                        // Month names
                                        const months = ['January', 'February', 'March', 'April', 'May', 'June',
                                                       'July', 'August', 'September', 'October', 'November', 'December'];
                                        
                                        // 12-hour format with am/pm
                                        final hour12 = myDate.hour == 0 ? 12 : (myDate.hour > 12 ? myDate.hour - 12 : myDate.hour);
                                        final ampm = myDate.hour >= 12 ? 'pm' : 'am';
                                        
                                        dateStr = '${myDate.day} ${months[myDate.month - 1]} ${myDate.year} - ${hour12.toString().padLeft(2, '0')}:${myDate.minute.toString().padLeft(2, '0')} $ampm';
                                      } catch (e) {
                                        // Keep original if parsing fails
                                      }
                                    }
                                    
                                    return DropdownMenuItem<int>(
                                      value: log['id'],
                                      child: Column(
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: [
                                          Text(
                                            log['program']?['nama_program'] ?? 'Tiada Program',
                                            style: AppTextStyles.bodyLarge.copyWith(fontWeight: FontWeight.w600),
                                          ),
                                          Text(
                                            '$dateStr - ${log['kenderaan']?['no_plat'] ?? 'N/A'}',
                                            style: AppTextStyles.bodySmall.copyWith(color: Colors.grey),
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
