import 'dart:io';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import '../theme/pastel_colors.dart';
import '../theme/text_styles.dart';

class ClaimScreen extends StatefulWidget {
  const ClaimScreen({super.key});

  @override
  State<ClaimScreen> createState() => _ClaimScreenState();
}

class _ClaimScreenState extends State<ClaimScreen> {
  String? selectedProgram;
  String? selectedCategory;
  final TextEditingController amountController = TextEditingController();
  List<File> claimImages = [];
  final ImagePicker _picker = ImagePicker();
  bool isSubmitting = false;

  // 🎨 DUMMY DATA - Will be replaced with API data later
  final dummyPrograms = [
    {
      'name': 'Program Jelajah Madani',
      'date': '2024-07-20',
      'location': 'Dewan Sibu Jaya',
      'desc': 'Program rasmi RISDA Sibu.',
      'est_km': '120',
      'actual_mileage': '100',
      'notes': 'Official program for RISDA Sibu.',
      'request_by': 'Ahmad Bin Ali',
    },
    {
      'name': 'Program Inovasi Desa',
      'date': '2024-08-05',
      'location': 'Kampung Sentosa',
      'desc': 'Inovasi dan latihan komuniti.',
      'est_km': '80',
      'actual_mileage': '65',
      'notes': 'Latihan inovasi untuk komuniti.',
      'request_by': 'Siti Aminah',
    },
    {
      'name': 'Program Komuniti Hijau',
      'date': '2024-08-15',
      'location': 'Taman Rekreasi Lembah Hijau',
      'desc': 'Program gotong royong komuniti.',
      'est_km': '45',
      'actual_mileage': '40',
      'notes': 'Aktiviti kebersihan dan keceriaan.',
      'request_by': 'Mohd Fairiz',
    },
  ];

  final dummyCategories = ['Tol', 'Parking', 'Food & Beverage', 'Accommodation', 'Fuel', 'Car Maintenance', 'Others'];

  Map<String, String>? get selectedProgramDetail {
    if (selectedProgram == null) return null;
    
    final found = dummyPrograms.firstWhere(
      (p) => p['name'] == selectedProgram,
      orElse: () => <String, String>{},
    );
    
    if (found.isEmpty) return null;
    return found.map((key, value) => MapEntry(key, value?.toString() ?? ''));
  }

  Future<void> _pickClaimImage() async {
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
      final XFile? photo = await _picker.pickImage(
        source: ImageSource.camera,
        imageQuality: 80,
      );
      
      if (photo != null) {
        setState(() {
          claimImages.add(File(photo.path));
        });
        
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Gambar berjaya ditambah!'),
              backgroundColor: Colors.green,
              duration: Duration(seconds: 2),
            ),
          );
        }
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Camera error: $e'),
            backgroundColor: Colors.orange,
            duration: const Duration(seconds: 3),
          ),
        );
      }
    }
  }

  Future<void> _pickImageFromGallery() async {
    try {
      final XFile? photo = await _picker.pickImage(
        source: ImageSource.gallery,
        imageQuality: 80,
      );
      
      if (photo != null) {
        setState(() {
          claimImages.add(File(photo.path));
        });
        
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Gambar berjaya ditambah!'),
              backgroundColor: Colors.green,
              duration: Duration(seconds: 2),
            ),
          );
        }
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Gallery error: $e'),
            backgroundColor: Colors.orange,
            duration: const Duration(seconds: 3),
          ),
        );
      }
    }
  }

  void _removeClaimImage(int index) {
    setState(() {
      claimImages.removeAt(index);
    });
  }

  Future<void> _submitClaim() async {
    // Validation
    if (selectedProgram == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Sila pilih program'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    if (selectedCategory == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Sila pilih kategori'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    if (amountController.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Sila masukkan jumlah'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    setState(() {
      isSubmitting = true;
    });

    // 🎨 DUMMY MODE - Simulate API call
    await Future.delayed(const Duration(seconds: 2));

    if (mounted) {
      setState(() {
        isSubmitting = false;
      });

      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Claim berjaya dihantar! (Dummy Mode)'),
          backgroundColor: Colors.green,
        ),
      );

      Navigator.pop(context, true);
    }
  }

  @override
  void dispose() {
    amountController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: PastelColors.primary,
        title: Text('Claim', style: AppTextStyles.h2.copyWith(color: Colors.white)),
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      backgroundColor: PastelColors.background,
      body: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 24),
        child: Card(
          color: PastelColors.cardBackground,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(8),
            side: BorderSide(color: PastelColors.border),
          ),
          elevation: 0,
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 24),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Program Name
                _fieldLabel('Program Name'),
                const SizedBox(height: 4),
                DropdownButtonFormField<String>(
                  value: selectedProgram,
                  items: dummyPrograms
                      .map((p) => DropdownMenuItem<String>(
                            value: p['name'] as String,
                            child: Text(p['name'] as String),
                          ))
                      .toList(),
                  onChanged: (v) => setState(() => selectedProgram = v),
                  decoration: InputDecoration(
                    hintText: 'Select Program',
                    border: OutlineInputBorder(borderRadius: BorderRadius.circular(3)),
                    isDense: true,
                    contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                    prefixIcon: Icon(Icons.event, color: PastelColors.primary, size: 20),
                  ),
                  style: AppTextStyles.bodyLarge,
                ),
                
                // Program Details (shown when program selected)
                if (selectedProgramDetail != null) ...[
                  const SizedBox(height: 10),
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: PastelColors.background,
                      border: Border.all(color: PastelColors.border),
                      borderRadius: BorderRadius.circular(6),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _detailRow('Program Name', selectedProgramDetail!['name'] ?? ''),
                        _detailRow('Date', selectedProgramDetail!['date'] ?? ''),
                        _detailRow('Location', selectedProgramDetail!['location'] ?? ''),
                        _detailRow('Description', selectedProgramDetail!['desc'] ?? ''),
                        _detailRow('Estimation KM', selectedProgramDetail!['est_km'] ?? ''),
                        _detailRow('Actual Mileage', selectedProgramDetail!['actual_mileage'] ?? ''),
                        _detailRow('Request By', selectedProgramDetail!['request_by'] ?? ''),
                        _detailRow('Notes', selectedProgramDetail!['notes'] ?? ''),
                      ],
                    ),
                  ),
                ],
                const SizedBox(height: 16),

                // Category
                _fieldLabel('Category'),
                const SizedBox(height: 4),
                DropdownButtonFormField<String>(
                  value: selectedCategory,
                  items: dummyCategories
                      .map((c) => DropdownMenuItem(value: c, child: Text(c)))
                      .toList(),
                  onChanged: (v) => setState(() => selectedCategory = v),
                  decoration: InputDecoration(
                    hintText: 'Select Category',
                    border: OutlineInputBorder(borderRadius: BorderRadius.circular(3)),
                    isDense: true,
                    contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                    prefixIcon: Icon(Icons.category, color: PastelColors.primary, size: 20),
                  ),
                  style: AppTextStyles.bodyLarge,
                ),
                const SizedBox(height: 16),

                // Amount
                _fieldLabel('Amount (RM)'),
                const SizedBox(height: 4),
                TextField(
                  controller: amountController,
                  keyboardType: const TextInputType.numberWithOptions(decimal: true),
                  decoration: InputDecoration(
                    hintText: '00.00',
                    border: OutlineInputBorder(borderRadius: BorderRadius.circular(3)),
                    isDense: true,
                    contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                    prefixIcon: Icon(Icons.attach_money, color: PastelColors.primary, size: 20),
                  ),
                  style: AppTextStyles.bodyLarge,
                ),
                const SizedBox(height: 16),

                // Claim Photo
                _fieldLabel('Claim Photo'),
                const SizedBox(height: 4),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    ElevatedButton.icon(
                      onPressed: _pickClaimImage,
                      icon: const Icon(Icons.add_a_photo, size: 18),
                      label: Text('Take Picture', style: AppTextStyles.bodyLarge),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: PastelColors.primary,
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(3)),
                      ),
                    ),
                    const SizedBox(height: 8),
                    if (claimImages.isNotEmpty)
                      Wrap(
                        spacing: 8,
                        runSpacing: 8,
                        children: List.generate(
                          claimImages.length,
                          (i) => Stack(
                            alignment: Alignment.topRight,
                            children: [
                              Container(
                                width: 64,
                                height: 64,
                                decoration: BoxDecoration(
                                  border: Border.all(color: PastelColors.border),
                                  borderRadius: BorderRadius.circular(6),
                                ),
                                child: ClipRRect(
                                  borderRadius: BorderRadius.circular(6),
                                  child: Image.file(
                                    claimImages[i],
                                    fit: BoxFit.cover,
                                  ),
                                ),
                              ),
                              Positioned(
                                top: -8,
                                right: -8,
                                child: IconButton(
                                  icon: Icon(
                                    Icons.cancel,
                                    color: PastelColors.errorText,
                                    size: 20,
                                  ),
                                  onPressed: () => _removeClaimImage(i),
                                  padding: EdgeInsets.zero,
                                  constraints: const BoxConstraints(),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                  ],
                ),
                const SizedBox(height: 28),

                // Submit Button
                SizedBox(
                  width: double.infinity,
                  height: 44,
                  child: ElevatedButton(
                    onPressed: isSubmitting ? null : _submitClaim,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: PastelColors.primary,
                      foregroundColor: Colors.white,
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(3)),
                    ),
                    child: isSubmitting
                        ? const SizedBox(
                            height: 20,
                            width: 20,
                            child: CircularProgressIndicator(
                              strokeWidth: 2,
                              valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                            ),
                          )
                        : Text(
                            'Submit Claim',
                            style: AppTextStyles.bodyLarge.copyWith(color: Colors.white),
                          ),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _fieldLabel(String label) {
    return Text(
      label,
      style: AppTextStyles.bodyMedium.copyWith(
        fontWeight: FontWeight.w600,
        color: PastelColors.textPrimary,
      ),
    );
  }

  Widget _detailRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 4),
      child: Text(
        '$label: $value',
        style: AppTextStyles.bodyMedium,
      ),
    );
  }
}