import 'package:hive/hive.dart';

part 'program_hive_model.g.dart';

@HiveType(typeId: 1)
class ProgramHive extends HiveObject {
  @HiveField(0)
  int id; // Server ID
  
  @HiveField(1)
  String namaProgram; // nama_program
  
  @HiveField(2)
  String? kenderaanId; // kenderaan_id (can be multiple, stored as JSON string)
  
  @HiveField(3)
  String? pemanduId; // pemandu_id (can be multiple, stored as JSON string)
  
  @HiveField(4)
  DateTime? tarikhMula; // tarikh_mula
  
  @HiveField(5)
  DateTime? tarikhTamat; // tarikh_tamat
  
  @HiveField(6)
  String? lokasi; // lokasi
  
  @HiveField(7)
  double? lokasiLat; // lokasi_lat
  
  @HiveField(8)
  double? lokasiLong; // lokasi_long
  
  @HiveField(9)
  String? peneranganProgram; // penerangan_program
  
  @HiveField(10)
  int? jarakAnggaran; // jarak_anggaran
  
  @HiveField(11)
  String? catatanTambahan; // catatan_tambahan
  
  @HiveField(12)
  String status; // status: 'belum_bermula', 'sedang_berlangsung', 'selesai', 'dibatalkan'
  
  @HiveField(13)
  String? organisasiId; // organisasi_id
  
  @HiveField(14)
  int diciptaOleh; // dicipta_oleh
  
  @HiveField(15)
  int? dikemaskiniOleh; // dikemaskini_oleh
  
  @HiveField(16)
  DateTime? createdAt;
  
  @HiveField(17)
  DateTime? updatedAt;
  
  // Offline fields
  @HiveField(18)
  DateTime lastSync; // Last synced time

  ProgramHive({
    required this.id,
    required this.namaProgram,
    this.kenderaanId,
    this.pemanduId,
    this.tarikhMula,
    this.tarikhTamat,
    this.lokasi,
    this.lokasiLat,
    this.lokasiLong,
    this.peneranganProgram,
    this.jarakAnggaran,
    this.catatanTambahan,
    required this.status,
    this.organisasiId,
    required this.diciptaOleh,
    this.dikemaskiniOleh,
    this.createdAt,
    this.updatedAt,
    required this.lastSync,
  });

  factory ProgramHive.fromJson(Map<String, dynamic> json) {
    return ProgramHive(
      id: json['id'],
      namaProgram: json['nama_program'],
      kenderaanId: json['kenderaan_id'],
      pemanduId: json['pemandu_id'],
      tarikhMula: json['tarikh_mula'] != null ? DateTime.parse(json['tarikh_mula']) : null,
      tarikhTamat: json['tarikh_tamat'] != null ? DateTime.parse(json['tarikh_tamat']) : null,
      lokasi: json['lokasi'],
      lokasiLat: json['lokasi_lat']?.toDouble(),
      lokasiLong: json['lokasi_long']?.toDouble(),
      peneranganProgram: json['penerangan_program'],
      jarakAnggaran: json['jarak_anggaran'],
      catatanTambahan: json['catatan_tambahan'],
      status: json['status'],
      organisasiId: json['organisasi_id'],
      diciptaOleh: json['dicipta_oleh'],
      dikemaskiniOleh: json['dikemaskini_oleh'],
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
      lastSync: DateTime.now(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'nama_program': namaProgram,
      'kenderaan_id': kenderaanId,
      'pemandu_id': pemanduId,
      'tarikh_mula': tarikhMula?.toIso8601String(),
      'tarikh_tamat': tarikhTamat?.toIso8601String(),
      'lokasi': lokasi,
      'lokasi_lat': lokasiLat,
      'lokasi_long': lokasiLong,
      'penerangan_program': peneranganProgram,
      'jarak_anggaran': jarakAnggaran,
      'catatan_tambahan': catatanTambahan,
      'status': status,
      'organisasi_id': organisasiId,
      'dicipta_oleh': diciptaOleh,
      'dikemaskini_oleh': dikemaskiniOleh,
    };
  }
}

