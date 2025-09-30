// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'driver_log_model.dart';

// **************************************************************************
// TypeAdapterGenerator
// **************************************************************************

class DriverLogAdapter extends TypeAdapter<DriverLog> {
  @override
  final int typeId = 4;

  @override
  DriverLog read(BinaryReader reader) {
    final numOfFields = reader.readByte();
    final fields = <int, dynamic>{
      for (int i = 0; i < numOfFields; i++) reader.readByte(): reader.read(),
    };
    return DriverLog(
      id: fields[0] as int?,
      programId: fields[1] as int,
      pemanduId: fields[2] as int,
      kenderaanId: fields[3] as int,
      namaLog: fields[4] as String?,
      checkinTime: fields[5] as DateTime,
      checkoutTime: fields[6] as DateTime?,
      jarakPerjalanan: fields[7] as int?,
      bacaanOdometer: fields[8] as int?,
      odometerPhoto: fields[9] as String?,
      lokasiCheckin: fields[10] as String,
      gpsLatitude: fields[11] as double?,
      gpsLongitude: fields[12] as double?,
      lokasiCheckout: fields[13] as String?,
      catatan: fields[14] as String?,
      status: fields[15] as String,
      createdBy: fields[16] as int,
      isSynced: fields[17] as bool,
      createdAt: fields[18] as DateTime,
      updatedAt: fields[19] as DateTime?,
      bacaanOdometerCheckout: fields[20] as int?,
      odometerPhotoCheckout: fields[21] as String?,
      programNama: fields[22] as String?,
      programLokasi: fields[23] as String?,
      kenderaanNoPlat: fields[24] as String?,
      kenderaanJenama: fields[25] as String?,
      kenderaanModel: fields[26] as String?,
    );
  }

  @override
  void write(BinaryWriter writer, DriverLog obj) {
    writer
      ..writeByte(27)
      ..writeByte(0)
      ..write(obj.id)
      ..writeByte(1)
      ..write(obj.programId)
      ..writeByte(2)
      ..write(obj.pemanduId)
      ..writeByte(3)
      ..write(obj.kenderaanId)
      ..writeByte(4)
      ..write(obj.namaLog)
      ..writeByte(5)
      ..write(obj.checkinTime)
      ..writeByte(6)
      ..write(obj.checkoutTime)
      ..writeByte(7)
      ..write(obj.jarakPerjalanan)
      ..writeByte(8)
      ..write(obj.bacaanOdometer)
      ..writeByte(9)
      ..write(obj.odometerPhoto)
      ..writeByte(10)
      ..write(obj.lokasiCheckin)
      ..writeByte(11)
      ..write(obj.gpsLatitude)
      ..writeByte(12)
      ..write(obj.gpsLongitude)
      ..writeByte(13)
      ..write(obj.lokasiCheckout)
      ..writeByte(14)
      ..write(obj.catatan)
      ..writeByte(15)
      ..write(obj.status)
      ..writeByte(16)
      ..write(obj.createdBy)
      ..writeByte(17)
      ..write(obj.isSynced)
      ..writeByte(18)
      ..write(obj.createdAt)
      ..writeByte(19)
      ..write(obj.updatedAt)
      ..writeByte(20)
      ..write(obj.bacaanOdometerCheckout)
      ..writeByte(21)
      ..write(obj.odometerPhotoCheckout)
      ..writeByte(22)
      ..write(obj.programNama)
      ..writeByte(23)
      ..write(obj.programLokasi)
      ..writeByte(24)
      ..write(obj.kenderaanNoPlat)
      ..writeByte(25)
      ..write(obj.kenderaanJenama)
      ..writeByte(26)
      ..write(obj.kenderaanModel);
  }

  @override
  int get hashCode => typeId.hashCode;

  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      other is DriverLogAdapter &&
          runtimeType == other.runtimeType &&
          typeId == other.typeId;
}
