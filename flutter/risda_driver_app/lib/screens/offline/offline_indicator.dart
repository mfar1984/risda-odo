import 'package:flutter/material.dart';
import '../../utils/sync_manager.dart';
import '../../theme/pastel_colors.dart';
import '../../theme/text_styles.dart';

class OfflineIndicator extends StatelessWidget {
  final SyncStatus status;
  
  const OfflineIndicator({
    Key? key,
    required this.status,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    if (status.isOnline && !status.isSyncing) {
      return const SizedBox.shrink(); // Online, no indicator needed
    }
    
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 4, horizontal: 8),
      color: status.isOnline 
          ? PastelColors.info.withOpacity(0.2)
          : PastelColors.warning.withOpacity(0.2),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(
            status.isOnline ? Icons.sync : Icons.cloud_off,
            size: 14,
            color: status.isOnline ? PastelColors.infoText : PastelColors.warningText,
          ),
          const SizedBox(width: 8),
          Text(
            status.isOnline 
                ? 'Menyegerakkan data...' 
                : 'Mod luar talian',
            style: AppTextStyles.bodySmall.copyWith(
              color: status.isOnline ? PastelColors.infoText : PastelColors.warningText,
              fontWeight: FontWeight.w500,
            ),
          ),
        ],
      ),
    );
  }
} 