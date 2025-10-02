import 'package:hive/hive.dart';

part 'driver_log_model.g.dart';

@HiveType(typeId: 4)
class DriverLog {
  @HiveField(0)
  final int? id;

  @HiveField(1)
  final int programId;

  @HiveField(2)
  final int pemanduId;

  @HiveField(3)
  final int kenderaanId;

  @HiveField(4)
  final String? namaLog;

  @HiveField(5)
  DateTime? checkinTime;

  @HiveField(6)
  DateTime? checkoutTime;

  @HiveField(7)
  int? jarakPerjalanan;

  @HiveField(8)
  int? bacaanOdometer;

  @HiveField(9)
  String? odometerPhoto;

  @HiveField(10)
  final String lokasiCheckin;

  @HiveField(11)
  double? gpsLatitude;

  @HiveField(12)
  double? gpsLongitude;

  @HiveField(13)
  String? lokasiCheckout;

  @HiveField(14)
  String? catatan;

  @HiveField(15)
  String status;

  @HiveField(16)
  final int createdBy;

  @HiveField(17)
  bool isSynced;

  @HiveField(18)
  final DateTime createdAt;

  @HiveField(19)
  DateTime? updatedAt;

  // Checkout fields
  @HiveField(20)
  int? bacaanOdometerCheckout;

  @HiveField(21)
  String? odometerPhotoCheckout;

  // Program and Kenderaan data
  @HiveField(22)
  String? programNama;

  @HiveField(23)
  String? programLokasi;

  @HiveField(24)
  String? kenderaanNoPlat;

  @HiveField(25)
  String? kenderaanJenama;

  @HiveField(26)
  String? kenderaanModel;

  DriverLog({
    this.id,
    required this.programId,
    required this.pemanduId,
    required this.kenderaanId,
    this.namaLog,
    this.checkinTime,
    this.checkoutTime,
    this.jarakPerjalanan = 0,
    this.bacaanOdometer,
    this.odometerPhoto,
    required this.lokasiCheckin,
    this.gpsLatitude,
    this.gpsLongitude,
    this.lokasiCheckout,
    this.catatan,
    required this.status,
    required this.createdBy,
    this.isSynced = false,
    required this.createdAt,
    this.updatedAt,
    this.bacaanOdometerCheckout,
    this.odometerPhotoCheckout,
    this.programNama,
    this.programLokasi,
    this.kenderaanNoPlat,
    this.kenderaanJenama,
    this.kenderaanModel,
  });

  factory DriverLog.fromJson(Map<String, dynamic> json) {
    // Helper function to safely convert to int
    int? safeInt(dynamic value) {
      if (value == null) return null;
      if (value is int) return value;
      if (value is String) return int.tryParse(value);
      return null;
    }
    
    // Helper function to safely convert to double
    double? safeDouble(dynamic value) {
      if (value == null) return null;
      if (value is double) return value;
      if (value is int) return value.toDouble();
      if (value is String) return double.tryParse(value);
      return null;
    }
    
    // Helper function to safely parse DateTime
    DateTime? safeDateTime(dynamic value) {
      if (value == null) return null;
      
      try {
        if (value is String) {
          // Try to parse the date string
          final dateTime = DateTime.parse(value);
          print('DEBUG: Successfully parsed date: $value to $dateTime');
          return dateTime;
        }
      } catch (e) {
        print('DEBUG: Error parsing date: $value - $e');
      }
      
      // Default to current time if parsing fails
      print('DEBUG: Using current time as fallback for date parsing');
      return DateTime.now();
    }
    
    // Helper function to safely parse nullable DateTime
    DateTime? safeNullableDateTime(dynamic value) {
      if (value == null) return null;
      
      try {
        if (value is String) {
          // Try to parse the date string
          final dateTime = DateTime.parse(value);
          print('DEBUG: Successfully parsed nullable date: $value to $dateTime');
          return dateTime;
        }
      } catch (e) {
        print('DEBUG: Error parsing nullable date: $value - $e');
      }
      
      return null;
    }
    
    print('DEBUG: Parsing DriverLog from JSON: ${json['id']}');
    print('DEBUG: Check-in time: ${json['checkin_time']}');
    print('DEBUG: Checkout time: ${json['checkout_time']}');
    
    return DriverLog(
      id: safeInt(json['id']),
      programId: safeInt(json['program_id']) ?? 0,
      pemanduId: safeInt(json['pemandu_id']) ?? 0,
      kenderaanId: safeInt(json['kenderaan_id']) ?? 0,
      namaLog: json['nama_log'],
      checkinTime: safeDateTime(json['checkin_time']),
      checkoutTime: safeNullableDateTime(json['checkout_time']),
      jarakPerjalanan: safeInt(json['jarak_perjalanan']) ?? 0,
      bacaanOdometer: safeInt(json['bacaan_odometer']),
      odometerPhoto: json['odometer_photo'] ?? json['odometer_photo_checkin'],
      lokasiCheckin: json['lokasi_checkin'] ?? '',
      gpsLatitude: safeDouble(json['gps_latitude']),
      gpsLongitude: safeDouble(json['gps_longitude']),
      lokasiCheckout: json['lokasi_checkout'],
      catatan: json['catatan'],
      status: json['status'] ?? 'aktif',
      createdBy: safeInt(json['created_by']) ?? 0,
      isSynced: true,
      createdAt: safeDateTime(json['created_at']) ?? DateTime.now(),
      updatedAt: safeNullableDateTime(json['updated_at']),
      bacaanOdometerCheckout: safeInt(json['bacaan_odometer_tamat']),
      odometerPhotoCheckout: json['odometer_photo_checkout'],
      // Program data
      programNama: json['program']?['nama_program'],
      programLokasi: json['program']?['lokasi_program'],
      // Kenderaan data
      kenderaanNoPlat: json['kenderaan']?['no_plat'],
      kenderaanJenama: json['kenderaan']?['jenama'],
      kenderaanModel: json['kenderaan']?['model'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'program_id': programId,
      'pemandu_id': pemanduId,
      'kenderaan_id': kenderaanId,
      'nama_log': namaLog,
      'checkin_time': checkinTime?.toIso8601String(),
      'checkout_time': checkoutTime?.toIso8601String(),
      'jarak_perjalanan': jarakPerjalanan,
      'bacaan_odometer': bacaanOdometer,
      'odometer_photo': odometerPhoto,
      'lokasi_checkin': lokasiCheckin,
      'gps_latitude': gpsLatitude,
      'gps_longitude': gpsLongitude,
      'lokasi_checkout': lokasiCheckout,
      'catatan': catatan,
      'status': status,
      'created_by': createdBy,
      'bacaan_odometer_tamat': bacaanOdometerCheckout,
      'odometer_photo_checkout': odometerPhotoCheckout,
      // Program data
      'program_nama': programNama,
      'program_lokasi': programLokasi,
      // Kenderaan data
      'kenderaan_no_plat': kenderaanNoPlat,
      'kenderaan_jenama': kenderaanJenama,
      'kenderaan_model': kenderaanModel,
    };
  }

  bool get isActive => status == 'aktif' && checkinTime != null && checkoutTime == null;
  bool get isCompleted => status == 'selesai';
  bool get isPending => status == 'tertunda';

  void completeLog({
    required DateTime checkoutTime,
    required int jarakPerjalanan,
    required int bacaanOdometer,
    required String lokasiCheckout,
    String? catatan,
    double? gpsLatitude,
    double? gpsLongitude,
  }) {
    this.checkoutTime = checkoutTime; // Use the provided checkout time
    this.jarakPerjalanan = jarakPerjalanan;
    this.bacaanOdometer = bacaanOdometer;
    this.lokasiCheckout = lokasiCheckout;
    this.catatan = catatan;
    this.gpsLatitude = gpsLatitude;
    this.gpsLongitude = gpsLongitude;
    this.status = 'selesai';
    this.updatedAt = DateTime.now(); // Only the updatedAt uses current time
    this.isSynced = false;
  }
} 