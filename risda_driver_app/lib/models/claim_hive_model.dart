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
  String status; // 'pending', 'approved', 'rejected'
  
  @HiveField(7)
  int diciptaOleh;
  
  @HiveField(8)
  DateTime? createdAt;
  
  @HiveField(9)
  DateTime? updatedAt;
  
  // Offline fields
  @HiveField(10)
  String localId;
  
  @HiveField(11)
  bool isSynced;
  
  @HiveField(12)
  String? resitLocal; // Local photo path
  
  @HiveField(13)
  int syncRetries;
  
  @HiveField(14)
  String? syncError;
  
  @HiveField(15)
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
      'catatan': catatan,
      'status': status,
      'dicipta_oleh': diciptaOleh,
    };
  }

  factory ClaimHive.fromJson(Map<String, dynamic> json, {required String localId}) {
    return ClaimHive(
      id: json['id'],
      logPemanduId: json['log_pemandu_id'],
      kategori: json['kategori'],
      jumlah: (json['jumlah'] as num).toDouble(),
      resit: json['resit'],
      catatan: json['catatan'],
      status: json['status'] ?? 'pending',
      diciptaOleh: json['dicipta_oleh'],
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
      localId: localId,
      isSynced: true, // From server
    );
  }
}

