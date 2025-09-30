class DriverLog {
  final int? id;
  final int programId;
  final int kenderaanId;
  final int pemanduId;
  final DateTime? checkInTime;
  final DateTime? checkOutTime;
  final String? lokasiCheckIn;
  final String? lokasiCheckOut;
  final double? bacaanOdometerMula;
  final double? bacaanOdometerTamat;
  final double? jarakPerjalanan;
  final String? catatan;
  final String status;
  final String? programNama;
  final String? kenderaanNoPlat;

  DriverLog({
    this.id,
    required this.programId,
    required this.kenderaanId,
    required this.pemanduId,
    this.checkInTime,
    this.checkOutTime,
    this.lokasiCheckIn,
    this.lokasiCheckOut,
    this.bacaanOdometerMula,
    this.bacaanOdometerTamat,
    this.jarakPerjalanan,
    this.catatan,
    required this.status,
    this.programNama,
    this.kenderaanNoPlat,
  });

  factory DriverLog.fromJson(Map<String, dynamic> json) {
    return DriverLog(
      id: json['id'],
      programId: json['program_id'] ?? 0,
      kenderaanId: json['kenderaan_id'] ?? 0,
      pemanduId: json['pemandu_id'] ?? 0,
      checkInTime: json['check_in_time'] != null 
          ? DateTime.tryParse(json['check_in_time']) 
          : null,
      checkOutTime: json['check_out_time'] != null 
          ? DateTime.tryParse(json['check_out_time']) 
          : null,
      lokasiCheckIn: json['lokasi_check_in'],
      lokasiCheckOut: json['lokasi_check_out'],
      bacaanOdometerMula: json['bacaan_odometer_mula'] != null 
          ? double.tryParse(json['bacaan_odometer_mula'].toString()) 
          : null,
      bacaanOdometerTamat: json['bacaan_odometer_tamat'] != null 
          ? double.tryParse(json['bacaan_odometer_tamat'].toString()) 
          : null,
      jarakPerjalanan: json['jarak_perjalanan'] != null 
          ? double.tryParse(json['jarak_perjalanan'].toString()) 
          : null,
      catatan: json['catatan'],
      status: json['status'] ?? 'dalam_perjalanan',
      programNama: json['program_nama'],
      kenderaanNoPlat: json['kenderaan_no_plat'],
    );
  }

  bool get isActive => status == 'dalam_perjalanan' || status == 'aktif';
  bool get isCompleted => status == 'selesai';
}
