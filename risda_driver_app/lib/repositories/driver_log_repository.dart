import '../models/driver_log.dart';
import '../services/api_service.dart';
import 'dart:io';

class DriverLogRepository {
  final ApiService _apiService;

  DriverLogRepository(this._apiService);

  Future<DriverLog?> getActiveTrip() async {
    return await _apiService.getActiveTrip();
  }

  Future<DriverLog> checkIn({
    required int programId,
    required int kenderaanId,
    required String location,
    required double odometerReading,
    String? notes,
    File? photo,
  }) async {
    return await _apiService.startTrip(
      programId: programId,
      kenderaanId: kenderaanId,
      lokasiMula: location,
      bacaanOdometerMula: odometerReading,
      notaMula: notes,
      odometerPhoto: photo,
    );
  }

  Future<DriverLog> checkOut({
    required int logId,
    required double distance,
    required double odometerReading,
    required String location,
    String? notes,
    double? latitude,
    double? longitude,
    File? photo,
  }) async {
    return await _apiService.endTrip(
      logId: logId,
      jarakPerjalanan: distance,
      bacaanOdometerTamat: odometerReading,
      lokasiTamat: location,
      notaTamat: notes,
      gpsLatitude: latitude,
      gpsLongitude: longitude,
      odometerPhoto: photo,
    );
  }
}
