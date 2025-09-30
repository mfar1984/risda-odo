import 'package:flutter/material.dart';
import '../theme/pastel_colors.dart';
import '../theme/text_styles.dart';
import '../models/user_model.dart';
import '../services/api_service.dart';
import 'dart:developer' as developer;

class EditProfileScreen extends StatefulWidget {
  final User user;
  
  const EditProfileScreen({Key? key, required this.user}) : super(key: key);
  
  @override
  _EditProfileScreenState createState() => _EditProfileScreenState();
}

class _EditProfileScreenState extends State<EditProfileScreen> {
  final _formKey = GlobalKey<FormState>();
  bool _isLoading = false;
  String _errorMessage = '';
  
  late TextEditingController _nameController;
  late TextEditingController _emailController;
  late TextEditingController _phoneController;
  late TextEditingController _addressController;
  
  @override
  void initState() {
    super.initState();
    _nameController = TextEditingController(text: widget.user.name);
    _emailController = TextEditingController(text: widget.user.email);
    
    // Extract phone and address from staff details if available
    Map<String, dynamic> staffDetails = {};
    if (widget.user.staff is Map) {
      staffDetails = Map<String, dynamic>.from(widget.user.staff);
    }
    
    _phoneController = TextEditingController(text: staffDetails['phone'] ?? '');
    _addressController = TextEditingController(text: staffDetails['address'] ?? '');
  }
  
  @override
  void dispose() {
    _nameController.dispose();
    _emailController.dispose();
    _phoneController.dispose();
    _addressController.dispose();
    super.dispose();
  }
  
  Future<void> _updateProfile() async {
    if (!_formKey.currentState!.validate()) {
      return;
    }
    
    setState(() {
      _isLoading = true;
      _errorMessage = '';
    });
    
    try {
      final apiService = ApiService();
      
      // Prepare data for API
      final data = {
        'name': _nameController.text.trim(),
        'phone': _phoneController.text.trim(),
        'address': _addressController.text.trim(),
      };
      
      // Email can only be changed by admin, so we don't include it in the update
      
      final response = await apiService.updateProfile(data);
      
      if (response['success'] == true) {
        // Return to profile screen with success flag
        Navigator.pop(context, true);
      } else {
        setState(() {
          _isLoading = false;
          _errorMessage = response['message'] ?? 'Failed to update profile';
        });
      }
    } catch (e) {
      developer.log('Error updating profile: $e');
      setState(() {
        _isLoading = false;
        _errorMessage = 'Error: $e';
      });
    }
  }
  
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: PastelColors.primary,
        title: Text('Edit Profile', style: AppTextStyles.h2.copyWith(color: Colors.white)),
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      backgroundColor: PastelColors.background,
      body: _isLoading
          ? Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: () async {
                // Reload the form with fresh data
                _nameController.text = widget.user.name;
                _emailController.text = widget.user.email;
                
                // Extract phone and address from staff details if available
                Map<String, dynamic> staffDetails = {};
                if (widget.user.staff is Map) {
                  staffDetails = Map<String, dynamic>.from(widget.user.staff);
                }
                
                _phoneController.text = staffDetails['phone'] ?? '';
                _addressController.text = staffDetails['address'] ?? '';
                
                setState(() {
                  _errorMessage = '';
                });
              },
              child: SingleChildScrollView(
                physics: AlwaysScrollableScrollPhysics(),
                padding: const EdgeInsets.all(16),
                child: Card(
                  color: PastelColors.cardBackground,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(8),
                    side: BorderSide(color: PastelColors.border),
                  ),
                  elevation: 0,
                  child: Padding(
                    padding: const EdgeInsets.all(24),
                    child: Form(
                      key: _formKey,
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          if (_errorMessage.isNotEmpty)
                            Container(
                              width: double.infinity,
                              padding: EdgeInsets.all(12),
                              margin: EdgeInsets.only(bottom: 16),
                              decoration: BoxDecoration(
                                color: PastelColors.error.withOpacity(0.1),
                                borderRadius: BorderRadius.circular(4),
                                border: Border.all(color: PastelColors.error),
                              ),
                              child: Text(
                                _errorMessage,
                                style: AppTextStyles.bodyMedium.copyWith(color: PastelColors.errorText),
                              ),
                            ),
                          
                          Text('Personal Information', style: AppTextStyles.bodyLarge.copyWith(color: PastelColors.primary)),
                          const SizedBox(height: 16),
                          
                          // Name field
                          TextFormField(
                            controller: _nameController,
                            decoration: InputDecoration(
                              labelText: 'Name',
                              border: OutlineInputBorder(),
                              prefixIcon: Icon(Icons.person),
                            ),
                            validator: (value) {
                              if (value == null || value.trim().isEmpty) {
                                return 'Name is required';
                              }
                              return null;
                            },
                          ),
                          const SizedBox(height: 16),
                          
                          // Email field (disabled)
                          TextFormField(
                            controller: _emailController,
                            decoration: InputDecoration(
                              labelText: 'Email',
                              border: OutlineInputBorder(),
                              prefixIcon: Icon(Icons.email),
                              helperText: 'Email cannot be changed',
                            ),
                            enabled: false,
                          ),
                          const SizedBox(height: 16),
                          
                          // Phone field
                          TextFormField(
                            controller: _phoneController,
                            decoration: InputDecoration(
                              labelText: 'Phone',
                              border: OutlineInputBorder(),
                              prefixIcon: Icon(Icons.phone),
                            ),
                            keyboardType: TextInputType.phone,
                          ),
                          const SizedBox(height: 16),
                          
                          // Address field
                          TextFormField(
                            controller: _addressController,
                            decoration: InputDecoration(
                              labelText: 'Address',
                              border: OutlineInputBorder(),
                              prefixIcon: Icon(Icons.home),
                            ),
                            maxLines: 3,
                          ),
                          const SizedBox(height: 24),
                          
                          // RISDA Info (read-only)
                          Text('RISDA Information (Cannot be changed)', style: AppTextStyles.bodyLarge.copyWith(color: PastelColors.primary)),
                          const SizedBox(height: 16),
                          
                          _infoField('Role', widget.user.role ?? 'N/A'),
                          const SizedBox(height: 8),
                          _infoField('Division', widget.user.bahagianName),
                          const SizedBox(height: 8),
                          _infoField('Station', widget.user.stesenName),
                          const SizedBox(height: 24),
                          
                          // Submit button
                          SizedBox(
                            width: double.infinity,
                            height: 50,
                            child: ElevatedButton(
                              onPressed: _updateProfile,
                              style: ElevatedButton.styleFrom(
                                backgroundColor: PastelColors.primary,
                                foregroundColor: Colors.white,
                                shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(8),
                                ),
                              ),
                              child: Text('Save Changes', style: TextStyle(fontSize: 16)),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
              ),
            ),
    );
  }
  
  Widget _infoField(String label, String value) {
    return Container(
      padding: EdgeInsets.symmetric(horizontal: 12, vertical: 16),
      decoration: BoxDecoration(
        color: PastelColors.background.withOpacity(0.5),
        borderRadius: BorderRadius.circular(4),
        border: Border.all(color: PastelColors.border),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
            width: 100,
            child: Text(label, style: AppTextStyles.bodyMedium.copyWith(color: PastelColors.textSecondary)),
          ),
          Expanded(
            child: Text(value, style: AppTextStyles.bodyLarge),
          ),
        ],
      ),
    );
  }
} 