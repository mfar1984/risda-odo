class Vehicle {
  final int id;
  final String noPlat;
  final String? jenama;
  final String? model;
  final int? tahun;
  final String? jenisBahanApi;
  final String status;

  Vehicle({
    required this.id,
    required this.noPlat,
    this.jenama,
    this.model,
    this.tahun,
    this.jenisBahanApi,
    required this.status,
  });

  factory Vehicle.fromJson(Map<String, dynamic> json) {
    return Vehicle(
      id: json['id'] ?? 0,
      noPlat: json['no_plat'] ?? '',
      jenama: json['jenama'],
      model: json['model'],
      tahun: json['tahun'],
      jenisBahanApi: json['jenis_bahan_api'],
      status: json['status'] ?? 'aktif',
    );
  }

  String get namaPenuh {
    String result = noPlat;
    if (jenama != null) result += ' - $jenama';
    if (model != null) result += ' $model';
    if (tahun != null) result += ' ($tahun)';
    return result;
  }
}
