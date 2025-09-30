import 'package:flutter/material.dart';
import '../theme/pastel_colors.dart';
import '../theme/text_styles.dart';

class SyncStatusScreen extends StatelessWidget {
  const SyncStatusScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Status Penyegerakan'),
        backgroundColor: PastelColors.primary,
        foregroundColor: Colors.white,
      ),
      body: Center(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(
                Icons.sync,
                size: 80,
                color: PastelColors.success,
              ),
              const SizedBox(height: 24),
              Text(
                'Penyegerakan Aktif',
                style: AppTextStyles.h2.copyWith(color: Colors.grey[700]),
              ),
              const SizedBox(height: 12),
              Text(
                'Semua data telah diselaraskan dengan pelayan.',
                style: AppTextStyles.bodyMedium.copyWith(color: Colors.grey[600]),
                textAlign: TextAlign.center,
              ),
            ],
          ),
        ),
      ),
    );
  }
}