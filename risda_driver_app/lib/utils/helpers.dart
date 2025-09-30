import 'package:flutter/material.dart';
import 'package:intl/intl.dart';

class Helpers {
  /// Format date to Malaysian format (dd/MM/yyyy)
  static String formatDate(DateTime? date) {
    if (date == null) return '-';
    return DateFormat('dd/MM/yyyy').format(date);
  }

  /// Format datetime to Malaysian format (dd/MM/yyyy HH:mm)
  static String formatDateTime(DateTime? dateTime) {
    if (dateTime == null) return '-';
    return DateFormat('dd/MM/yyyy HH:mm').format(dateTime);
  }

  /// Format time only (HH:mm)
  static String formatTime(DateTime? time) {
    if (time == null) return '-';
    return DateFormat('HH:mm').format(time);
  }

  /// Show success snackbar
  static void showSuccess(BuildContext context, String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: Colors.green,
        behavior: SnackBarBehavior.floating,
      ),
    );
  }

  /// Show error snackbar
  static void showError(BuildContext context, String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: Colors.red,
        behavior: SnackBarBehavior.floating,
      ),
    );
  }

  /// Show info snackbar
  static void showInfo(BuildContext context, String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: Colors.blue,
        behavior: SnackBarBehavior.floating,
      ),
    );
  }

  /// Format number with 2 decimal places
  static String formatNumber(double? number) {
    if (number == null) return '0.00';
    return number.toStringAsFixed(2);
  }

  /// Get status color
  static Color getStatusColor(String status) {
    switch (status.toLowerCase()) {
      case 'aktif':
      case 'dalam_perjalanan':
      case 'lulus':
        return Colors.green;
      case 'selesai':
        return Colors.blue;
      case 'tertunda':
      case 'draf':
        return Colors.orange;
      case 'tidak_aktif':
      case 'batal':
        return Colors.red;
      default:
        return Colors.grey;
    }
  }

  /// Get status label in Malay
  static String getStatusLabel(String status) {
    switch (status.toLowerCase()) {
      case 'aktif':
        return 'Aktif';
      case 'dalam_perjalanan':
        return 'Dalam Perjalanan';
      case 'selesai':
        return 'Selesai';
      case 'tertunda':
        return 'Tertunda';
      case 'draf':
        return 'Draf';
      case 'lulus':
        return 'Lulus';
      case 'tidak_aktif':
        return 'Tidak Aktif';
      case 'batal':
        return 'Batal';
      default:
        return status;
    }
  }
}
