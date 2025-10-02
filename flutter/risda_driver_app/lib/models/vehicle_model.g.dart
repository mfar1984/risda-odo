// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'vehicle_model.dart';

// **************************************************************************
// TypeAdapterGenerator
// **************************************************************************

class VehicleAdapter extends TypeAdapter<Vehicle> {
  @override
  final int typeId = 3;

  @override
  Vehicle read(BinaryReader reader) {
    final numOfFields = reader.readByte();
    final fields = <int, dynamic>{
      for (int i = 0; i < numOfFields; i++) reader.readByte(): reader.read(),
    };
    return Vehicle(
      id: fields[0] as int,
      noPlat: fields[1] as String,
      jenama: fields[2] as String,
      model: fields[3] as String,
      tahun: fields[4] as int,
      noEnjin: fields[5] as String,
      noCasis: fields[6] as String,
      jenisBahanApi: fields[7] as String,
      kapasitiMuatan: fields[8] as String,
      warna: fields[9] as String,
      odometerSemasa: fields[10] as int,
      cukaiTamatTempoh: fields[11] as DateTime,
      tarikhPendaftaran: fields[12] as DateTime,
      penyelenggaraanSeterusnya: fields[13] as DateTime?,
      status: fields[14] as String,
    );
  }

  @override
  void write(BinaryWriter writer, Vehicle obj) {
    writer
      ..writeByte(15)
      ..writeByte(0)
      ..write(obj.id)
      ..writeByte(1)
      ..write(obj.noPlat)
      ..writeByte(2)
      ..write(obj.jenama)
      ..writeByte(3)
      ..write(obj.model)
      ..writeByte(4)
      ..write(obj.tahun)
      ..writeByte(5)
      ..write(obj.noEnjin)
      ..writeByte(6)
      ..write(obj.noCasis)
      ..writeByte(7)
      ..write(obj.jenisBahanApi)
      ..writeByte(8)
      ..write(obj.kapasitiMuatan)
      ..writeByte(9)
      ..write(obj.warna)
      ..writeByte(10)
      ..write(obj.odometerSemasa)
      ..writeByte(11)
      ..write(obj.cukaiTamatTempoh)
      ..writeByte(12)
      ..write(obj.tarikhPendaftaran)
      ..writeByte(13)
      ..write(obj.penyelenggaraanSeterusnya)
      ..writeByte(14)
      ..write(obj.status);
  }

  @override
  int get hashCode => typeId.hashCode;

  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      other is VehicleAdapter &&
          runtimeType == other.runtimeType &&
          typeId == other.typeId;
}
