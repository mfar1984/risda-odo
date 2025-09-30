import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../theme/pastel_colors.dart';
import '../theme/text_styles.dart';
import '../services/auth_service.dart';
import 'edit_profile_screen.dart';

class ProfileScreen extends StatelessWidget {
  const ProfileScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text('Profile'),
        backgroundColor: PastelColors.primary,
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: Consumer<AuthService>(
        builder: (context, authService, child) {
          final user = authService.currentUser;
          
          if (user == null) {
            return const Center(
              child: Text('Tiada maklumat pengguna'),
            );
          }

          return SingleChildScrollView(
            child: Column(
              children: [
                // Header with gradient
                Container(
                  width: double.infinity,
                  padding: const EdgeInsets.all(24),
                  decoration: const BoxDecoration(
                    gradient: PastelColors.primaryGradient,
                  ),
                  child: Column(
                    children: [
                      CircleAvatar(
                        radius: 50,
                        backgroundColor: Colors.white,
                        child: Text(
                          user.name.isNotEmpty ? user.name[0].toUpperCase() : 'U',
                          style: TextStyle(
                            fontSize: 40,
                            color: PastelColors.primary,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                      const SizedBox(height: 16),
                      Text(
                        user.name,
                        style: AppTextStyles.h1.copyWith(color: Colors.white),
                      ),
                      const SizedBox(height: 8),
                      Text(
                        user.email,
                        style: AppTextStyles.bodyLarge.copyWith(color: Colors.white70),
                      ),
                    ],
                  ),
                ),
                
                // Profile Details
                Padding(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    children: [
                      _buildInfoCard(
                        icon: Icons.badge,
                        title: 'ID Pengguna',
                        value: user.id.toString(),
                      ),
                      const SizedBox(height: 12),
                      _buildInfoCard(
                        icon: Icons.person,
                        title: 'Nama',
                        value: user.name,
                      ),
                      const SizedBox(height: 12),
                      _buildInfoCard(
                        icon: Icons.email,
                        title: 'Email',
                        value: user.email,
                      ),
                      const SizedBox(height: 12),
                      _buildInfoCard(
                        icon: Icons.business,
                        title: 'Jenis Organisasi',
                        value: _getOrganisationType(user.jenisOrganisasi),
                      ),
                      const SizedBox(height: 12),
                      _buildInfoCard(
                        icon: Icons.location_city,
                        title: 'ID Organisasi',
                        value: user.organisasiId?.toString() ?? 'Tidak ditetapkan',
                      ),
                      const SizedBox(height: 24),
                      
                      // Edit Profile Button
                      SizedBox(
                        width: double.infinity,
                        child: ElevatedButton.icon(
                          onPressed: () {
                            Navigator.push(
                              context,
                              MaterialPageRoute(
                                builder: (_) => const EditProfileScreen(),
                              ),
                            );
                          },
                          icon: const Icon(Icons.edit),
                          label: const Text('Edit Profile'),
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
            child: Icon(icon, color: PastelColors.primary),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  title,
                  style: AppTextStyles.bodySmall.copyWith(
                    color: Colors.grey[600],
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  value,
                  style: AppTextStyles.bodyLarge.copyWith(
                    fontWeight: FontWeight.w500,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  String _getOrganisationType(String? type) {
    switch (type?.toLowerCase()) {
      case 'semua':
        return 'Semua';
      case 'bahagian':
        return 'Bahagian';
      case 'stesen':
        return 'Stesen';
      default:
        return type ?? 'Tidak ditetapkan';
    }
  }
}