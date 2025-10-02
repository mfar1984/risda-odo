import 'package:hive/hive.dart';

part 'sync_queue_hive_model.g.dart';

@HiveType(typeId: 4)
class SyncQueueHive extends HiveObject {
  @HiveField(0)
  String id; // UUID
  
  @HiveField(1)
  String type; // 'journey', 'claim', 'photo'
  
  @HiveField(2)
  String action; // 'create', 'update', 'delete'
  
  @HiveField(3)
  String localId; // References local data
  
  @HiveField(4)
  int priority; // 1=high (journeys), 2=medium (claims), 3=low (photos)
  
  @HiveField(5)
  String data; // JSON string payload for API
  
  @HiveField(6)
  DateTime createdAt;
  
  @HiveField(7)
  DateTime? lastAttempt;
  
  @HiveField(8)
  int retries;
  
  @HiveField(9)
  String? errorMessage;

  SyncQueueHive({
    required this.id,
    required this.type,
    required this.action,
    required this.localId,
    required this.priority,
    required this.data,
    required this.createdAt,
    this.lastAttempt,
    this.retries = 0,
    this.errorMessage,
  });

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'type': type,
      'action': action,
      'local_id': localId,
      'priority': priority,
      'data': data,
      'created_at': createdAt.toIso8601String(),
      'last_attempt': lastAttempt?.toIso8601String(),
      'retries': retries,
      'error_message': errorMessage,
    };
  }
}

