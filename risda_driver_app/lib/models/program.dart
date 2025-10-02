class Program {
  final int id;
  final String namaProgram;
  final String? lokasiProgram;
  final String? penerangan;
  final DateTime? tarikhMula;
  final DateTime? tarikhSelesai;
  final DateTime? tarikhKelulusan;
  final DateTime? tarikhMulaAktif;
  final DateTime? tarikhSebenarSelesai;
  final String status;
  final String? pemohonNama;
  final String? pemanduNama;
  final String? kenderaanNoPlat;

  Program({
    required this.id,
    required this.namaProgram,
    this.lokasiProgram,
    this.penerangan,
    this.tarikhMula,
    this.tarikhSelesai,
    this.tarikhKelulusan,
    this.tarikhMulaAktif,
    this.tarikhSebenarSelesai,
    required this.status,
    this.pemohonNama,
    this.pemanduNama,
    this.kenderaanNoPlat,
  });

  factory Program.fromJson(Map<String, dynamic> json) {
    return Program(
      id: json['id'] ?? 0,
      namaProgram: json['nama_program'] ?? '',
      lokasiProgram: json['lokasi_program'],
      penerangan: json['penerangan'],
      tarikhMula: json['tarikh_mula'] != null 
          ? DateTime.tryParse(json['tarikh_mula']) 
          : null,
      tarikhSelesai: json['tarikh_selesai'] != null 
          ? DateTime.tryParse(json['tarikh_selesai']) 
          : null,
      tarikhKelulusan: json['tarikh_kelulusan'] != null 
          ? DateTime.tryParse(json['tarikh_kelulusan']) 
          : null,
      tarikhMulaAktif: json['tarikh_mula_aktif'] != null 
          ? DateTime.tryParse(json['tarikh_mula_aktif']) 
          : null,
      tarikhSebenarSelesai: json['tarikh_sebenar_selesai'] != null 
          ? DateTime.tryParse(json['tarikh_sebenar_selesai']) 
          : null,
      status: json['status'] ?? 'draf',
      pemohonNama: json['pemohon_nama'],
      pemanduNama: json['pemandu_nama'],
      kenderaanNoPlat: json['kenderaan_no_plat'],
    );
  }

  bool get isActive => status == 'aktif' || status == 'lulus';
}
