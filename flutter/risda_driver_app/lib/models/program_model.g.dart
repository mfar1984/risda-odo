// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'program_model.dart';

// **************************************************************************
// TypeAdapterGenerator
// **************************************************************************

class ProgramAdapter extends TypeAdapter<Program> {
  @override
  final int typeId = 2;

  @override
  Program read(BinaryReader reader) {
    final numOfFields = reader.readByte();
    final fields = <int, dynamic>{
      for (int i = 0; i < numOfFields; i++) reader.readByte(): reader.read(),
    };
    return Program(
      id: fields[0] as int,
      namaProgram: fields[1] as String,
      penerangan: fields[2] as String?,
      lokasiProgram: fields[3] as String?,
      tarikhMula: fields[4] as DateTime,
      tarikhSelesai: fields[5] as DateTime,
      anggaranKm: fields[6] as double,
      status: fields[7] as String,
      permohonanDariStaffId: fields[8] as int,
      createdAt: fields[9] as String?,
      updatedAt: fields[10] as String?,
    );
  }

  @override
  void write(BinaryWriter writer, Program obj) {
    writer
      ..writeByte(11)
      ..writeByte(0)
      ..write(obj.id)
      ..writeByte(1)
      ..write(obj.namaProgram)
      ..writeByte(2)
      ..write(obj.penerangan)
      ..writeByte(3)
      ..write(obj.lokasiProgram)
      ..writeByte(4)
      ..write(obj.tarikhMula)
      ..writeByte(5)
      ..write(obj.tarikhSelesai)
      ..writeByte(6)
      ..write(obj.anggaranKm)
      ..writeByte(7)
      ..write(obj.status)
      ..writeByte(8)
      ..write(obj.permohonanDariStaffId)
      ..writeByte(9)
      ..write(obj.createdAt)
      ..writeByte(10)
      ..write(obj.updatedAt);
  }

  @override
  int get hashCode => typeId.hashCode;

  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      other is ProgramAdapter &&
          runtimeType == other.runtimeType &&
          typeId == other.typeId;
}
