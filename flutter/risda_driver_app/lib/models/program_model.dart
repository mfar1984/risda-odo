import 'package:hive/hive.dart';

part 'program_model.g.dart';

@HiveType(typeId: 2)
class Program {
  @HiveField(0)
  final int id;

  @HiveField(1)
  final String namaProgram;

  @HiveField(2)
  final String? penerangan;

  @HiveField(3)
  final String? lokasiProgram;

  @HiveField(4)
  final DateTime tarikhMula;

  @HiveField(5)
  final DateTime tarikhSelesai;

  @HiveField(6)
  final double anggaranKm;

  @HiveField(7)
  final String status;

  @HiveField(8)
  final int permohonanDariStaffId;

  @HiveField(9)
  final String? createdAt;

  @HiveField(10)
  final String? updatedAt;

  Program({
    required this.id,
    required this.namaProgram,
    this.penerangan,
    this.lokasiProgram,
    required this.tarikhMula,
    required this.tarikhSelesai,
    required this.anggaranKm,
    required this.status,
    required this.permohonanDariStaffId,
    this.createdAt,
    this.updatedAt,
  });

  factory Program.fromJson(Map<String, dynamic> json) {
    return Program(
      id: json['id'],
      namaProgram: json['nama_program'],
      penerangan: json['penerangan'],
      lokasiProgram: json['lokasi_program'],
      tarikhMula: DateTime.parse(json['tarikh_mula']),
      tarikhSelesai: DateTime.parse(json['tarikh_selesai']),
      anggaranKm: double.parse(json['anggaran_km'].toString()),
      status: json['status'],
      permohonanDariStaffId: json['permohonan_dari_staff_id'],
      createdAt: json['created_at'],
      updatedAt: json['updated_at'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'nama_program': namaProgram,
      'penerangan': penerangan,
      'lokasi_program': lokasiProgram,
      'tarikh_mula': tarikhMula.toIso8601String(),
      'tarikh_selesai': tarikhSelesai.toIso8601String(),
      'anggaran_km': anggaranKm,
      'status': status,
      'permohonan_dari_staff_id': permohonanDariStaffId,
      'created_at': createdAt,
      'updated_at': updatedAt,
    };
  }

  bool get isActive {
    final now = DateTime.now();
    return status == 'aktif' && now.isAfter(tarikhMula) && now.isBefore(tarikhSelesai);
  }
} 