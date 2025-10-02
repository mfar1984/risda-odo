import 'dart:io';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:provider/provider.dart';
import '../services/auth_service.dart';
import '../services/api_service.dart';
import '../core/api_client.dart';
import '../theme/pastel_colors.dart';

class EditProfileScreen extends StatefulWidget {
  const EditProfileScreen({super.key});
  
  @override
  State<EditProfileScreen> createState() => _EditProfileScreenState();
}

class _EditProfileScreenState extends State<EditProfileScreen> {
  File? _profileImage;
  bool _isLoading = false;
  final ImagePicker _picker = ImagePicker();
  
  // Password controllers
  final _currentPasswordController = TextEditingController();
  final _newPasswordController = TextEditingController();
  final _confirmPasswordController = TextEditingController();
  
  bool _obscureCurrentPassword = true;
  bool _obscureNewPassword = true;
  bool _obscureConfirmPassword = true;
  
  @override
  void dispose() {
    _currentPasswordController.dispose();
    _newPasswordController.dispose();
    _confirmPasswordController.dispose();
    super.dispose();
  }
  
  Future<void> _pickImage(ImageSource source) async {
    try {
      final XFile? pickedFile = await _picker.pickImage(
        source: source,
        maxWidth: 1024,
        maxHeight: 1024,
        imageQuality: 85,
      );

      if (pickedFile != null) {
        setState(() {
          _profileImage = File(pickedFile.path);
        });
      }
    } catch (e) {
      _showErrorSnackBar('Gagal memilih gambar: $e');
    }
  }

  void _showImageSourceDialog(String? currentProfileUrl) {
    showModalBottomSheet(
      context: context,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (BuildContext context) {
        return SafeArea(
          child: Padding(
            padding: const EdgeInsets.symmetric(vertical: 16),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                ListTile(
                  leading: Container(
                    padding: const EdgeInsets.all(8),
                    decoration: BoxDecoration(
                      color: PastelColors.primary.withOpacity(0.1),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: const Icon(Icons.photo_camera, color: PastelColors.primary),
                  ),
                  title: const Text('Kamera'),
                  onTap: () {
                    Navigator.pop(context);
                    _pickImage(ImageSource.camera);
                  },
                ),
                ListTile(
                  leading: Container(
                    padding: const EdgeInsets.all(8),
                    decoration: BoxDecoration(
                      color: PastelColors.primary.withOpacity(0.1),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: const Icon(Icons.photo_library, color: PastelColors.primary),
                  ),
                  title: const Text('Galeri'),
                  onTap: () {
                    Navigator.pop(context);
                    _pickImage(ImageSource.gallery);
                  },
                ),
                if (currentProfileUrl != null || _profileImage != null)
                  ListTile(
                    leading: Container(
                      padding: const EdgeInsets.all(8),
                      decoration: BoxDecoration(
                        color: Colors.red.withOpacity(0.1),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: const Icon(Icons.delete, color: Colors.red),
                    ),
                    title: const Text('Padam Gambar Profil'),
                    onTap: () {
                      Navigator.pop(context);
                      _deleteProfilePicture();
                    },
                  ),
              ],
            ),
          ),
        );
      },
    );
  }

  Future<void> _uploadProfilePicture() async {
    if (_profileImage == null) {
      _showErrorSnackBar('Sila pilih gambar terlebih dahulu');
      return;
    }
    
    setState(() => _isLoading = true);

    try {
      final apiService = ApiService(ApiClient());
      final response = await apiService.uploadProfilePicture(_profileImage!);
      
      if (response['success'] == true) {
        // Refresh user data
        final authService = Provider.of<AuthService>(context, listen: false);
        await authService.refreshUserData();

        if (mounted) {
          _showSuccessSnackBar('Gambar profil berjaya dikemaskini');
          setState(() {
            _profileImage = null;
          });
        }
      }
    } catch (e) {
      _showErrorSnackBar('Gagal mengemaskini gambar profil: $e');
    } finally {
      setState(() => _isLoading = false);
    }
  }

  Future<void> _deleteProfilePicture() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        title: const Text('Padam Gambar Profil'),
        content: const Text('Adakah anda pasti mahu memadam gambar profil?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Batal'),
          ),
          TextButton(
            onPressed: () => Navigator.pop(context, true),
            child: const Text('Padam', style: TextStyle(color: Colors.red)),
          ),
        ],
      ),
    );

    if (confirm != true) return;

    setState(() => _isLoading = true);

    try {
      final apiService = ApiService(ApiClient());
      await apiService.deleteProfilePicture();

      // Refresh user data
      final authService = Provider.of<AuthService>(context, listen: false);
      await authService.refreshUserData();

      if (mounted) {
        _showSuccessSnackBar('Gambar profil berjaya dipadam');
        setState(() {
          _profileImage = null;
        });
      }
    } catch (e) {
      _showErrorSnackBar('Gagal memadam gambar profil: $e');
    } finally {
      setState(() => _isLoading = false);
    }
  }

  Future<void> _changePassword() async {
    // Validation
    if (_currentPasswordController.text.isEmpty) {
      _showErrorSnackBar('Sila masukkan kata laluan semasa');
      return;
    }
    if (_newPasswordController.text.isEmpty) {
      _showErrorSnackBar('Sila masukkan kata laluan baru');
      return;
    }
    if (_newPasswordController.text.length < 8) {
      _showErrorSnackBar('Kata laluan baru mesti sekurang-kurangnya 8 aksara');
      return;
    }
    if (_newPasswordController.text != _confirmPasswordController.text) {
      _showErrorSnackBar('Pengesahan kata laluan tidak sepadan');
      return;
    }

    setState(() => _isLoading = true);

    try {
      final apiService = ApiService(ApiClient());
      final response = await apiService.changePassword(
        currentPassword: _currentPasswordController.text,
        newPassword: _newPasswordController.text,
        newPasswordConfirmation: _confirmPasswordController.text,
      );

      if (response['success'] == true) {
        if (mounted) {
          _showSuccessSnackBar('Kata laluan berjaya dikemaskini');
          _currentPasswordController.clear();
          _newPasswordController.clear();
          _confirmPasswordController.clear();
        }
      }
    } catch (e) {
      _showErrorSnackBar('Gagal menukar kata laluan: $e');
    } finally {
      setState(() => _isLoading = false);
    }
  }

  void _showSuccessSnackBar(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: PastelColors.success,
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
        duration: const Duration(seconds: 3),
      ),
    );
  }

  void _showErrorSnackBar(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: PastelColors.error,
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
        duration: const Duration(seconds: 3),
      ),
    );
  }

  Widget _buildProfilePictureSection(String? currentProfileUrl, String? userName) {
    return GestureDetector(
      onTap: () => _showImageSourceDialog(currentProfileUrl),
      child: Stack(
        children: [
          CircleAvatar(
            radius: 50,
            backgroundColor: Colors.white,
            backgroundImage: _profileImage != null
                ? FileImage(_profileImage!)
                : (currentProfileUrl != null
                    ? NetworkImage(currentProfileUrl)
                    : null) as ImageProvider?,
            child: (_profileImage == null && currentProfileUrl == null)
                ? Text(
                    (userName ?? '').isNotEmpty ? userName![0].toUpperCase() : 'U',
                    style: const TextStyle(
                      fontSize: 40,
                      color: PastelColors.primary,
                      fontWeight: FontWeight.bold,
                    ),
                  )
                : null,
          ),
          Positioned(
            bottom: 0,
            right: 0,
            child: Container(
              padding: const EdgeInsets.all(8),
              decoration: BoxDecoration(
                color: PastelColors.primary,
                shape: BoxShape.circle,
                border: Border.all(color: Colors.white, width: 2),
              ),
              child: const Icon(Icons.camera_alt, size: 20, color: Colors.white),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildInfoCard({
    required IconData icon,
    required String title,
    required String value,
  }) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.grey[50],
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: Colors.grey[200]!),
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              color: PastelColors.primary.withOpacity(0.1),
              borderRadius: BorderRadius.circular(8),
            ),
            child: Icon(icon, color: PastelColors.primary, size: 24),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  title,
                  style: TextStyle(
                    fontSize: 12,
                    color: Colors.grey[600],
                    fontWeight: FontWeight.w500,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  value,
                  style: const TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.w600,
                    color: Colors.black87,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildPasswordField({
    required TextEditingController controller,
    required String label,
    required bool obscureText,
    required VoidCallback onToggleVisibility,
  }) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.grey[50],
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: Colors.grey[200]!),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            label,
            style: TextStyle(
              fontSize: 12,
              color: Colors.grey[600],
              fontWeight: FontWeight.w500,
            ),
          ),
          const SizedBox(height: 8),
          TextField(
            controller: controller,
            obscureText: obscureText,
            decoration: InputDecoration(
              hintText: 'Masukkan $label',
              border: OutlineInputBorder(
                borderRadius: BorderRadius.circular(8),
                borderSide: BorderSide(color: Colors.grey[300]!),
              ),
              enabledBorder: OutlineInputBorder(
                borderRadius: BorderRadius.circular(8),
                borderSide: BorderSide(color: Colors.grey[300]!),
              ),
              focusedBorder: OutlineInputBorder(
                borderRadius: BorderRadius.circular(8),
                borderSide: const BorderSide(color: PastelColors.primary, width: 2),
              ),
              filled: true,
              fillColor: Colors.white,
              contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 14),
              suffixIcon: IconButton(
                icon: Icon(
                  obscureText ? Icons.visibility : Icons.visibility_off,
                  color: Colors.grey,
                ),
                onPressed: onToggleVisibility,
              ),
            ),
          ),
        ],
      ),
    );
  }

  String _getRisdaStationTitle(String? jenisOrganisasi) {
    if (jenisOrganisasi == 'stesen') {
      return 'RISDA Stesen';
    } else if (jenisOrganisasi == 'bahagian') {
      return 'RISDA Bahagian';
    }
    return 'RISDA';
  }

  String _getRisdaStationName(Map<String, dynamic> user) {
    if (user['user']?['stesen'] != null) {
      return user['user']!['stesen']['nama'] ?? 'Tidak ditetapkan';
    } else if (user['user']?['bahagian'] != null) {
      return user['user']!['bahagian']['nama'] ?? 'Tidak ditetapkan';
    }
    return 'Tidak ditetapkan';
  }
  
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text('Edit Profil'),
        backgroundColor: PastelColors.primary,
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: PastelColors.primary))
          : Consumer<AuthService>(
              builder: (context, authService, child) {
                final user = authService.currentUser;
                final currentProfileUrl = user?['user']?['profile_picture_url'];
                final userName = user?['user']?['name'];
                final userEmail = user?['user']?['email'];
                final staf = user?['user']?['staf'];
                
                return SingleChildScrollView(
                  child: Column(
                    children: [
                      // Header with gradient and profile picture
                      Container(
                        width: double.infinity,
                        padding: const EdgeInsets.all(24),
                        decoration: const BoxDecoration(
                          gradient: PastelColors.primaryGradient,
                        ),
                        child: Column(
                          children: [
                            _buildProfilePictureSection(currentProfileUrl, userName),
                            const SizedBox(height: 16),
                            Text(
                              'Ketuk untuk menukar gambar',
                              style: TextStyle(
                                color: Colors.white.withOpacity(0.9),
                                fontSize: 14,
                              ),
                            ),
                            if (_profileImage != null) ...[
                              const SizedBox(height: 16),
                              ElevatedButton.icon(
                                onPressed: _isLoading ? null : _uploadProfilePicture,
                                icon: const Icon(Icons.upload, size: 20),
                                label: const Text('Muat Naik'),
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: Colors.white,
                                  foregroundColor: PastelColors.primary,
                                  padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(20),
                                  ),
                                ),
                              ),
                            ],
                          ],
                        ),
                      ),
                      
                      // Profile Details
                      Padding(
                padding: const EdgeInsets.all(16),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                            // Read-only info section
                            const Text(
                              'Maklumat Peribadi',
                              style: TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.bold,
                                color: Colors.black87,
                              ),
                            ),
                            const SizedBox(height: 4),
                            Text(
                              'Maklumat ini hanya boleh dikemaskini oleh pentadbir',
                              style: TextStyle(
                                fontSize: 12,
                                color: Colors.grey[600],
                              ),
                            ),
                          const SizedBox(height: 16),
                          
                            _buildInfoCard(
                              icon: Icons.badge_outlined,
                              title: 'No. Pekerja',
                              value: staf?['no_pekerja'] ?? 'Tidak ditetapkan',
                            ),
                            const SizedBox(height: 12),
                            _buildInfoCard(
                              icon: Icons.person,
                              title: 'Nama Penuh',
                              value: staf?['nama_penuh'] ?? userName ?? 'Tidak ditetapkan',
                            ),
                            const SizedBox(height: 12),
                            _buildInfoCard(
                              icon: Icons.credit_card,
                              title: 'No. Kad Pengenalan',
                              value: staf?['no_kad_pengenalan'] ?? 'Tidak ditetapkan',
                            ),
                            const SizedBox(height: 12),
                            _buildInfoCard(
                              icon: Icons.business,
                              title: _getRisdaStationTitle(user?['user']?['jenis_organisasi']),
                              value: _getRisdaStationName(user ?? {}),
                            ),
                            const SizedBox(height: 12),
                            _buildInfoCard(
                              icon: Icons.work,
                              title: 'Jawatan',
                              value: staf?['jawatan'] ?? 'Tidak ditetapkan',
                            ),
                            const SizedBox(height: 12),
                            _buildInfoCard(
                              icon: Icons.phone,
                              title: 'No. Telefon',
                              value: staf?['no_telefon'] ?? user?['user']?['no_telefon'] ?? 'Tidak ditetapkan',
                            ),
                            const SizedBox(height: 12),
                            _buildInfoCard(
                              icon: Icons.email,
                              title: 'E-mel',
                              value: userEmail ?? 'Tidak ditetapkan',
                            ),
                            
                            const SizedBox(height: 32),
                            
                            // Change password section
                            const Text(
                              'Tukar Kata Laluan',
                              style: TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.bold,
                                color: Colors.black87,
                              ),
                          ),
                          const SizedBox(height: 16),
                          
                            _buildPasswordField(
                              controller: _currentPasswordController,
                              label: 'Kata Laluan Semasa',
                              obscureText: _obscureCurrentPassword,
                              onToggleVisibility: () => setState(() => _obscureCurrentPassword = !_obscureCurrentPassword),
                            ),
                            const SizedBox(height: 12),
                            _buildPasswordField(
                              controller: _newPasswordController,
                              label: 'Kata Laluan Baru',
                              obscureText: _obscureNewPassword,
                              onToggleVisibility: () => setState(() => _obscureNewPassword = !_obscureNewPassword),
                            ),
                            const SizedBox(height: 12),
                            _buildPasswordField(
                              controller: _confirmPasswordController,
                              label: 'Sahkan Kata Laluan Baru',
                              obscureText: _obscureConfirmPassword,
                              onToggleVisibility: () => setState(() => _obscureConfirmPassword = !_obscureConfirmPassword),
                          ),
                          const SizedBox(height: 24),
                          
                          SizedBox(
                            width: double.infinity,
                              child: ElevatedButton.icon(
                                onPressed: _isLoading ? null : _changePassword,
                                icon: const Icon(Icons.lock_reset),
                                label: const Text('Tukar Kata Laluan'),
                              style: ElevatedButton.styleFrom(
                                backgroundColor: PastelColors.primary,
                                foregroundColor: Colors.white,
                                  padding: const EdgeInsets.symmetric(vertical: 16),
                                shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(12),
                                  ),
                                ),
                            ),
                          ),
                        ],
                      ),
                    ),
                    ],
                  ),
                );
              },
            ),
    );
  }
}
