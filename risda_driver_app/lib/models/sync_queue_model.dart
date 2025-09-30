import 'package:hive/hive.dart';

part 'sync_queue_model.g.dart';

@HiveType(typeId: 5)
class SyncQueueItem {
  @HiveField(0)
  final String id;

  @HiveField(1)
  final String endpoint;

  @HiveField(2)
  final String method;

  @HiveField(3)
  final Map<String, dynamic> data;

  @HiveField(4)
  final DateTime createdAt;

  @HiveField(5)
  int retryCount;

  @HiveField(6)
  bool isProcessing;

  @HiveField(7)
  String? errorMessage;

  SyncQueueItem({
    required this.id,
    required this.endpoint,
    required this.method,
    required this.data,
    required this.createdAt,
    this.retryCount = 0,
    this.isProcessing = false,
    this.errorMessage,
  });

  factory SyncQueueItem.create({
    required String endpoint,
    required String method,
    required Map<String, dynamic> data,
  }) {
    return SyncQueueItem(
      id: DateTime.now().millisecondsSinceEpoch.toString(),
      endpoint: endpoint,
      method: method,
      data: data,
      createdAt: DateTime.now(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'endpoint': endpoint,
      'method': method,
      'data': data,
      'created_at': createdAt.toIso8601String(),
      'retry_count': retryCount,
      'is_processing': isProcessing,
      'error_message': errorMessage,
    };
  }
} 