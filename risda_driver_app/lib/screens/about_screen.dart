import 'package:flutter/material.dart';
import '../theme/pastel_colors.dart';
import '../theme/text_styles.dart';
import '../services/api_service.dart';
import '../core/api_client.dart';

class AboutScreen extends StatefulWidget {
  @override
  _AboutScreenState createState() => _AboutScreenState();
}

class _AboutScreenState extends State<AboutScreen> {
  final ApiService _apiService = ApiService(ApiClient());
  
  bool _isLoading = true;
  String _appName = 'JARA Mobile App';
  String _systemFullName = 'JARA (Jejak Aset & Rekod Automotif)';
  String _version = '1.0.0';
  int _buildNumber = 100;
  String _releaseDate = 'Loading...';
  String _organization = 'RISDA';
  String _department = 'RISDA Bahagian Sibu';
  Map<String, dynamic>? _address;
  List<String> _phone = ['084-344712', '084-344713'];
  String _fax = '084-322531';
  String _email = 'prbsibu@risda.gov.my';
  String _backendUrl = 'https://jara.my';
  String _websiteUrl = 'https://www.jara.com.my';
  List<String> _supportedPlatforms = ['Android', 'iOS'];
  String _purpose = '';
  String _description = '';
  List<String> _keyFeatures = [];
  String _copyright = '© 1973 - ${DateTime.now().year} RISDA';

  @override
  void initState() {
    super.initState();
    _loadAppInfo();
  }

  Future<void> _loadAppInfo() async {
    setState(() {
      _isLoading = true;
    });

    try {
      final response = await _apiService.getAppInfo();
      
      if (response['success'] == true && response['data'] != null) {
        final data = response['data'];
        
        if (mounted) {
          setState(() {
            _appName = data['app_name'] ?? _appName;
            _systemFullName = data['system_full_name'] ?? _systemFullName;
            _version = data['version'] ?? _version;
            _buildNumber = data['build_number'] ?? _buildNumber;
            _releaseDate = data['release_date'] ?? _releaseDate;
            _organization = data['organization'] ?? _organization;
            _department = data['department'] ?? _department;
            _address = data['address'];
            _phone = (data['phone'] as List<dynamic>?)?.cast<String>() ?? _phone;
            _fax = data['fax'] ?? _fax;
            _email = data['email'] ?? _email;
            _backendUrl = data['backend_url'] ?? _backendUrl;
            _websiteUrl = data['website_url'] ?? _websiteUrl;
            _supportedPlatforms = (data['supported_platforms'] as List<dynamic>?)?.cast<String>() ?? _supportedPlatforms;
            _purpose = data['purpose'] ?? '';
            _description = data['description'] ?? '';
            _keyFeatures = (data['key_features'] as List<dynamic>?)?.cast<String>() ?? [];
            _copyright = data['copyright'] ?? _copyright;
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
        title: Text('About', style: AppTextStyles.h2.copyWith(color: Colors.white)),
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
                children: [
                  // Main Info Card
                  Card(
                    color: PastelColors.cardBackground,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(8),
                      side: BorderSide(color: PastelColors.border),
                    ),
                    elevation: 0,
                    child: Padding(
                      padding: const EdgeInsets.all(24),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(_systemFullName, style: AppTextStyles.h2),
                          const SizedBox(height: 4),
                          Text(_appName, style: AppTextStyles.bodyLarge.copyWith(color: Colors.grey[600])),
                          const SizedBox(height: 16),
                          _buildInfoRow('Version', _version),
                          _buildInfoRow('Build Number', _buildNumber.toString()),
                          _buildInfoRow('Release Date', _releaseDate),
                          const SizedBox(height: 8),
                          Text(_copyright, style: AppTextStyles.bodyMedium.copyWith(fontWeight: FontWeight.w600)),
                        ],
                      ),
                    ),
                  ),
                  
                  const SizedBox(height: 16),
                  
                  // Organization Card
                  Card(
                    color: PastelColors.cardBackground,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(8),
                      side: BorderSide(color: PastelColors.border),
                    ),
                    elevation: 0,
                    child: Padding(
                      padding: const EdgeInsets.all(24),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text('Organization', style: AppTextStyles.h3),
                          const SizedBox(height: 12),
                          Text(_department, style: AppTextStyles.bodyLarge.copyWith(fontWeight: FontWeight.w600)),
                          if (_address != null) ...[
                            const SizedBox(height: 8),
                            Text(_address!['line1'] ?? '', style: AppTextStyles.bodyMedium),
                            Text(_address!['line2'] ?? '', style: AppTextStyles.bodyMedium),
                            Text('${_address!['postcode']} ${_address!['city']}, ${_address!['state']}', style: AppTextStyles.bodyMedium),
                            Text(_address!['country'] ?? '', style: AppTextStyles.bodyMedium),
                          ],
                          const SizedBox(height: 12),
                          _buildInfoRow('Tel', _phone.join(' / ')),
                          _buildInfoRow('Fax', _fax),
                          _buildInfoRow('Email', _email),
                          _buildInfoRow('Backend', _backendUrl),
                          _buildInfoRow('Website', _websiteUrl),
                        ],
                      ),
                    ),
                  ),
                  
                  const SizedBox(height: 16),
                  
                  // Purpose Card
                  if (_purpose.isNotEmpty)
                    Card(
                      color: PastelColors.cardBackground,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(8),
                        side: BorderSide(color: PastelColors.border),
                      ),
                      elevation: 0,
                      child: Padding(
                        padding: const EdgeInsets.all(24),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text('Purpose', style: AppTextStyles.h3),
                            const SizedBox(height: 12),
                            Text(_purpose, style: AppTextStyles.bodyMedium, textAlign: TextAlign.justify),
                          ],
                        ),
                      ),
                    ),
                  
                  const SizedBox(height: 16),
                  
                  // Description Card
                  if (_description.isNotEmpty)
                    Card(
                      color: PastelColors.cardBackground,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(8),
                        side: BorderSide(color: PastelColors.border),
                      ),
                      elevation: 0,
                      child: Padding(
                        padding: const EdgeInsets.all(24),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text('About the App', style: AppTextStyles.h3),
                            const SizedBox(height: 12),
                            Text(_description, style: AppTextStyles.bodyMedium, textAlign: TextAlign.justify),
                          ],
                        ),
                      ),
                    ),
                  
                  const SizedBox(height: 16),
                  
                  // Supported Platforms Card
                  Card(
                    color: PastelColors.cardBackground,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(8),
                      side: BorderSide(color: PastelColors.border),
                    ),
                    elevation: 0,
                    child: Padding(
                      padding: const EdgeInsets.all(24),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text('Supported Platforms', style: AppTextStyles.h3),
                          const SizedBox(height: 12),
                          ..._supportedPlatforms.map((platform) => 
                            Padding(
                              padding: const EdgeInsets.symmetric(vertical: 2),
                              child: Row(
                                children: [
                                  Icon(Icons.check_circle, size: 16, color: PastelColors.primary),
                                  const SizedBox(width: 8),
                                  Text(platform, style: AppTextStyles.bodyMedium),
                                ],
                              ),
                            ),
                          ).toList(),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ),
    );
  }

  Widget _buildInfoRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
            width: 100,
            child: Text('$label:', style: AppTextStyles.bodyMedium.copyWith(color: Colors.grey[600])),
          ),
          Expanded(
            child: Text(value, style: AppTextStyles.bodyMedium),
          ),
        ],
      ),
    );
  }
}
