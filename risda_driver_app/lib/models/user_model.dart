import 'package:hive/hive.dart';

part 'user_model.g.dart';

@HiveType(typeId: 0)
class User {
  @HiveField(0)
  final int id;

  @HiveField(1)
  final String name;

  @HiveField(2)
  final String email;

  @HiveField(3)
  final String? role;

  @HiveField(4)
  final dynamic bahagian; // Boleh menerima List atau Map

  @HiveField(5)
  final dynamic stesen; // Boleh menerima List atau Map

  @HiveField(6)
  final dynamic staff; // Boleh menerima List atau Map

  @HiveField(7)
  final String? createdAt;

  @HiveField(8)
  final String? lastLogin;

  User({
    required this.id,
    required this.name,
    required this.email,
    this.role,
    this.bahagian,
    this.stesen,
    this.staff,
    this.createdAt,
    this.lastLogin,
  });

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'],
      name: json['name'],
      email: json['email'],
      role: json['role'],
      bahagian: json['bahagian'],
      stesen: json['stesen'],
      staff: json['staff'],
      createdAt: json['created_at'],
      lastLogin: json['last_login'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'role': role,
      'bahagian': bahagian,
      'stesen': stesen,
      'staff': staff,
      'created_at': createdAt,
      'last_login': lastLogin,
    };
  }
  
  // Helper getters for profile display
  String get icNumber {
    if (staff is Map && staff['no_staf'] != null) {
      return staff['no_staf'];
    }
    return 'N/A';
  }
  
  String get jawatan {
    if (staff is Map && staff['jawatan'] != null) {
      return staff['jawatan'];
    }
    return role ?? 'N/A';
  }
  
  String get bahagianName {
    if (bahagian is List && bahagian.isNotEmpty && bahagian[0] is Map) {
      return bahagian[0]['nama'] ?? 'N/A';
    } else if (bahagian is Map) {
      // Try to extract first value from map
      try {
        var firstKey = bahagian.keys.first;
        var firstValue = bahagian[firstKey];
        if (firstValue is Map && firstValue['nama'] != null) {
          return firstValue['nama'];
        }
      } catch (e) {
        // Ignore errors
      }
    }
    return 'N/A';
  }
  
  String get stesenName {
    if (stesen is List && stesen.isNotEmpty && stesen[0] is Map) {
      return stesen[0]['nama'] ?? 'N/A';
    } else if (stesen is Map) {
      // Try to extract first value from map
      try {
        var firstKey = stesen.keys.first;
        var firstValue = stesen[firstKey];
        if (firstValue is Map && firstValue['nama'] != null) {
          return firstValue['nama'];
        }
      } catch (e) {
        // Ignore errors
      }
    }
    return 'N/A';
  }
} 