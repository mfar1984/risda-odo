// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'program_hive_model.dart';

// **************************************************************************
// TypeAdapterGenerator
// **************************************************************************

class ProgramHiveAdapter extends TypeAdapter<ProgramHive> {
  @override
  final int typeId = 1;

  @override
  ProgramHive read(BinaryReader reader) {
    final numOfFields = reader.readByte();
    final fields = <int, dynamic>{
      for (int i = 0; i < numOfFields; i++) reader.readByte(): reader.read(),
    };
    return ProgramHive(
      id: fields[0] as int,
      namaProgram: fields[1] as String,
      kenderaanId: fields[2] as String?,
      pemanduId: fields[3] as String?,
      tarikhMula: fields[4] as DateTime?,
      tarikhTamat: fields[5] as DateTime?,
      lokasi: fields[6] as String?,
      lokasiLat: fields[7] as double?,
      lokasiLong: fields[8] as double?,
      peneranganProgram: fields[9] as String?,
      jarakAnggaran: fields[10] as int?,
      catatanTambahan: fields[11] as String?,
      status: fields[12] as String,
      organisasiId: fields[13] as String?,
      diciptaOleh: fields[14] as int,
      dikemaskiniOleh: fields[15] as int?,
      createdAt: fields[16] as DateTime?,
      updatedAt: fields[17] as DateTime?,
      lastSync: fields[18] as DateTime,
    );
  }

  @override
  void write(BinaryWriter writer, ProgramHive obj) {
    writer
      ..writeByte(19)
      ..writeByte(0)
      ..write(obj.id)
      ..writeByte(1)
      ..write(obj.namaProgram)
      ..writeByte(2)
      ..write(obj.kenderaanId)
      ..writeByte(3)
      ..write(obj.pemanduId)
      ..writeByte(4)
      ..write(obj.tarikhMula)
      ..writeByte(5)
      ..write(obj.tarikhTamat)
      ..writeByte(6)
      ..write(obj.lokasi)
      ..writeByte(7)
      ..write(obj.lokasiLat)
      ..writeByte(8)
      ..write(obj.lokasiLong)
      ..writeByte(9)
      ..write(obj.peneranganProgram)
      ..writeByte(10)
      ..write(obj.jarakAnggaran)
      ..writeByte(11)
      ..write(obj.catatanTambahan)
      ..writeByte(12)
      ..write(obj.status)
      ..writeByte(13)
      ..write(obj.organisasiId)
      ..writeByte(14)
      ..write(obj.diciptaOleh)
      ..writeByte(15)
      ..write(obj.dikemaskiniOleh)
      ..writeByte(16)
      ..write(obj.createdAt)
      ..writeByte(17)
      ..write(obj.updatedAt)
      ..writeByte(18)
      ..write(obj.lastSync);
  }

  @override
  int get hashCode => typeId.hashCode;

  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      other is ProgramHiveAdapter &&
          runtimeType == other.runtimeType &&
          typeId == other.typeId;
}
