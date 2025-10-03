// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'journey_hive_model.dart';

// **************************************************************************
// TypeAdapterGenerator
// **************************************************************************

class JourneyHiveAdapter extends TypeAdapter<JourneyHive> {
  @override
  final int typeId = 0;

  @override
  JourneyHive read(BinaryReader reader) {
    final numOfFields = reader.readByte();
    final fields = <int, dynamic>{
      for (int i = 0; i < numOfFields; i++) reader.readByte(): reader.read(),
    };
    return JourneyHive(
      id: fields[0] as int?,
      pemanduId: fields[1] as int,
      kenderaanId: fields[2] as int,
      programId: fields[3] as int?,
      tarikhPerjalanan: fields[4] as DateTime,
      masaKeluar: fields[5] as String,
      masaMasuk: fields[6] as String?,
      destinasi: fields[7] as String,
      catatan: fields[8] as String?,
      odometerKeluar: fields[9] as int,
      odometerMasuk: fields[10] as int?,
      jarak: fields[11] as int?,
      literMinyak: fields[12] as double?,
      kosMinyak: fields[13] as double?,
      stesenMinyak: fields[14] as String?,
      resitMinyak: fields[15] as String?,
      fotoOdometerKeluar: fields[16] as String?,
      fotoOdometerMasuk: fields[17] as String?,
      status: fields[18] as String,
      jenisOrganisasi: fields[19] as String?,
      organisasiId: fields[20] as String?,
      diciptaOleh: fields[21] as int,
      dikemaskiniOleh: fields[22] as int?,
      lokasiCheckinLat: fields[23] as double?,
      lokasiCheckinLong: fields[24] as double?,
      lokasiCheckoutLat: fields[25] as double?,
      lokasiCheckoutLong: fields[26] as double?,
      createdAt: fields[27] as DateTime?,
      updatedAt: fields[28] as DateTime?,
      localId: fields[29] as String,
      isSynced: fields[30] as bool,
      lastSyncAttempt: fields[31] as DateTime?,
      syncRetries: fields[32] as int,
      syncError: fields[33] as String?,
      fotoOdometerKeluarLocal: fields[34] as String?,
      fotoOdometerMasukLocal: fields[35] as String?,
      resitMinyakLocal: fields[36] as String?,
    );
  }

  @override
  void write(BinaryWriter writer, JourneyHive obj) {
    writer
      ..writeByte(37)
      ..writeByte(0)
      ..write(obj.id)
      ..writeByte(1)
      ..write(obj.pemanduId)
      ..writeByte(2)
      ..write(obj.kenderaanId)
      ..writeByte(3)
      ..write(obj.programId)
      ..writeByte(4)
      ..write(obj.tarikhPerjalanan)
      ..writeByte(5)
      ..write(obj.masaKeluar)
      ..writeByte(6)
      ..write(obj.masaMasuk)
      ..writeByte(7)
      ..write(obj.destinasi)
      ..writeByte(8)
      ..write(obj.catatan)
      ..writeByte(9)
      ..write(obj.odometerKeluar)
      ..writeByte(10)
      ..write(obj.odometerMasuk)
      ..writeByte(11)
      ..write(obj.jarak)
      ..writeByte(12)
      ..write(obj.literMinyak)
      ..writeByte(13)
      ..write(obj.kosMinyak)
      ..writeByte(14)
      ..write(obj.stesenMinyak)
      ..writeByte(15)
      ..write(obj.resitMinyak)
      ..writeByte(16)
      ..write(obj.fotoOdometerKeluar)
      ..writeByte(17)
      ..write(obj.fotoOdometerMasuk)
      ..writeByte(18)
      ..write(obj.status)
      ..writeByte(19)
      ..write(obj.jenisOrganisasi)
      ..writeByte(20)
      ..write(obj.organisasiId)
      ..writeByte(21)
      ..write(obj.diciptaOleh)
      ..writeByte(22)
      ..write(obj.dikemaskiniOleh)
      ..writeByte(23)
      ..write(obj.lokasiCheckinLat)
      ..writeByte(24)
      ..write(obj.lokasiCheckinLong)
      ..writeByte(25)
      ..write(obj.lokasiCheckoutLat)
      ..writeByte(26)
      ..write(obj.lokasiCheckoutLong)
      ..writeByte(27)
      ..write(obj.createdAt)
      ..writeByte(28)
      ..write(obj.updatedAt)
      ..writeByte(29)
      ..write(obj.localId)
      ..writeByte(30)
      ..write(obj.isSynced)
      ..writeByte(31)
      ..write(obj.lastSyncAttempt)
      ..writeByte(32)
      ..write(obj.syncRetries)
      ..writeByte(33)
      ..write(obj.syncError)
      ..writeByte(34)
      ..write(obj.fotoOdometerKeluarLocal)
      ..writeByte(35)
      ..write(obj.fotoOdometerMasukLocal)
      ..writeByte(36)
      ..write(obj.resitMinyakLocal);
  }

  @override
  int get hashCode => typeId.hashCode;

  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      other is JourneyHiveAdapter &&
          runtimeType == other.runtimeType &&
          typeId == other.typeId;
}
