import 'package:hive/hive.dart';

part 'claim_hive_model.g.dart';

@HiveType(typeId: 3)
class ClaimHive extends HiveObject {
  @HiveField(0)
  int? id; // Server ID (null if not synced)
  
  @HiveField(1)
  int? logPemanduId; // Links to journey (log_pemandu) - nullable for standalone claims
  
  @HiveField(2)
  String kategori; // 'Tol', 'Parking', 'Food & Beverage', etc.
  
  @HiveField(3)
  double jumlah; // Amount (RM)
  
  @HiveField(4)
  String? resit; // Receipt photo path/URL
  
  @HiveField(5)
  String? catatan; // Notes
  
  @HiveField(6)
  String status; // 'pending', 'diluluskan', 'ditolak', 'dibatalkan'
  
  @HiveField(7)
  int diciptaOleh;
  
  @HiveField(8)
  int? dikemaskiniOleh; // Who last updated
  
  @HiveField(9)
  int? diprosesOleh; // Who approved/rejected/cancelled
  
  @HiveField(10)
  DateTime? tarikhDiproses; // When processed
  
  @HiveField(11)
  String? alasanTolak; // Reason if rejected
  
  @HiveField(12)
  String? alasanGantung; // Reason if cancelled (dibatalkan)
  
  @HiveField(13)
  DateTime? createdAt;
  
  @HiveField(14)
  DateTime? updatedAt;
  
  // Offline fields
  @HiveField(15)
  String localId;
  
  @HiveField(16)
  bool isSynced;
  
  @HiveField(17)
  String? resitLocal; // Local photo path
  
  @HiveField(18)
  int syncRetries;
  
  @HiveField(19)
  String? syncError;
  
  @HiveField(20)
  DateTime? lastSyncAttempt;

  ClaimHive({
    this.id,
    this.logPemanduId,
    required this.kategori,
    required this.jumlah,
    this.resit,
    this.catatan,
    this.status = 'pending',
    required this.diciptaOleh,
    this.dikemaskiniOleh,
    this.diprosesOleh,
    this.tarikhDiproses,
    this.alasanTolak,
    this.alasanGantung,
    this.createdAt,
    this.updatedAt,
    required this.localId,
    this.isSynced = false,
    this.resitLocal,
    this.syncRetries = 0,
    this.syncError,
    this.lastSyncAttempt,
  });

  Map<String, dynamic> toJson() {
    return {
      if (id != null) 'id': id,
      'log_pemandu_id': logPemanduId,
      'kategori': kategori,
      'jumlah': jumlah,
      'resit': resit,
      'keterangan': catatan, // MySQL uses 'keterangan', not 'catatan'
      'status': status,
      'dicipta_oleh': diciptaOleh,
      'dikemaskini_oleh': dikemaskiniOleh,
      'diproses_oleh': diprosesOleh,
      'tarikh_diproses': tarikhDiproses?.toIso8601String(),
      'alasan_tolak': alasanTolak,
      'alasan_gantung': alasanGantung,
    };
  }

  factory ClaimHive.fromJson(Map<String, dynamic> json, {required String localId}) {
    // Handle nested objects (diproses_oleh might be Map)
    int? diprosesOlehId;
    if (json['diproses_oleh'] != null) {
      if (json['diproses_oleh'] is int) {
        diprosesOlehId = json['diproses_oleh'];
      } else if (json['diproses_oleh'] is Map) {
        diprosesOlehId = json['diproses_oleh']['id'];
      }
    }
    
    return ClaimHive(
      id: json['id'],
      logPemanduId: json['log_pemandu_id'],
      kategori: json['kategori'] ?? 'others',
      jumlah: json['jumlah'] != null ? double.tryParse(json['jumlah'].toString()) ?? 0.0 : 0.0,
      resit: json['resit'],
      catatan: json['keterangan'], // MySQL uses 'keterangan'
      status: json['status'] ?? 'pending',
      diciptaOleh: json['dicipta_oleh'] ?? 0,
      dikemaskiniOleh: json['dikemaskini_oleh'],
      diprosesOleh: diprosesOlehId,
      tarikhDiproses: json['tarikh_diproses'] != null ? DateTime.parse(json['tarikh_diproses']) : null,
      alasanTolak: json['alasan_tolak'],
      alasanGantung: json['alasan_gantung'],
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
      localId: localId,
      isSynced: true, // From server
    );
  }
}

