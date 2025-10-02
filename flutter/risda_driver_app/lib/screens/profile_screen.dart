import 'package:flutter/material.dart';
import '../theme/pastel_colors.dart';
import '../theme/text_styles.dart';
import '../models/user_model.dart';
import '../services/api_service.dart';
import '../services/hive_service.dart';
import '../models/auth_model.dart';
import '../services/connectivity_service.dart';
import 'dart:developer' as developer;
import 'edit_profile_screen.dart';

class ProfileScreen extends StatefulWidget {
  @override
  _ProfileScreenState createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  User? user;
  bool isLoading = true;
  bool isOnline = false;
  String errorMessage = '';

  @override
  void initState() {
    super.initState();
    _initialize();
  }
  
  Future<void> _initialize() async {
    // Load data from cache first for immediate display
    await _loadUserDataFromCache();
    
    // Then check connectivity and fetch from API if online
    await _checkConnectivity();
    if (isOnline) {
      await _fetchUserDataFromApi(showLoadingIndicator: false);
    }
  }
  
  Future<void> _checkConnectivity() async {
    final connectivityService = ConnectivityService();
    final isConnected = await connectivityService.checkConnectivity();
    setState(() {
      isOnline = isConnected;
    });
  }

  Future<void> _loadUserDataFromCache() async {
    // Get user data from Hive
    final userData = HiveService.getUser();
    setState(() {
      user = userData;
      isLoading = false;
    });
  }

  Future<void> _fetchUserDataFromApi({bool showLoadingIndicator = true}) async {
    if (showLoadingIndicator) {
      setState(() {
        isLoading = true;
        errorMessage = '';
      });
    }

    try {
      final apiService = ApiService();
      final response = await apiService.getProfile();

      if (response['success'] == true && response['data'] != null) {
        // Create user from API response
        final userData = response['data'];
        final user = User(
          id: userData['id'],
          name: userData['name'],
          email: userData['email'],
          role: userData['role'],
          bahagian: userData['bahagian'],
          stesen: userData['stesen'],
          staff: userData['staff'],
          createdAt: userData['created_at'],
          lastLogin: userData['last_login'],
        );

        // Save to Hive
        await HiveService.saveUser(user);

        // Update state
        setState(() {
          this.user = user;
          isLoading = false;
        });

        if (showLoadingIndicator) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(content: Text('Profil berjaya dikemas kini')),
          );
        }
      } else {
        setState(() {
          isLoading = false;
          if (showLoadingIndicator) {
            errorMessage = 'Gagal mendapatkan data profil';
          }
        });
      }
    } catch (e) {
      developer.log('Error fetching user data: $e');
      setState(() {
        isLoading = false;
        if (showLoadingIndicator) {
          errorMessage = 'Ralat: $e';
        }
      });
    }
  }

  Future<void> _refreshUserData() async {
    await _checkConnectivity();

    if (!isOnline) {
      setState(() {
        errorMessage = 'Cannot connect to internet. Showing offline data.';
      });
      return;
    }
    
    await _fetchUserDataFromApi(showLoadingIndicator: true);
  }
  
  void _navigateToEditProfile() async {
    if (!isOnline) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('You need internet connection to edit profile'),
          backgroundColor: PastelColors.warning,
        ),
      );
      return;
    }
    
    final result = await Navigator.push(
      context, 
      MaterialPageRoute(
        builder: (context) => EditProfileScreen(user: user!),
      ),
    );
    
    if (result == true) {
      // Profile was updated, refresh data
      _refreshUserData();
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: PastelColors.primary,
        title: Text('Profile', style: AppTextStyles.h2.copyWith(color: Colors.white)),
        iconTheme: const IconThemeData(color: Colors.white),
        actions: [
          // Connection status indicator
          Padding(
            padding: const EdgeInsets.only(right: 8.0),
            child: Icon(
              isOnline ? Icons.wifi : Icons.wifi_off,
              color: isOnline ? Colors.white : Colors.white70,
              size: 20,
            ),
          ),
          IconButton(
            icon: Icon(Icons.refresh, color: Colors.white),
            onPressed: isLoading ? null : _refreshUserData,
            tooltip: 'Update',
          ),
        ],
      ),
      backgroundColor: PastelColors.background,
      body: isLoading
          ? Center(child: CircularProgressIndicator())
          : errorMessage.isNotEmpty && user == null
              ? Center(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 24.0),
                        child: Text(
                          errorMessage, 
                          style: AppTextStyles.bodyLarge.copyWith(color: Colors.red),
                          textAlign: TextAlign.center,
                        ),
                      ),
                      SizedBox(height: 16),
                      ElevatedButton(
                        onPressed: _refreshUserData,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: PastelColors.primary,
                          foregroundColor: Colors.white,
                        ),
                        child: Text('Try Again'),
                      ),
                    ],
                  ),
                )
              : user == null
                  ? Center(child: Text('No profile data', style: AppTextStyles.bodyLarge))
                  : RefreshIndicator(
                      onRefresh: _refreshUserData,
                      child: Stack(
                        children: [
                          SingleChildScrollView(
                            physics: AlwaysScrollableScrollPhysics(),
                            child: _buildProfileContent(),
                          ),
                          if (errorMessage.isNotEmpty)
                            Positioned(
                              top: 0,
                              left: 0,
                              right: 0,
                              child: Container(
                                color: PastelColors.warning.withOpacity(0.1),
                                padding: EdgeInsets.symmetric(vertical: 8, horizontal: 16),
                                child: Text(
                                  errorMessage,
                                  style: AppTextStyles.bodySmall.copyWith(color: PastelColors.warningText),
                                  textAlign: TextAlign.center,
                                ),
                              ),
                            ),
                        ],
                      ),
                    ),
      floatingActionButton: user != null && isOnline ? FloatingActionButton(
        onPressed: _navigateToEditProfile,
        backgroundColor: PastelColors.primary,
        child: Icon(Icons.edit, color: Colors.white),
        tooltip: 'Edit Profil',
      ) : null,
    );
  }

  Widget _buildProfileContent() {
    // Extract staff details
    Map<String, dynamic> staffDetails = {};
    if (user!.staff is Map) {
      staffDetails = Map<String, dynamic>.from(user!.staff);
    }

    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 24),
      child: Align(
        alignment: Alignment.topCenter,
        child: Card(
          color: PastelColors.cardBackground,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8), side: BorderSide(color: PastelColors.border)),
          elevation: 0,
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 32, vertical: 28),
            child: SingleChildScrollView(
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Center(
                    child: Column(
                      children: [
                        CircleAvatar(
                          radius: 36,
                          backgroundColor: PastelColors.primary,
                          child: Text(
                            user!.name.isNotEmpty ? user!.name[0].toUpperCase() : '?',
                            style: TextStyle(fontSize: 30, color: Colors.white),
                          ),
                        ),
                        const SizedBox(height: 16),
                        Text(user!.name, style: AppTextStyles.h2),
                        const SizedBox(height: 4),
                        Text(user!.email, style: AppTextStyles.bodyMedium),
                        const SizedBox(height: 8),
                        // Connection status
                        Container(
                          padding: EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                          decoration: BoxDecoration(
                            color: isOnline ? PastelColors.success.withOpacity(0.1) : PastelColors.warning.withOpacity(0.1),
                            borderRadius: BorderRadius.circular(12),
                            border: Border.all(
                              color: isOnline ? PastelColors.success : PastelColors.warning,
                              width: 1,
                            ),
                          ),
                          child: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              Icon(
                                isOnline ? Icons.wifi : Icons.wifi_off,
                                size: 14,
                                color: isOnline ? PastelColors.success : PastelColors.warning,
                              ),
                              SizedBox(width: 4),
                              Text(
                                isOnline ? 'Online' : 'Offline',
                                style: AppTextStyles.bodySmall.copyWith(
                                  color: isOnline ? PastelColors.success : PastelColors.warning,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 24),
                  
                  // Staff Info (if available)
                  if (staffDetails.isNotEmpty) ...[
                    Text('Personal Information', style: AppTextStyles.bodyLarge.copyWith(color: PastelColors.primary)),
                    const SizedBox(height: 8),
                    _profileField('Staff ID', staffDetails['no_staf'] ?? 'N/A'),
                    _profileField('Position', staffDetails['jawatan'] ?? 'N/A'),
                    _profileField('Status', staffDetails['status'] ?? 'N/A'),
                    const SizedBox(height: 18),
                  ],
                  
                  // RISDA Info
                  Text('RISDA Information', style: AppTextStyles.bodyLarge.copyWith(color: PastelColors.primary)),
                  const SizedBox(height: 8),
                  _profileField('Role', user!.role ?? 'N/A'),
                  _profileField('RISDA Division', user!.bahagianName),
                  _profileField('RISDA Station', user!.stesenName),
                  const SizedBox(height: 18),
                  
                  // Account Info
                  Text('Account Information', style: AppTextStyles.bodyLarge.copyWith(color: PastelColors.primary)),
                  const SizedBox(height: 8),
                  _profileField('Created On', user!.createdAt ?? 'N/A'),
                  _profileField('Last Login', user!.lastLogin ?? 'N/A'),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}

Widget _profileField(String label, String value) {
  return Padding(
    padding: const EdgeInsets.symmetric(vertical: 4),
    child: Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        SizedBox(
          width: 130,
          child: Text(label, style: AppTextStyles.bodyMedium.copyWith(color: PastelColors.textSecondary)),
        ),
        Expanded(
          child: Text(value, style: AppTextStyles.bodyLarge),
        ),
      ],
    ),
  );
} 