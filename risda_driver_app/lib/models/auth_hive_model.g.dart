// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'auth_hive_model.dart';

// **************************************************************************
// TypeAdapterGenerator
// **************************************************************************

class AuthHiveAdapter extends TypeAdapter<AuthHive> {
  @override
  final int typeId = 5;

  @override
  AuthHive read(BinaryReader reader) {
    final numOfFields = reader.readByte();
    final fields = <int, dynamic>{
      for (int i = 0; i < numOfFields; i++) reader.readByte(): reader.read(),
    };
    return AuthHive(
      token: fields[0] as String,
      userId: fields[1] as int,
      name: fields[2] as String,
      email: fields[3] as String,
      jenisOrganisasi: fields[4] as String?,
      organisasiId: fields[5] as String?,
      organisasiName: fields[6] as String?,
      role: fields[7] as String,
      loginAt: fields[8] as DateTime,
      lastSync: fields[9] as DateTime?,
      rememberMe: fields[10] as bool,
    );
  }

  @override
  void write(BinaryWriter writer, AuthHive obj) {
    writer
      ..writeByte(11)
      ..writeByte(0)
      ..write(obj.token)
      ..writeByte(1)
      ..write(obj.userId)
      ..writeByte(2)
      ..write(obj.name)
      ..writeByte(3)
      ..write(obj.email)
      ..writeByte(4)
      ..write(obj.jenisOrganisasi)
      ..writeByte(5)
      ..write(obj.organisasiId)
      ..writeByte(6)
      ..write(obj.organisasiName)
      ..writeByte(7)
      ..write(obj.role)
      ..writeByte(8)
      ..write(obj.loginAt)
      ..writeByte(9)
      ..write(obj.lastSync)
      ..writeByte(10)
      ..write(obj.rememberMe);
  }

  @override
  int get hashCode => typeId.hashCode;

  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      other is AuthHiveAdapter &&
          runtimeType == other.runtimeType &&
          typeId == other.typeId;
}
