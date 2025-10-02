import 'package:flutter/material.dart';
import '../theme/pastel_colors.dart';
import '../theme/text_styles.dart';

class HelpScreen extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: PastelColors.primary,
        title: Text('Privacy Policy', style: AppTextStyles.h2.copyWith(color: Colors.white)),
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      backgroundColor: PastelColors.background,
      body: Center(
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
                  Text('Privacy Policy', style: AppTextStyles.h2),
                  const SizedBox(height: 18),
                  Text('1. Introduction', style: AppTextStyles.bodyLarge.copyWith(fontWeight: FontWeight.w600)),
                  Text('This Privacy Policy explains how RISDA Driver App collects, uses, discloses, and protects your personal information when you use our application.', style: AppTextStyles.bodyMedium),
                  const SizedBox(height: 14),
                  Text('2. Information We Collect', style: AppTextStyles.bodyLarge.copyWith(fontWeight: FontWeight.w600)),
                  Text('We may collect the following types of information:', style: AppTextStyles.bodyMedium),
                  Text('- Personal identification (name, email, IC number, phone number)', style: AppTextStyles.bodyMedium),
                  Text('- Location data (for check-in/out and trip logs)', style: AppTextStyles.bodyMedium),
                  Text('- Device information (model, OS version)', style: AppTextStyles.bodyMedium),
                  Text('- Usage data (app interactions, log history)', style: AppTextStyles.bodyMedium),
                  const SizedBox(height: 14),
                  Text('3. How We Use Your Information', style: AppTextStyles.bodyLarge.copyWith(fontWeight: FontWeight.w600)),
                  Text('We use your information to:', style: AppTextStyles.bodyMedium),
                  Text('- Provide and improve app functionality', style: AppTextStyles.bodyMedium),
                  Text('- Process check-in/out and claims', style: AppTextStyles.bodyMedium),
                  Text('- Communicate important updates', style: AppTextStyles.bodyMedium),
                  Text('- Ensure security and compliance', style: AppTextStyles.bodyMedium),
                  const SizedBox(height: 14),
                  Text('4. Data Sharing & Disclosure', style: AppTextStyles.bodyLarge.copyWith(fontWeight: FontWeight.w600)),
                  Text('We do not sell or rent your personal data. Your information may be shared with:', style: AppTextStyles.bodyMedium),
                  Text('- RISDA administrators for operational purposes', style: AppTextStyles.bodyMedium),
                  Text('- Service providers (e.g., cloud hosting) under strict confidentiality', style: AppTextStyles.bodyMedium),
                  Text('- Authorities if required by law', style: AppTextStyles.bodyMedium),
                  const SizedBox(height: 14),
                  Text('5. Data Security', style: AppTextStyles.bodyLarge.copyWith(fontWeight: FontWeight.w600)),
                  Text('We implement appropriate technical and organizational measures to protect your data against unauthorized access, alteration, disclosure, or destruction.', style: AppTextStyles.bodyMedium),
                  const SizedBox(height: 14),
                  Text('6. Your Rights', style: AppTextStyles.bodyLarge.copyWith(fontWeight: FontWeight.w600)),
                  Text('You have the right to access, correct, or delete your personal data. You may also request to restrict or object to certain processing activities.', style: AppTextStyles.bodyMedium),
                  const SizedBox(height: 14),
                  Text('7. Retention', style: AppTextStyles.bodyLarge.copyWith(fontWeight: FontWeight.w600)),
                  Text('We retain your data only as long as necessary for the purposes stated in this policy or as required by law.', style: AppTextStyles.bodyMedium),
                  const SizedBox(height: 14),
                  Text('8. Changes to This Policy', style: AppTextStyles.bodyLarge.copyWith(fontWeight: FontWeight.w600)),
                  Text('We may update this Privacy Policy from time to time. We will notify you of any significant changes via the app or email.', style: AppTextStyles.bodyMedium),
                  const SizedBox(height: 14),
                  Text('9. Contact Us', style: AppTextStyles.bodyLarge.copyWith(fontWeight: FontWeight.w600)),
                  Text('If you have any questions or concerns about this Privacy Policy, please contact RISDA admin at 03-8888 8888 or email support@risda.gov.my.', style: AppTextStyles.bodyMedium),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
} 