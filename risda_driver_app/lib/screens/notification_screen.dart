import 'package:flutter/material.dart';
import '../theme/pastel_colors.dart';
import '../theme/text_styles.dart';

class NotificationScreen extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: PastelColors.primary,
        title: Text('Notification', style: AppTextStyles.h2.copyWith(color: Colors.white)),
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      backgroundColor: PastelColors.background,
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          Card(
            color: PastelColors.info,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(6), side: BorderSide(color: PastelColors.border)),
            child: ListTile(
              leading: Icon(Icons.notifications, color: PastelColors.infoText),
              title: Text('Claim Approved', style: AppTextStyles.bodyLarge),
              subtitle: Text('Your claim for Program A has been approved.', style: AppTextStyles.bodyMedium),
            ),
          ),
          Card(
            color: PastelColors.success,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(6), side: BorderSide(color: PastelColors.border)),
            child: ListTile(
              leading: Icon(Icons.check_circle, color: PastelColors.successText),
              title: Text('Check In Successful', style: AppTextStyles.bodyLarge),
              subtitle: Text('You have checked in for Program B.', style: AppTextStyles.bodyMedium),
            ),
          ),
          Card(
            color: PastelColors.warning,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(6), side: BorderSide(color: PastelColors.border)),
            child: ListTile(
              leading: Icon(Icons.warning, color: PastelColors.warningText),
              title: Text('Vehicle Maintenance Due', style: AppTextStyles.bodyLarge),
              subtitle: Text('Vehicle QAA1234 is due for maintenance.', style: AppTextStyles.bodyMedium),
            ),
          ),
          Card(
            color: PastelColors.error,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(6), side: BorderSide(color: PastelColors.border)),
            child: ListTile(
              leading: Icon(Icons.error, color: PastelColors.errorText),
              title: Text('Claim Rejected', style: AppTextStyles.bodyLarge),
              subtitle: Text('Your claim for Program C was rejected.', style: AppTextStyles.bodyMedium),
            ),
          ),
        ],
      ),
    );
  }
} 