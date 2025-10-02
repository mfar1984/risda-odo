import 'package:flutter/material.dart';
import 'pastel_colors.dart';

class AppTextStyles {
  // Headings
  static const TextStyle h1 = TextStyle(
    fontSize: 20, // Reduced from 24
    fontWeight: FontWeight.w700,
    color: PastelColors.textPrimary,
  );
  
  static const TextStyle h2 = TextStyle(
    fontSize: 16, // Reduced from 18
    fontWeight: FontWeight.w600,
    color: PastelColors.textPrimary,
  );
  
  static const TextStyle h3 = TextStyle(
    fontSize: 14, // Reduced from 16
    fontWeight: FontWeight.w600,
    color: PastelColors.textPrimary,
  );
  
  // Body Text
  static const TextStyle bodyLarge = TextStyle(
    fontSize: 14, // Reduced from 16
    color: PastelColors.textPrimary,
  );
  
  static const TextStyle bodyMedium = TextStyle(
    fontSize: 12, // Reduced from 14
    color: PastelColors.textSecondary,
  );
  
  static const TextStyle bodySmall = TextStyle(
    fontSize: 11, // New smaller size
    color: PastelColors.textLight,
  );
  
  // Button Text
  static const TextStyle button = TextStyle(
    fontSize: 13, // Reduced from 14
    fontWeight: FontWeight.w500,
  );
  
  // Caption
  static const TextStyle caption = TextStyle(
    fontSize: 10, // Reduced from 12
    color: PastelColors.textSecondary,
  );
}
