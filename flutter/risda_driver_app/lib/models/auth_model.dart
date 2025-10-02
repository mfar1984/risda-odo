import 'package:hive/hive.dart';

part 'auth_model.g.dart';

@HiveType(typeId: 1)
class Auth {
  @HiveField(0)
  final String token;

  @HiveField(1)
  final String tokenType;

  @HiveField(2)
  final int expiresIn;

  @HiveField(3)
  final DateTime createdAt;

  @HiveField(4)
  bool isLoggedIn;

  Auth({
    required this.token,
    required this.tokenType,
    required this.expiresIn,
    required this.createdAt,
    this.isLoggedIn = true,
  });

  factory Auth.fromJson(Map<String, dynamic> json) {
    return Auth(
      token: json['token'],
      tokenType: json['token_type'],
      expiresIn: int.parse(json['expires_in'].toString()),
      createdAt: DateTime.now(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'token': token,
      'token_type': tokenType,
      'expires_in': expiresIn,
      'created_at': createdAt.toIso8601String(),
      'is_logged_in': isLoggedIn,
    };
  }

  bool get isExpired {
    final expiryDate = createdAt.add(Duration(seconds: expiresIn));
    final now = DateTime.now();
    final isExpired = now.isAfter(expiryDate);
    
    print('DEBUG: Token expiry check:');
    print('DEBUG: Created: $createdAt');
    print('DEBUG: Expires in: $expiresIn seconds');
    print('DEBUG: Expiry date: $expiryDate');
    print('DEBUG: Current time: $now');
    print('DEBUG: Is expired: $isExpired');
    
    return isExpired;
  }

  String get authorizationHeader => '$tokenType $token';
} 