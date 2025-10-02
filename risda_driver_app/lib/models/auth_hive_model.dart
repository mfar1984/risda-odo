import 'package:hive/hive.dart';

part 'auth_hive_model.g.dart';

@HiveType(typeId: 5)
class AuthHive extends HiveObject {
  @HiveField(0)
  String token; // API token
  
  @HiveField(1)
  int userId;
  
  @HiveField(2)
  String name;
  
  @HiveField(3)
  String email;
  
  @HiveField(4)
  String? jenisOrganisasi; // 'semua', 'bahagian', 'stesen'
  
  @HiveField(5)
  String? organisasiId;
  
  @HiveField(6)
  String? organisasiName;
  
  @HiveField(7)
  String role; // 'driver', 'admin', etc.
  
  @HiveField(8)
  DateTime loginAt; // When user logged in
  
  @HiveField(9)
  DateTime? lastSync; // Last time synced with server
  
  @HiveField(10)
  bool rememberMe; // User preference

  AuthHive({
    required this.token,
    required this.userId,
    required this.name,
    required this.email,
    this.jenisOrganisasi,
    this.organisasiId,
    this.organisasiName,
    required this.role,
    required this.loginAt,
    this.lastSync,
    this.rememberMe = false,
  });

  factory AuthHive.fromJson(Map<String, dynamic> json) {
    return AuthHive(
      token: json['token'] ?? '',
      userId: json['user']['id'] ?? 0,
      name: json['user']['name'] ?? '',
      email: json['user']['email'] ?? '',
      jenisOrganisasi: json['user']['jenis_organisasi'],
      organisasiId: json['user']['organisasi_id'],
      organisasiName: json['user']['organisasi_name'],
      role: json['user']['role'] ?? 'driver',
      loginAt: DateTime.now(),
      lastSync: DateTime.now(),
      rememberMe: json['remember_me'] ?? false,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'token': token,
      'user': {
        'id': userId,
        'name': name,
        'email': email,
        'jenis_organisasi': jenisOrganisasi,
        'organisasi_id': organisasiId,
        'organisasi_name': organisasiName,
        'role': role,
      },
      'login_at': loginAt.toIso8601String(),
      'last_sync': lastSync?.toIso8601String(),
      'remember_me': rememberMe,
    };
  }
}

