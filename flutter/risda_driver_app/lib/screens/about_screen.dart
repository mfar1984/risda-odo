import 'package:flutter/material.dart';
import '../theme/pastel_colors.dart';
import '../theme/text_styles.dart';

class AboutScreen extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: PastelColors.primary,
        title: Text('About', style: AppTextStyles.h2.copyWith(color: Colors.white)),
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      backgroundColor: PastelColors.background,
      body: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 24),
        child: Align(
          alignment: Alignment.topCenter,
          child: Card(
            color: PastelColors.cardBackground,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8), side: BorderSide(color: PastelColors.border)),
            elevation: 0,
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 32, vertical: 28),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text('RISDA Driver App', style: AppTextStyles.h2),
                  const SizedBox(height: 10),
                  Text('Version: 1.0.0', style: AppTextStyles.bodyLarge),
                  Text('Build Number: 100', style: AppTextStyles.bodyLarge),
                  Text('Release Date: 15 July 2024', style: AppTextStyles.bodyLarge),
                  const SizedBox(height: 10),
                  Text('© 2024 RISDA', style: AppTextStyles.bodyMedium),
                  Text('Developer: RISDA Bahagian Sibu', style: AppTextStyles.bodyMedium),
                  Text('Contact: support@risda.gov.my', style: AppTextStyles.bodyMedium),
                  Text('Website: https://www.risda.gov.my', style: AppTextStyles.bodyMedium),
                  const SizedBox(height: 10),
                  Text('Supported Platforms:', style: AppTextStyles.bodyLarge),
                  Text('- Android', style: AppTextStyles.bodyMedium),
                  Text('- iOS', style: AppTextStyles.bodyMedium),
                  Text('- Web', style: AppTextStyles.bodyMedium),
                  const SizedBox(height: 10),
                  Text('Description:', style: AppTextStyles.bodyLarge),
                  Text('The RISDA Driver App is designed to help RISDA drivers manage their trips, claims, and vehicle logs efficiently. The app provides real-time analytics, program management, and secure access for all registered drivers.', style: AppTextStyles.bodyMedium),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
} 