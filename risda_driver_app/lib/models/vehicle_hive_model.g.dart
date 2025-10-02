// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'vehicle_hive_model.dart';

// **************************************************************************
// TypeAdapterGenerator
// **************************************************************************

class VehicleHiveAdapter extends TypeAdapter<VehicleHive> {
  @override
  final int typeId = 2;

  @override
  VehicleHive read(BinaryReader reader) {
    final numOfFields = reader.readByte();
    final fields = <int, dynamic>{
      for (int i = 0; i < numOfFields; i++) reader.readByte(): reader.read(),
    };
    return VehicleHive(
      id: fields[0] as int,
      noPendaftaran: fields[1] as String,
      jenisKenderaan: fields[2] as String,
      model: fields[3] as String?,
      tahun: fields[4] as int?,
      warna: fields[5] as String?,
      bacanOdometerSemasaTerkini: fields[6] as int?,
      status: fields[7] as String,
      organisasiId: fields[8] as String?,
      jenisOrganisasi: fields[9] as String,
      diciptaOleh: fields[10] as int,
      dikemaskiniOleh: fields[11] as int?,
      createdAt: fields[12] as DateTime?,
      updatedAt: fields[13] as DateTime?,
      lastSync: fields[14] as DateTime,
    );
  }

  @override
  void write(BinaryWriter writer, VehicleHive obj) {
    writer
      ..writeByte(15)
      ..writeByte(0)
      ..write(obj.id)
      ..writeByte(1)
      ..write(obj.noPendaftaran)
      ..writeByte(2)
      ..write(obj.jenisKenderaan)
      ..writeByte(3)
      ..write(obj.model)
      ..writeByte(4)
      ..write(obj.tahun)
      ..writeByte(5)
      ..write(obj.warna)
      ..writeByte(6)
      ..write(obj.bacanOdometerSemasaTerkini)
      ..writeByte(7)
      ..write(obj.status)
      ..writeByte(8)
      ..write(obj.organisasiId)
      ..writeByte(9)
      ..write(obj.jenisOrganisasi)
      ..writeByte(10)
      ..write(obj.diciptaOleh)
      ..writeByte(11)
      ..write(obj.dikemaskiniOleh)
      ..writeByte(12)
      ..write(obj.createdAt)
      ..writeByte(13)
      ..write(obj.updatedAt)
      ..writeByte(14)
      ..write(obj.lastSync);
  }

  @override
  int get hashCode => typeId.hashCode;

  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      other is VehicleHiveAdapter &&
          runtimeType == other.runtimeType &&
          typeId == other.typeId;
}
