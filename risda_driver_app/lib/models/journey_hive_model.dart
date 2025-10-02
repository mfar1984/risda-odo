import 'package:hive/hive.dart';

part 'journey_hive_model.g.dart';

@HiveType(typeId: 0)
class JourneyHive extends HiveObject {
  // ===== SERVER FIELDS (from backend log_pemandu table) =====
  @HiveField(0)
  int? id; // Server ID (null if not synced yet)
  
  @HiveField(1)
  int pemanduId; // pemandu_id
  
  @HiveField(2)
  int kenderaanId; // kenderaan_id
  
  @HiveField(3)
  int? programId; // program_id (nullable)
  
  @HiveField(4)
  DateTime tarikhPerjalanan; // tarikh_perjalanan
  
  @HiveField(5)
  String masaKeluar; // masa_keluar (time as string "HH:mm")
  
  @HiveField(6)
  String? masaMasuk; // masa_masuk (nullable)
  
  @HiveField(7)
  String destinasi; // destinasi
  
  @HiveField(8)
  String? catatan; // catatan
  
  @HiveField(9)
  int odometerKeluar; // odometer_keluar
  
  @HiveField(10)
  int? odometerMasuk; // odometer_masuk
  
  @HiveField(11)
  int? jarak; // jarak (auto-calculated)
  
  @HiveField(12)
  double? literMinyak; // liter_minyak
  
  @HiveField(13)
  double? kosMinyak; // kos_minyak
  
  @HiveField(14)
  String? stesenMinyak; // stesen_minyak
  
  @HiveField(15)
  String? resitMinyak; // resit_minyak (file path or URL)
  
  @HiveField(16)
  String status; // status: 'dalam_perjalanan', 'selesai', 'tertunda'
  
  @HiveField(17)
  String? organisasiId; // organisasi_id
  
  @HiveField(18)
  int diciptaOleh; // dicipta_oleh
  
  @HiveField(19)
  int? dikemaskiniOleh; // dikemaskini_oleh
  
  @HiveField(20)
  double? lokasiCheckinLat; // lokasi_checkin_lat
  
  @HiveField(21)
  double? lokasiCheckinLong; // lokasi_checkin_long
  
  @HiveField(22)
  double? lokasiCheckoutLat; // lokasi_checkout_lat
  
  @HiveField(23)
  double? lokasiCheckoutLong; // lokasi_checkout_long
  
  @HiveField(24)
  DateTime? createdAt; // created_at
  
  @HiveField(25)
  DateTime? updatedAt; // updated_at
  
  // ===== OFFLINE-SPECIFIC FIELDS =====
  @HiveField(26)
  String localId; // UUID for local identification
  
  @HiveField(27)
  bool isSynced; // Sync status
  
  @HiveField(28)
  DateTime? lastSyncAttempt; // Last sync try
  
  @HiveField(29)
  int syncRetries; // Retry count
  
  @HiveField(30)
  String? syncError; // Error message if sync failed
  
  @HiveField(31)
  String? odometerPhotoLocal; // Local photo path (before upload)
  
  @HiveField(32)
  String? resitMinyakLocal; // Local receipt path (before upload)

  JourneyHive({
    this.id,
    required this.pemanduId,
    required this.kenderaanId,
    this.programId,
    required this.tarikhPerjalanan,
    required this.masaKeluar,
    this.masaMasuk,
    required this.destinasi,
    this.catatan,
    required this.odometerKeluar,
    this.odometerMasuk,
    this.jarak,
    this.literMinyak,
    this.kosMinyak,
    this.stesenMinyak,
    this.resitMinyak,
    required this.status,
    this.organisasiId,
    required this.diciptaOleh,
    this.dikemaskiniOleh,
    this.lokasiCheckinLat,
    this.lokasiCheckinLong,
    this.lokasiCheckoutLat,
    this.lokasiCheckoutLong,
    this.createdAt,
    this.updatedAt,
    required this.localId,
    this.isSynced = false,
    this.lastSyncAttempt,
    this.syncRetries = 0,
    this.syncError,
    this.odometerPhotoLocal,
    this.resitMinyakLocal,
  });

  // Convert to JSON for API
  Map<String, dynamic> toJson() {
    return {
      if (id != null) 'id': id,
      'pemandu_id': pemanduId,
      'kenderaan_id': kenderaanId,
      'program_id': programId,
      'tarikh_perjalanan': tarikhPerjalanan.toIso8601String().split('T')[0],
      'masa_keluar': masaKeluar,
      'masa_masuk': masaMasuk,
      'destinasi': destinasi,
      'catatan': catatan,
      'odometer_keluar': odometerKeluar,
      'odometer_masuk': odometerMasuk,
      'jarak': jarak,
      'liter_minyak': literMinyak,
      'kos_minyak': kosMinyak,
      'stesen_minyak': stesenMinyak,
      'resit_minyak': resitMinyak,
      'status': status,
      'organisasi_id': organisasiId,
      'dicipta_oleh': diciptaOleh,
      'dikemaskini_oleh': dikemaskiniOleh,
      'lokasi_checkin_lat': lokasiCheckinLat,
      'lokasi_checkin_long': lokasiCheckinLong,
      'lokasi_checkout_lat': lokasiCheckoutLat,
      'lokasi_checkout_long': lokasiCheckoutLong,
    };
  }

  // Create from API response
  factory JourneyHive.fromJson(Map<String, dynamic> json, {required String localId}) {
    return JourneyHive(
      id: json['id'],
      pemanduId: json['pemandu_id'],
      kenderaanId: json['kenderaan_id'],
      programId: json['program_id'],
      tarikhPerjalanan: DateTime.parse(json['tarikh_perjalanan']),
      masaKeluar: json['masa_keluar'],
      masaMasuk: json['masa_masuk'],
      destinasi: json['destinasi'],
      catatan: json['catatan'],
      odometerKeluar: json['odometer_keluar'],
      odometerMasuk: json['odometer_masuk'],
      jarak: json['jarak'],
      literMinyak: json['liter_minyak']?.toDouble(),
      kosMinyak: json['kos_minyak']?.toDouble(),
      stesenMinyak: json['stesen_minyak'],
      resitMinyak: json['resit_minyak'],
      status: json['status'],
      organisasiId: json['organisasi_id'],
      diciptaOleh: json['dicipta_oleh'],
      dikemaskiniOleh: json['dikemaskini_oleh'],
      lokasiCheckinLat: json['lokasi_checkin_lat']?.toDouble(),
      lokasiCheckinLong: json['lokasi_checkin_long']?.toDouble(),
      lokasiCheckoutLat: json['lokasi_checkout_lat']?.toDouble(),
      lokasiCheckoutLong: json['lokasi_checkout_long']?.toDouble(),
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
      localId: localId,
      isSynced: true, // From server, so synced
    );
  }
}

