import 'package:hive/hive.dart';

part 'vehicle_model.g.dart';

@HiveType(typeId: 3)
class Vehicle {
  @HiveField(0)
  final int id;

  @HiveField(1)
  final String noPlat;

  @HiveField(2)
  final String jenama;

  @HiveField(3)
  final String model;

  @HiveField(4)
  final int tahun;

  @HiveField(5)
  final String noEnjin;

  @HiveField(6)
  final String noCasis;

  @HiveField(7)
  final String jenisBahanApi;

  @HiveField(8)
  final String kapasitiMuatan;

  @HiveField(9)
  final String warna;

  @HiveField(10)
  final int odometerSemasa;

  @HiveField(11)
  final DateTime cukaiTamatTempoh;

  @HiveField(12)
  final DateTime tarikhPendaftaran;

  @HiveField(13)
  final DateTime? penyelenggaraanSeterusnya;

  @HiveField(14)
  final String status;

  Vehicle({
    required this.id,
    required this.noPlat,
    required this.jenama,
    required this.model,
    required this.tahun,
    required this.noEnjin,
    required this.noCasis,
    required this.jenisBahanApi,
    required this.kapasitiMuatan,
    required this.warna,
    required this.odometerSemasa,
    required this.cukaiTamatTempoh,
    required this.tarikhPendaftaran,
    this.penyelenggaraanSeterusnya,
    required this.status,
  });

  factory Vehicle.fromJson(Map<String, dynamic> json) {
    return Vehicle(
      id: json['id'],
      noPlat: json['no_plat'],
      jenama: json['jenama'],
      model: json['model'],
      tahun: int.parse(json['tahun'].toString()),
      noEnjin: json['no_enjin'],
      noCasis: json['no_casis'],
      jenisBahanApi: json['jenis_bahan_api'],
      kapasitiMuatan: json['kapasiti_muatan'],
      warna: json['warna'],
      odometerSemasa: json['odometer_semasa'],
      cukaiTamatTempoh: DateTime.parse(json['cukai_tamat_tempoh']),
      tarikhPendaftaran: DateTime.parse(json['tarikh_pendaftaran']),
      penyelenggaraanSeterusnya: json['penyelenggaraan_seterusnya'] != null
          ? DateTime.parse(json['penyelenggaraan_seterusnya'])
          : null,
      status: json['status'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'no_plat': noPlat,
      'jenama': jenama,
      'model': model,
      'tahun': tahun,
      'no_enjin': noEnjin,
      'no_casis': noCasis,
      'jenis_bahan_api': jenisBahanApi,
      'kapasiti_muatan': kapasitiMuatan,
      'warna': warna,
      'odometer_semasa': odometerSemasa,
      'cukai_tamat_tempoh': cukaiTamatTempoh.toIso8601String(),
      'tarikh_pendaftaran': tarikhPendaftaran.toIso8601String(),
      'penyelenggaraan_seterusnya': penyelenggaraanSeterusnya?.toIso8601String(),
      'status': status,
    };
  }

  String get displayName => '$noPlat - $jenama $model';

  bool get isActive => status == 'aktif';
} 