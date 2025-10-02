import 'package:hive/hive.dart';

part 'vehicle_hive_model.g.dart';

@HiveType(typeId: 2)
class VehicleHive extends HiveObject {
  @HiveField(0)
  int id;
  
  @HiveField(1)
  String noPendaftaran; // no_pendaftaran
  
  @HiveField(2)
  String jenisKenderaan; // jenis_kenderaan
  
  @HiveField(3)
  String? model;
  
  @HiveField(4)
  int? tahun;
  
  @HiveField(5)
  String? warna;
  
  @HiveField(6)
  int? bacanOdometerSemasaTerkini; // bacan_odometer_semasa_terkini
  
  @HiveField(7)
  String status; // status: 'aktif', 'tidak_aktif', 'dalam_penyelenggaraan'
  
  @HiveField(8)
  String? organisasiId;
  
  @HiveField(9)
  String jenisOrganisasi; // jenis_organisasi: 'semua', 'bahagian', 'stesen'
  
  @HiveField(10)
  int diciptaOleh;
  
  @HiveField(11)
  int? dikemaskiniOleh;
  
  @HiveField(12)
  DateTime? createdAt;
  
  @HiveField(13)
  DateTime? updatedAt;
  
  @HiveField(14)
  DateTime lastSync;

  VehicleHive({
    required this.id,
    required this.noPendaftaran,
    required this.jenisKenderaan,
    this.model,
    this.tahun,
    this.warna,
    this.bacanOdometerSemasaTerkini,
    required this.status,
    this.organisasiId,
    required this.jenisOrganisasi,
    required this.diciptaOleh,
    this.dikemaskiniOleh,
    this.createdAt,
    this.updatedAt,
    required this.lastSync,
  });

  factory VehicleHive.fromJson(Map<String, dynamic> json) {
    return VehicleHive(
      id: json['id'],
      noPendaftaran: json['no_pendaftaran'],
      jenisKenderaan: json['jenis_kenderaan'],
      model: json['model'],
      tahun: json['tahun'],
      warna: json['warna'],
      bacanOdometerSemasaTerkini: json['bacan_odometer_semasa_terkini'],
      status: json['status'],
      organisasiId: json['organisasi_id'],
      jenisOrganisasi: json['jenis_organisasi'],
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
      'no_pendaftaran': noPendaftaran,
      'jenis_kenderaan': jenisKenderaan,
      'model': model,
      'tahun': tahun,
      'warna': warna,
      'bacan_odometer_semasa_terkini': bacanOdometerSemasaTerkini,
      'status': status,
      'organisasi_id': organisasiId,
      'jenis_organisasi': jenisOrganisasi,
      'dicipta_oleh': diciptaOleh,
      'dikemaskini_oleh': dikemaskiniOleh,
    };
  }
}

