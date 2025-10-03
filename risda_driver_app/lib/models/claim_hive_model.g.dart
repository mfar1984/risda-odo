// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'claim_hive_model.dart';

// **************************************************************************
// TypeAdapterGenerator
// **************************************************************************

class ClaimHiveAdapter extends TypeAdapter<ClaimHive> {
  @override
  final int typeId = 3;

  @override
  ClaimHive read(BinaryReader reader) {
    final numOfFields = reader.readByte();
    final fields = <int, dynamic>{
      for (int i = 0; i < numOfFields; i++) reader.readByte(): reader.read(),
    };
    return ClaimHive(
      id: fields[0] as int?,
      logPemanduId: fields[1] as int?,
      kategori: fields[2] as String,
      jumlah: fields[3] as double,
      resit: fields[4] as String?,
      catatan: fields[5] as String?,
      status: fields[6] as String,
      diciptaOleh: fields[7] as int,
      dikemaskiniOleh: fields[8] as int?,
      diprosesOleh: fields[9] as int?,
      tarikhDiproses: fields[10] as DateTime?,
      alasanTolak: fields[11] as String?,
      alasanGantung: fields[12] as String?,
      createdAt: fields[13] as DateTime?,
      updatedAt: fields[14] as DateTime?,
      localId: fields[15] as String,
      isSynced: fields[16] as bool,
      resitLocal: fields[17] as String?,
      syncRetries: fields[18] as int,
      syncError: fields[19] as String?,
      lastSyncAttempt: fields[20] as DateTime?,
    );
  }

  @override
  void write(BinaryWriter writer, ClaimHive obj) {
    writer
      ..writeByte(21)
      ..writeByte(0)
      ..write(obj.id)
      ..writeByte(1)
      ..write(obj.logPemanduId)
      ..writeByte(2)
      ..write(obj.kategori)
      ..writeByte(3)
      ..write(obj.jumlah)
      ..writeByte(4)
      ..write(obj.resit)
      ..writeByte(5)
      ..write(obj.catatan)
      ..writeByte(6)
      ..write(obj.status)
      ..writeByte(7)
      ..write(obj.diciptaOleh)
      ..writeByte(8)
      ..write(obj.dikemaskiniOleh)
      ..writeByte(9)
      ..write(obj.diprosesOleh)
      ..writeByte(10)
      ..write(obj.tarikhDiproses)
      ..writeByte(11)
      ..write(obj.alasanTolak)
      ..writeByte(12)
      ..write(obj.alasanGantung)
      ..writeByte(13)
      ..write(obj.createdAt)
      ..writeByte(14)
      ..write(obj.updatedAt)
      ..writeByte(15)
      ..write(obj.localId)
      ..writeByte(16)
      ..write(obj.isSynced)
      ..writeByte(17)
      ..write(obj.resitLocal)
      ..writeByte(18)
      ..write(obj.syncRetries)
      ..writeByte(19)
      ..write(obj.syncError)
      ..writeByte(20)
      ..write(obj.lastSyncAttempt);
  }

  @override
  int get hashCode => typeId.hashCode;

  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      other is ClaimHiveAdapter &&
          runtimeType == other.runtimeType &&
          typeId == other.typeId;
}
