import 'package:flutter/material.dart';
import '../theme/pastel_colors.dart';
import '../theme/text_styles.dart';
import '../services/api_service.dart';
import '../core/api_client.dart';

class PrivacyPolicyScreen extends StatefulWidget {
  @override
  _PrivacyPolicyScreenState createState() => _PrivacyPolicyScreenState();
}

class _PrivacyPolicyScreenState extends State<PrivacyPolicyScreen> {
  final ApiService _apiService = ApiService(ApiClient());
  
  bool _isLoading = true;
  String _title = 'Privacy Policy';
  String _effectiveDate = '';
  String _lastUpdated = '';
  List<dynamic> _sections = [];
  String _acknowledgment = '';

  @override
  void initState() {
    super.initState();
    _loadPrivacyPolicy();
  }

  Future<void> _loadPrivacyPolicy() async {
    setState(() {
      _isLoading = true;
    });

    try {
      final response = await _apiService.getPrivacyPolicy();
      
      if (response['success'] == true && response['data'] != null) {
        final data = response['data'];
        
        if (mounted) {
          setState(() {
            _title = data['title'] ?? _title;
            _effectiveDate = data['effective_date'] ?? '';
            _lastUpdated = data['last_updated'] ?? '';
            _sections = data['sections'] ?? [];
            _acknowledgment = data['acknowledgment'] ?? '';
            _isLoading = false;
          });
        }
      }
    } catch (e) {
      // Use default values on error
      if (mounted) {
        setState(() {
          _isLoading = false;
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: PastelColors.primary,
        title: Text(_title, style: AppTextStyles.h2.copyWith(color: Colors.white)),
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      backgroundColor: PastelColors.background,
      body: _isLoading
          ? Center(
              child: CircularProgressIndicator(color: PastelColors.primary),
            )
          : SingleChildScrollView(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 24),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  // Header Card
                  Card(
                    color: PastelColors.cardBackground,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(8),
                      side: BorderSide(color: PastelColors.border),
                    ),
                    elevation: 0,
                    child: Padding(
                      padding: const EdgeInsets.all(20),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(_title, style: AppTextStyles.h2),
                          if (_effectiveDate.isNotEmpty) ...[
                            const SizedBox(height: 8),
                            Text('Effective Date: $_effectiveDate', style: AppTextStyles.bodyMedium.copyWith(color: Colors.grey[600])),
                          ],
                          if (_lastUpdated.isNotEmpty) ...[
                            const SizedBox(height: 4),
                            Text('Last Updated: $_lastUpdated', style: AppTextStyles.bodyMedium.copyWith(color: Colors.grey[600])),
                          ],
                        ],
                      ),
                    ),
                  ),
                  
                  const SizedBox(height: 16),
                  
                  // Sections
                  ..._sections.map((section) => _buildSection(section)).toList(),
                  
                  // Acknowledgment
                  if (_acknowledgment.isNotEmpty) ...[
                    const SizedBox(height: 16),
                    Card(
                      color: PastelColors.primary.withOpacity(0.1),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(8),
                        side: BorderSide(color: PastelColors.primary.withOpacity(0.3)),
                      ),
                      elevation: 0,
                      child: Padding(
                        padding: const EdgeInsets.all(20),
                        child: Row(
                          children: [
                            Icon(Icons.info_outline, color: PastelColors.primary, size: 24),
                            const SizedBox(width: 12),
                            Expanded(
                              child: Text(_acknowledgment, style: AppTextStyles.bodyMedium.copyWith(fontWeight: FontWeight.w600)),
                            ),
                          ],
                        ),
                      ),
                    ),
                  ],
                  
                  const SizedBox(height: 24),
                ],
              ),
            ),
    );
  }

  Widget _buildSection(dynamic section) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 16),
      child: Card(
        color: PastelColors.cardBackground,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(8),
          side: BorderSide(color: PastelColors.border),
        ),
        elevation: 0,
        child: Padding(
          padding: const EdgeInsets.all(20),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              if (section['heading'] != null) ...[
                Text(section['heading'], style: AppTextStyles.h3),
                const SizedBox(height: 12),
              ],
              if (section['content'] != null) ...[
                Text(section['content'], style: AppTextStyles.bodyMedium, textAlign: TextAlign.justify),
                const SizedBox(height: 8),
              ],
              if (section['list'] != null) ...[
                ...(section['list'] as List<dynamic>).map((item) => 
                  Padding(
                    padding: const EdgeInsets.only(left: 16, bottom: 8),
                    child: Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('• ', style: AppTextStyles.bodyMedium.copyWith(fontWeight: FontWeight.bold)),
                        Expanded(
                          child: Text(item, style: AppTextStyles.bodyMedium),
                        ),
                      ],
                    ),
                  ),
                ).toList(),
              ],
              if (section['footer'] != null) ...[
                const SizedBox(height: 8),
                Text(section['footer'], style: AppTextStyles.bodyMedium.copyWith(fontStyle: FontStyle.italic)),
              ],
              if (section['contact'] != null) ...[
                const SizedBox(height: 8),
                _buildContactInfo(section['contact']),
              ],
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildContactInfo(Map<String, dynamic> contact) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.grey[100],
        borderRadius: BorderRadius.circular(8),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          if (contact['organization'] != null)
            Text(contact['organization'], style: AppTextStyles.bodyLarge.copyWith(fontWeight: FontWeight.w600)),
          if (contact['address'] != null) ...[
            const SizedBox(height: 8),
            ...(contact['address'] as List<dynamic>).map((line) => 
              Text(line, style: AppTextStyles.bodyMedium),
            ).toList(),
          ],
          if (contact['phone'] != null) ...[
            const SizedBox(height: 8),
            Row(
              children: [
                Icon(Icons.phone, size: 16, color: Colors.grey[600]),
                const SizedBox(width: 8),
                Text(contact['phone'], style: AppTextStyles.bodyMedium),
              ],
            ),
          ],
          if (contact['fax'] != null) ...[
            const SizedBox(height: 4),
            Row(
              children: [
                Icon(Icons.fax, size: 16, color: Colors.grey[600]),
                const SizedBox(width: 8),
                Text('Fax: ${contact['fax']}', style: AppTextStyles.bodyMedium),
              ],
            ),
          ],
          if (contact['email'] != null) ...[
            const SizedBox(height: 4),
            Row(
              children: [
                Icon(Icons.email, size: 16, color: Colors.grey[600]),
                const SizedBox(width: 8),
                Text(contact['email'], style: AppTextStyles.bodyMedium),
              ],
            ),
          ],
          if (contact['website'] != null) ...[
            const SizedBox(height: 4),
            Row(
              children: [
                Icon(Icons.language, size: 16, color: Colors.grey[600]),
                const SizedBox(width: 8),
                Text(contact['website'], style: AppTextStyles.bodyMedium),
              ],
            ),
          ],
        ],
      ),
    );
  }
}

