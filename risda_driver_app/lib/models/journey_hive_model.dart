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
  String? fotoOdometerKeluar; // foto_odometer_keluar (Start Journey photo)
  
  @HiveField(17)
  String? fotoOdometerMasuk; // foto_odometer_masuk (End Journey photo)
  
  @HiveField(18)
  String status; // status: 'dalam_perjalanan', 'selesai', 'tertunda'
  
  @HiveField(19)
  String? jenisOrganisasi; // jenis_organisasi ('semua', 'bahagian', 'stesen')
  
  @HiveField(20)
  String? organisasiId; // organisasi_id
  
  @HiveField(21)
  int diciptaOleh; // dicipta_oleh
  
  @HiveField(22)
  int? dikemaskiniOleh; // dikemaskini_oleh
  
  @HiveField(23)
  double? lokasiCheckinLat; // lokasi_checkin_lat
  
  @HiveField(24)
  double? lokasiCheckinLong; // lokasi_checkin_long
  
  @HiveField(25)
  double? lokasiCheckoutLat; // lokasi_checkout_lat
  
  @HiveField(26)
  double? lokasiCheckoutLong; // lokasi_checkout_long
  
  @HiveField(27)
  DateTime? createdAt; // created_at
  
  @HiveField(28)
  DateTime? updatedAt; // updated_at
  
  // ===== OFFLINE-SPECIFIC FIELDS =====
  @HiveField(29)
  String localId; // UUID for local identification
  
  @HiveField(30)
  bool isSynced; // Sync status
  
  @HiveField(31)
  DateTime? lastSyncAttempt; // Last sync try
  
  @HiveField(32)
  int syncRetries; // Retry count
  
  @HiveField(33)
  String? syncError; // Error message if sync failed
  
  @HiveField(34)
  String? fotoOdometerKeluarLocal; // Local Start Journey photo path (before upload)
  
  @HiveField(35)
  String? fotoOdometerMasukLocal; // Local End Journey photo path (before upload)
  
  @HiveField(36)
  String? resitMinyakLocal; // Local receipt path (before upload)

  @HiveField(37)
  String? noResit; // Fuel receipt reference number (no_resit)

  @HiveField(38)
  String? lokasiMulaPerjalanan; // Textual start location (lokasi_mula_perjalanan)

  @HiveField(39)
  String? lokasiTamatPerjalanan; // Textual end location (lokasi_tamat_perjalanan)

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
    this.fotoOdometerKeluar,
    this.fotoOdometerMasuk,
    required this.status,
    this.jenisOrganisasi,
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
    this.fotoOdometerKeluarLocal,
    this.fotoOdometerMasukLocal,
    this.resitMinyakLocal,
    this.noResit,
    this.lokasiMulaPerjalanan,
    this.lokasiTamatPerjalanan,
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
      'no_resit': noResit,
      'lokasi_mula_perjalanan': lokasiMulaPerjalanan,
      'lokasi_tamat_perjalanan': lokasiTamatPerjalanan,
      'foto_odometer_keluar': fotoOdometerKeluar,
      'foto_odometer_masuk': fotoOdometerMasuk,
      'status': status,
      'jenis_organisasi': jenisOrganisasi,
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
    int parseInt(dynamic v) {
      if (v == null) return 0;
      if (v is int) return v;
      return int.tryParse(v.toString()) ?? 0;
    }
    // Derive kenderaan_id and program_id from nested objects if needed
    final int derivedKenderaanId = json.containsKey('kenderaan_id')
        ? parseInt(json['kenderaan_id'])
        : (json['kenderaan'] is Map ? parseInt(json['kenderaan']['id']) : 0);
    final int? derivedProgramId = json.containsKey('program_id')
        ? parseInt(json['program_id'])
        : (json['program'] is Map ? parseInt(json['program']['id']) : null);
    return JourneyHive(
      id: json['id'],
      pemanduId: json['pemandu_id'] ?? 0,
      kenderaanId: derivedKenderaanId,
      programId: derivedProgramId,
      tarikhPerjalanan: json['tarikh_perjalanan'] != null 
          ? DateTime.parse(json['tarikh_perjalanan']) 
          : DateTime.now(),
      masaKeluar: json['masa_keluar'] ?? '00:00',
      masaMasuk: json['masa_masuk'],
      destinasi: json['destinasi'] ?? 'N/A',
      catatan: json['catatan'],
      odometerKeluar: json['odometer_keluar'] ?? 0,
      odometerMasuk: json['odometer_masuk'],
      jarak: json['jarak'],
      literMinyak: json['liter_minyak'] != null ? double.tryParse(json['liter_minyak'].toString()) : null,
      kosMinyak: json['kos_minyak'] != null ? double.tryParse(json['kos_minyak'].toString()) : null,
      stesenMinyak: json['stesen_minyak'],
      resitMinyak: json['resit_minyak'],
      noResit: json['no_resit'],
      lokasiMulaPerjalanan: json['lokasi_mula_perjalanan'],
      lokasiTamatPerjalanan: json['lokasi_tamat_perjalanan'],
      fotoOdometerKeluar: json['foto_odometer_keluar'],
      fotoOdometerMasuk: json['foto_odometer_masuk'],
      status: json['status'] ?? 'selesai',
      jenisOrganisasi: json['jenis_organisasi'],
      organisasiId: json['organisasi_id']?.toString(),
      diciptaOleh: json['dicipta_oleh'] ?? 0,
      dikemaskiniOleh: json['dikemaskini_oleh'],
      lokasiCheckinLat: json['lokasi_checkin_lat'] != null ? double.tryParse(json['lokasi_checkin_lat'].toString()) : null,
      lokasiCheckinLong: json['lokasi_checkin_long'] != null ? double.tryParse(json['lokasi_checkin_long'].toString()) : null,
      lokasiCheckoutLat: json['lokasi_checkout_lat'] != null ? double.tryParse(json['lokasi_checkout_lat'].toString()) : null,
      lokasiCheckoutLong: json['lokasi_checkout_long'] != null ? double.tryParse(json['lokasi_checkout_long'].toString()) : null,
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
      localId: localId,
      isSynced: true, // From server, so synced
    );
  }
}

