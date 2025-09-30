import 'dart:io';
import 'package:flutter/material.dart';
import '../theme/pastel_colors.dart';
import '../theme/text_styles.dart';

class ClaimScreen extends StatefulWidget {
  @override
  State<ClaimScreen> createState() => _ClaimScreenState();
}

class _ClaimScreenState extends State<ClaimScreen> {
  String? selectedProgram;
  String? selectedCategory;
  final TextEditingController amountController = TextEditingController();
  List<File> claimImages = [];

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
  ];
  final dummyCategories = ['Refuel', 'Maintenance', 'Toll', 'Parking', 'Other'];

  void _pickClaimImage() async {
    setState(() {
      claimImages.add(File('dummy_path_${claimImages.length + 1}'));
    });
  }

  void _removeClaimImage(int index) {
    setState(() {
      claimImages.removeAt(index);
    });
  }

  Map<String, String>? get selectedProgramDetail {
    final found = dummyPrograms.firstWhere(
      (p) => p['name'] == selectedProgram,
      orElse: () => <String, String>{},
    );
    if (found.isEmpty) return null;
    if (found is Map<String, String>) return found;
    // Convert Map<String, dynamic> to Map<String, String>
    return found.map((key, value) => MapEntry(key, value?.toString() ?? ''));
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
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8), side: BorderSide(color: PastelColors.border)),
          elevation: 0,
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 24),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                _fieldLabel('Program Name'),
                const SizedBox(height: 4),
                DropdownButtonFormField<String>(
                  value: selectedProgram,
                  items: dummyPrograms.map((p) => DropdownMenuItem<String>(value: p['name'] as String, child: Text(p['name'] as String))).toList(),
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
                        Text('Program Name: ' + (selectedProgramDetail!['name'] ?? ''), style: AppTextStyles.bodyMedium),
                        Text('Date: ' + (selectedProgramDetail!['date'] ?? ''), style: AppTextStyles.bodyMedium),
                        Text('Location: ' + (selectedProgramDetail!['location'] ?? ''), style: AppTextStyles.bodyMedium),
                        Text('Description: ' + (selectedProgramDetail!['desc'] ?? ''), style: AppTextStyles.bodyMedium),
                        Text('Estimation KM: ' + (selectedProgramDetail!['est_km'] ?? ''), style: AppTextStyles.bodyMedium),
                        Text('Estimation (Actual Mileage): ' + (selectedProgramDetail!['actual_mileage'] ?? ''), style: AppTextStyles.bodyMedium),
                        Text('Request By: ' + (selectedProgramDetail!['request_by'] ?? ''), style: AppTextStyles.bodyMedium),
                        Text('Notes: ' + (selectedProgramDetail!['notes'] ?? ''), style: AppTextStyles.bodyMedium),
                      ],
                    ),
                  ),
                ],
                const SizedBox(height: 16),
                _fieldLabel('Category'),
                const SizedBox(height: 4),
                DropdownButtonFormField<String>(
                  value: selectedCategory,
                  items: dummyCategories.map((c) => DropdownMenuItem(value: c, child: Text(c))).toList(),
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
                _fieldLabel('Amount (RM)'),
                const SizedBox(height: 4),
                TextField(
                  controller: amountController,
                  keyboardType: TextInputType.numberWithOptions(decimal: true),
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
                _fieldLabel('Claim Photo'),
                const SizedBox(height: 4),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        ElevatedButton.icon(
                          onPressed: _pickClaimImage,
                          icon: Icon(Icons.add_a_photo, size: 18),
                          label: Text('Take Picture', style: AppTextStyles.bodyLarge),
                          style: ElevatedButton.styleFrom(
                            backgroundColor: PastelColors.primary,
                            foregroundColor: Colors.white,
                            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                            textStyle: AppTextStyles.bodyLarge,
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(3)),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 8),
                    Wrap(
                      spacing: 8,
                      children: List.generate(claimImages.length, (i) => Stack(
                        alignment: Alignment.topRight,
                        children: [
                          Container(
                            width: 64,
                            height: 64,
                            decoration: BoxDecoration(
                              color: PastelColors.background,
                              border: Border.all(color: PastelColors.border),
                              borderRadius: BorderRadius.circular(6),
                            ),
                            child: Icon(Icons.image, size: 32, color: PastelColors.textLight),
                          ),
                          Positioned(
                            top: -8,
                            right: -8,
                            child: IconButton(
                              icon: Icon(Icons.cancel, color: PastelColors.errorText, size: 20),
                              onPressed: () => _removeClaimImage(i),
                              padding: EdgeInsets.zero,
                              constraints: BoxConstraints(),
                            ),
                          ),
                        ],
                      )),
                    ),
                  ],
                ),
                const SizedBox(height: 28),
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton(
                    onPressed: () {},
                    style: ElevatedButton.styleFrom(
                      backgroundColor: PastelColors.primary,
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(vertical: 16),
                      textStyle: AppTextStyles.bodyLarge,
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(3)),
                    ),
                    child: Text('Submit Claim', style: AppTextStyles.bodyLarge.copyWith(color: Colors.white)),
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
    return Text(label, style: AppTextStyles.bodyMedium.copyWith(color: PastelColors.textSecondary));
  }
} 