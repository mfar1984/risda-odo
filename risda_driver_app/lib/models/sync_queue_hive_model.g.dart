// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'sync_queue_hive_model.dart';

// **************************************************************************
// TypeAdapterGenerator
// **************************************************************************

class SyncQueueHiveAdapter extends TypeAdapter<SyncQueueHive> {
  @override
  final int typeId = 4;

  @override
  SyncQueueHive read(BinaryReader reader) {
    final numOfFields = reader.readByte();
    final fields = <int, dynamic>{
      for (int i = 0; i < numOfFields; i++) reader.readByte(): reader.read(),
    };
    return SyncQueueHive(
      id: fields[0] as String,
      type: fields[1] as String,
      action: fields[2] as String,
      localId: fields[3] as String,
      priority: fields[4] as int,
      data: fields[5] as String,
      createdAt: fields[6] as DateTime,
      lastAttempt: fields[7] as DateTime?,
      retries: fields[8] as int,
      errorMessage: fields[9] as String?,
    );
  }

  @override
  void write(BinaryWriter writer, SyncQueueHive obj) {
    writer
      ..writeByte(10)
      ..writeByte(0)
      ..write(obj.id)
      ..writeByte(1)
      ..write(obj.type)
      ..writeByte(2)
      ..write(obj.action)
      ..writeByte(3)
      ..write(obj.localId)
      ..writeByte(4)
      ..write(obj.priority)
      ..writeByte(5)
      ..write(obj.data)
      ..writeByte(6)
      ..write(obj.createdAt)
      ..writeByte(7)
      ..write(obj.lastAttempt)
      ..writeByte(8)
      ..write(obj.retries)
      ..writeByte(9)
      ..write(obj.errorMessage);
  }

  @override
  int get hashCode => typeId.hashCode;

  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      other is SyncQueueHiveAdapter &&
          runtimeType == other.runtimeType &&
          typeId == other.typeId;
}
