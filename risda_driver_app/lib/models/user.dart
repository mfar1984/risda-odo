class User {
  final int id;
  final String name;
  final String email;
  final String? phone;
  final String? staffId;
  final String? stationName;
  final String? divisionName;
  final String? jenisOrganisasi;
  final int? organisasiId;
  final DateTime? createdAt;

  User({
    required this.id,
    required this.name,
    required this.email,
    this.phone,
    this.staffId,
    this.stationName,
    this.divisionName,
    this.jenisOrganisasi,
    this.organisasiId,
    this.createdAt,
  });

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      email: json['email'] ?? '',
      phone: json['phone'],
      staffId: json['staff_id'],
      stationName: json['station_name'],
      divisionName: json['division_name'],
      jenisOrganisasi: json['jenis_organisasi'],
      organisasiId: json['organisasi_id'],
      createdAt: json['created_at'] != null 
          ? DateTime.tryParse(json['created_at']) 
          : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'phone': phone,
      'staff_id': staffId,
      'station_name': stationName,
      'division_name': divisionName,
      'jenis_organisasi': jenisOrganisasi,
      'organisasi_id': organisasiId,
      'created_at': createdAt?.toIso8601String(),
    };
  }
}
