import 'package:flutter/material.dart';
import '../theme/pastel_colors.dart';

class ProfessionalCard extends StatelessWidget {
  final Widget child;
  final Color? color;
  final EdgeInsetsGeometry? padding;
  final VoidCallback? onTap;
  final bool isElevated;

  const ProfessionalCard({
    super.key,
    required this.child,
    this.color,
    this.padding,
    this.onTap,
    this.isElevated = true,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        margin: const EdgeInsets.only(bottom: 12),
        padding: padding ?? const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: color ?? Colors.white,
          borderRadius: BorderRadius.circular(3), // 3px corner radius
          boxShadow: isElevated ? [
            // Subtle 3D effect
            BoxShadow(
              color: Colors.black.withOpacity(0.04),
              blurRadius: 8,
              offset: const Offset(0, 2),
            ),
            BoxShadow(
              color: Colors.black.withOpacity(0.02),
              blurRadius: 16,
              offset: const Offset(0, 4),
            ),
          ] : null,
          border: Border.all(
            color: PastelColors.border,
            width: 0.5,
          ),
        ),
        child: child,
      ),
    );
  }
}
