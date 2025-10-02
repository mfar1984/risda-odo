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
                        backgroundImage: user['user']?['profile_picture_url'] != null
                            ? NetworkImage(user['user']!['profile_picture_url']!)
                            : null,
                        child: user['user']?['profile_picture_url'] == null
                            ? Text(
                                (user['user']?['name'] ?? '').isNotEmpty ? (user['user']?['name'] ?? 'U')[0].toUpperCase() : 'U',
                                style: TextStyle(
                                  fontSize: 40,
                                  color: PastelColors.primary,
                                  fontWeight: FontWeight.bold,
                                ),
                              )
                            : null,
                      ),
                      const SizedBox(height: 16),
                      Text(
                        user['user']?['name'] ?? 'User Name',
                        style: AppTextStyles.h1.copyWith(color: Colors.white),
                      ),
                      const SizedBox(height: 8),
                      Text(
                        user['user']?['email'] ?? 'user@email.com',
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
                        icon: Icons.badge_outlined,
                        title: 'No. Pekerja',
                        value: user['user']?['staf']?['no_pekerja'] ?? 'Tidak ditetapkan',
                      ),
                      const SizedBox(height: 12),
                      _buildInfoCard(
                        icon: Icons.person,
                        title: 'Nama Penuh',
                        value: user['user']?['staf']?['nama_penuh'] ?? user['user']?['name'] ?? 'N/A',
                      ),
                      const SizedBox(height: 12),
                      _buildInfoCard(
                        icon: Icons.credit_card,
                        title: 'No. Kad Pengenalan',
                        value: user['user']?['staf']?['no_kad_pengenalan'] ?? 'Tidak ditetapkan',
                      ),
                      const SizedBox(height: 12),
                      _buildInfoCard(
                        icon: Icons.business,
                        title: _getRisdaStationTitle(user['user']?['jenis_organisasi']),
                        value: _getRisdaStationName(user),
                      ),
                      const SizedBox(height: 12),
                      _buildInfoCard(
                        icon: Icons.work,
                        title: 'Jawatan',
                        value: user['user']?['staf']?['jawatan'] ?? user['user']?['kumpulan']?['nama'] ?? 'Tidak ditetapkan',
                      ),
                      const SizedBox(height: 12),
                      _buildInfoCard(
                        icon: Icons.phone,
                        title: 'No. Telefon',
                        value: user['user']?['staf']?['no_telefon'] ?? user['user']?['no_telefon'] ?? 'Tidak ditetapkan',
                      ),
                      const SizedBox(height: 24),
                      
                      // Action Buttons Row
                      Row(
                        children: [
                          // Edit Profile Button
                          Expanded(
                            child: ElevatedButton.icon(
                              onPressed: () {
                                Navigator.push(
                                  context,
                                  MaterialPageRoute(
                                    builder: (_) => const EditProfileScreen(),
                                  ),
                                );
                              },
                              icon: const Icon(Icons.edit, size: 20),
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
                          const SizedBox(width: 12),
                          // Delete Account Button
                          Expanded(
                            child: ElevatedButton.icon(
                              onPressed: () {
                                // Show confirmation dialog
                                showDialog(
                                  context: context,
                                  builder: (context) => AlertDialog(
                                    title: const Text('Delete Account'),
                                    content: const Text(
                                      'Are you sure you want to delete your account? This action cannot be undone.',
                                    ),
                                    actions: [
                                      TextButton(
                                        onPressed: () => Navigator.pop(context),
                                        child: const Text('Cancel'),
                                      ),
                                      TextButton(
                                        onPressed: () {
                                          Navigator.pop(context);
                                          // TODO: Implement delete account logic
                                          ScaffoldMessenger.of(context).showSnackBar(
                                            const SnackBar(
                                              content: Text('Delete account feature coming soon'),
                                            ),
                                          );
                                        },
                                        child: const Text(
                                          'Delete',
                                          style: TextStyle(color: Colors.red),
                                        ),
                                      ),
                                    ],
                                  ),
                                );
                              },
                              icon: const Icon(Icons.delete_forever, size: 20),
                              label: const Text('Delete Account'),
                              style: ElevatedButton.styleFrom(
                                backgroundColor: PastelColors.error,
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

  String _getRisdaStationTitle(String? type) {
    switch (type?.toLowerCase()) {
      case 'bahagian':
        return 'RISDA Bahagian';
      case 'stesen':
        return 'RISDA Stesen';
      default:
        return 'RISDA';
    }
  }

  String _getRisdaStationName(Map<String, dynamic> user) {
    final userData = user['user'];
    if (userData == null) return 'Tidak ditetapkan';
    
    // Check if stesen data exists
    if (userData['stesen'] != null && userData['stesen']['nama'] != null) {
      return userData['stesen']['nama'];
    }
    
    // Check if bahagian data exists
    if (userData['bahagian'] != null && userData['bahagian']['nama'] != null) {
      return userData['bahagian']['nama'];
    }
    
    return 'Tidak ditetapkan';
  }
}