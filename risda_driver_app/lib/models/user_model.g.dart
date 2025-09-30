// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'user_model.dart';

// **************************************************************************
// TypeAdapterGenerator
// **************************************************************************

class UserAdapter extends TypeAdapter<User> {
  @override
  final int typeId = 0;

  @override
  User read(BinaryReader reader) {
    final numOfFields = reader.readByte();
    final fields = <int, dynamic>{
      for (int i = 0; i < numOfFields; i++) reader.readByte(): reader.read(),
    };
    
    // Baca data tanpa menggunakan cast untuk mengelakkan ralat
    final bahagian = fields[4];
    final stesen = fields[5];
    final staff = fields[6];
    
    return User(
      id: fields[0] as int,
      name: fields[1] as String,
      email: fields[2] as String,
      role: fields[3] as String?,
      bahagian: bahagian, // Terima apa sahaja format (List atau Map)
      stesen: stesen, // Terima apa sahaja format (List atau Map)
      staff: staff, // Terima apa sahaja format (List atau Map)
      createdAt: fields[7] as String?,
      lastLogin: fields[8] as String?,
    );
  }

  @override
  void write(BinaryWriter writer, User obj) {
    writer
      ..writeByte(9)
      ..writeByte(0)
      ..write(obj.id)
      ..writeByte(1)
      ..write(obj.name)
      ..writeByte(2)
      ..write(obj.email)
      ..writeByte(3)
      ..write(obj.role)
      ..writeByte(4)
      ..write(obj.bahagian)
      ..writeByte(5)
      ..write(obj.stesen)
      ..writeByte(6)
      ..write(obj.staff)
      ..writeByte(7)
      ..write(obj.createdAt)
      ..writeByte(8)
      ..write(obj.lastLogin);
  }

  @override
  int get hashCode => typeId.hashCode;

  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      other is UserAdapter &&
          runtimeType == other.runtimeType &&
          typeId == other.typeId;
}
