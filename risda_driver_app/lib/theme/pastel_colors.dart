import 'package:flutter/material.dart';

class PastelColors {
  // Primary Colors (More subtle)
  static const Color primary = Color(0xFF5170FF);      // Accent Blue
  static const Color secondary = Color(0xFF5170FF);    // Accent Blue (for gradient consistency)
  static const Color accent = Color(0xFF5170FF);       // Accent Blue
  
  // Background Colors
  static const Color background = Color(0xFFFAFBFC);   // Very Light Grey
  static const Color surface = Color(0xFFFFFFFF);      // Pure White
  static const Color cardBackground = Color(0xFFFEFEFE);
  
  // Text Colors (Smaller contrast)
  static const Color textPrimary = Color(0xFF374151);  // Darker Grey
  static const Color textSecondary = Color(0xFF6B7280); // Medium Grey
  static const Color textLight = Color(0xFF9CA3AF);    // Light Grey
  
  // Status Colors (Darker, more visible)
  static const Color success = Color(0xFF34D399);      // Medium Green
  static const Color successText = Color(0xFF065F46);  // Dark Green
  static const Color warning = Color(0xFFFBBF24);      // Medium Yellow
  static const Color warningText = Color(0xFF92400E);  // Dark Orange
  static const Color error = Color(0xFFF87171);        // Medium Red
  static const Color errorText = Color(0xFF991B1B);    // Dark Red
  static const Color info = Color(0xFF60A5FA);         // Medium Blue
  static const Color infoText = Color(0xFF1E40AF);     // Dark Blue
  
  // Additional Colors
  static const Color border = Color(0xFFE5E7EB);       // Light Border
  static const Color divider = Color(0xFFF3F4F6);      // Very Light Divider
  
  // Gradients
  static const LinearGradient primaryGradient = LinearGradient(
    colors: [primary, secondary],
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );
  
  static const LinearGradient cardGradient = LinearGradient(
    colors: [surface, cardBackground],
    begin: Alignment.topCenter,
    end: Alignment.bottomCenter,
  );
}
