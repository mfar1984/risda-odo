<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mockup: Penggunaan Kenderaan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Mockup: Penggunaan Kenderaan</h1>
        
        <!-- Filter Panel (sama seperti dashboard) -->
        <div class="bg-white border border-gray-300 rounded-sm p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" style="font-size: 12px;">Jenis Laporan</label>
                    <select class="form-select w-full border border-gray-300 rounded-sm px-3 py-1.5" style="font-size: 12px;">
                        <option>Kerja Lebih Masa</option>
                        <option>Kenderaan</option>
                        <option selected>Penggunaan Kenderaan</option>
                        <option>Tuntutan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" style="font-size: 12px;">Bulan/Tahun</label>
                    <input type="month" class="form-input w-full border border-gray-300 rounded-sm px-3 py-1.5" style="font-size: 12px;" value="2025-09">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" style="font-size: 12px;">Jenis Kenderaan</label>
                    <select class="form-select w-full border border-gray-300 rounded-sm px-3 py-1.5" style="font-size: 12px;">
                        <option>- Semua Jenis -</option>
                        <option>Kereta</option>
                        <option>Van</option>
                        <option>Lori</option>
                        <option>MPV</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" style="font-size: 12px;">No. Pendaftaran</label>
                    <select class="form-select w-full border border-gray-300 rounded-sm px-3 py-1.5" style="font-size: 12px;">
                        <option>- Pilih Kenderaan -</option>
                        <option>QSR43 - Toyota Alphard</option>
                        <option>QKJ261 - Toyota Hiace</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" style="font-size: 12px;">Bahagian/Unit</label>
                    <select class="form-select w-full border border-gray-300 rounded-sm px-3 py-1.5" style="font-size: 12px;">
                        <option>- Semua Bahagian -</option>
                        <option>Bahagian Pembangunan Sibu</option>
                        <option>Bahagian Pengurusan Kuching</option>
                    </select>
                </div>
            </div>
            <div class="mt-4 text-right">
                <button type="button" class="px-6 h-8 bg-green-600 text-white rounded-sm hover:bg-green-700" style="font-size: 12px;">Generate</button>
            </div>
        </div>

        <!-- Result Area -->
        <div class="border border-gray-300 rounded-sm bg-gray-50 p-4">
            <!-- Header Info -->
            <div class="mb-4 space-y-2 text-gray-800" style="font-size: 12px;">
                <h2 class="text-lg font-bold text-gray-900 mb-3">BUTIR-BUTIR PENGGUNAAN KENDERAAN</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="flex items-baseline">
                        <span class="font-semibold w-40">Bulan/Tahun</span>
                        <span class="w-3 text-center">:</span>
                        <span class="text-gray-700">September 2025</span>
                    </div>
                    <div class="flex items-baseline">
                        <span class="font-semibold w-40">Jenis Kenderaan</span>
                        <span class="w-3 text-center">:</span>
                        <span class="text-gray-700">MPV</span>
                    </div>
                    <div class="flex items-baseline">
                        <span class="font-semibold w-40">No. Pendaftaran</span>
                        <span class="w-3 text-center">:</span>
                        <span class="text-gray-700">QSR43</span>
                    </div>
                </div>
                <div class="flex items-baseline">
                    <span class="font-semibold w-40">Bahagian/Unit</span>
                    <span class="w-3 text-center">:</span>
                    <span class="text-gray-700">Bahagian Pembangunan Sibu</span>
                </div>
            </div>

            <!-- Main Table -->
            <div class="w-full overflow-x-auto mb-4">
                <table class="min-w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th rowspan="2" class="border border-gray-300 px-2 py-2 text-left text-gray-800" style="font-size: 11px; min-width: 90px;">Tarikh</th>
                            <th colspan="2" class="border border-gray-300 px-2 py-2 text-center text-gray-800" style="font-size: 11px;">Masa</th>
                            <th rowspan="2" class="border border-gray-300 px-2 py-2 text-left text-gray-800" style="font-size: 11px; min-width: 120px;">Nama Pemandu</th>
                            <th colspan="2" class="border border-gray-300 px-2 py-2 text-center text-gray-800" style="font-size: 11px;">Tujuan & Destinasi (dari — ke)</th>
                            <th colspan="2" class="border border-gray-300 px-2 py-2 text-center text-gray-800" style="font-size: 11px;">Nama Tandatangan</th>
                            <th rowspan="2" class="border border-gray-300 px-2 py-2 text-right text-gray-800" style="font-size: 11px; min-width: 80px;">Bacaan Odometer (KM)</th>
                            <th rowspan="2" class="border border-gray-300 px-2 py-2 text-right text-gray-800" style="font-size: 11px; min-width: 90px;">Jarak Perjalanan / Trip Meter (KM)</th>
                            <th colspan="2" class="border border-gray-300 px-2 py-2 text-center text-gray-800" style="font-size: 11px;">Pembelian Bahan Api (Petrol/Diesel/Gas)</th>
                            <th rowspan="2" class="border border-gray-300 px-2 py-2 text-left text-gray-800" style="font-size: 11px; min-width: 100px;">Arahan Khas Pengguna Kenderaan</th>
                        </tr>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-300 px-2 py-1 text-center text-gray-800" style="font-size: 10px; min-width: 60px;">Mulai</th>
                            <th class="border border-gray-300 px-2 py-1 text-center text-gray-800" style="font-size: 10px; min-width: 60px;">Hingga</th>
                            <th class="border border-gray-300 px-2 py-1 text-left text-gray-800" style="font-size: 10px; min-width: 140px;">Pelulus</th>
                            <th class="border border-gray-300 px-2 py-1 text-left text-gray-800" style="font-size: 10px; min-width: 140px;">Pengguna</th>
                            <th class="border border-gray-300 px-2 py-1 text-right text-gray-800" style="font-size: 10px; min-width: 90px;">No. Resit Pembelian & RM</th>
                            <th class="border border-gray-300 px-2 py-1 text-right text-gray-800" style="font-size: 10px; min-width: 50px;">Liter</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Sample Row 1 -->
                        <tr class="odd:bg-white even:bg-gray-50">
                            <td class="border border-gray-300 px-2 py-2" style="font-size: 11px;">1 September 2025</td>
                            <td class="border border-gray-300 px-2 py-2 text-center" style="font-size: 11px;">7:05 AM</td>
                            <td class="border border-gray-300 px-2 py-2 text-center" style="font-size: 11px;">7:18 AM</td>
                            <td class="border border-gray-300 px-2 py-2" style="font-size: 11px;">Fairiz Bin Rahman</td>
                            <td class="border border-gray-300 px-2 py-2" style="font-size: 11px;">Pejabat RISDA Sibu</td>
                            <td class="border border-gray-300 px-2 py-2" style="font-size: 11px;">Dewan Suarah Sibu</td>
                            <td class="border border-gray-300 px-2 py-2" style="font-size: 11px;">Ahmad Bin Ali</td>
                            <td class="border border-gray-300 px-2 py-2" style="font-size: 11px;">Fairiz Bin Rahman</td>
                            <td class="border border-gray-300 px-2 py-2 text-right" style="font-size: 11px;">12,445</td>
                            <td class="border border-gray-300 px-2 py-2 text-right" style="font-size: 11px;">100.0</td>
                            <td class="border border-gray-300 px-2 py-2 text-right" style="font-size: 11px;">R123456 - RM120.50</td>
                            <td class="border border-gray-300 px-2 py-2 text-right" style="font-size: 11px;">45.50</td>
                            <td class="border border-gray-300 px-2 py-2" style="font-size: 11px;">-</td>
                        </tr>
                        <!-- Sample Row 2 -->
                        <tr class="odd:bg-white even:bg-gray-50">
                            <td class="border border-gray-300 px-2 py-2" style="font-size: 11px;">3 September 2025</td>
                            <td class="border border-gray-300 px-2 py-2 text-center" style="font-size: 11px;">1:48 PM</td>
                            <td class="border border-gray-300 px-2 py-2 text-center" style="font-size: 11px;">1:49 PM</td>
                            <td class="border border-gray-300 px-2 py-2" style="font-size: 11px;">Fairiz Bin Rahman</td>
                            <td class="border border-gray-300 px-2 py-2" style="font-size: 11px;">Pejabat RISDA Sibu</td>
                            <td class="border border-gray-300 px-2 py-2" style="font-size: 11px;">Program Jalinan Kasih</td>
                            <td class="border border-gray-300 px-2 py-2" style="font-size: 11px;">-</td>
                            <td class="border border-gray-300 px-2 py-2" style="font-size: 11px;">Fairiz Bin Rahman</td>
                            <td class="border border-gray-300 px-2 py-2 text-right" style="font-size: 11px;">12,545</td>
                            <td class="border border-gray-300 px-2 py-2 text-right" style="font-size: 11px;">1.0</td>
                            <td class="border border-gray-300 px-2 py-2 text-right" style="font-size: 11px;">-</td>
                            <td class="border border-gray-300 px-2 py-2 text-right" style="font-size: 11px;">-</td>
                            <td class="border border-gray-300 px-2 py-2" style="font-size: 11px;">Perjalanan kecemasan</td>
                        </tr>
                        <!-- Sample Row 3 -->
                        <tr class="odd:bg-white even:bg-gray-50">
                            <td class="border border-gray-300 px-2 py-2" style="font-size: 11px;">5 September 2025</td>
                            <td class="border border-gray-300 px-2 py-2 text-center" style="font-size: 11px;">7:01 AM</td>
                            <td class="border border-gray-300 px-2 py-2 text-center" style="font-size: 11px;">3:22 PM</td>
                            <td class="border border-gray-300 px-2 py-2" style="font-size: 11px;">Fairiz Bin Rahman</td>
                            <td class="border border-gray-300 px-2 py-2" style="font-size: 11px;">Pejabat RISDA Sibu</td>
                            <td class="border border-gray-300 px-2 py-2" style="font-size: 11px;">Dewan Suarah Sibu</td>
                            <td class="border border-gray-300 px-2 py-2" style="font-size: 11px;">Mohd Khairul</td>
                            <td class="border border-gray-300 px-2 py-2" style="font-size: 11px;">Fairiz Bin Rahman</td>
                            <td class="border border-gray-300 px-2 py-2 text-right" style="font-size: 11px;">12,645</td>
                            <td class="border border-gray-300 px-2 py-2 text-right" style="font-size: 11px;">100.0</td>
                            <td class="border border-gray-300 px-2 py-2 text-right" style="font-size: 11px;">R789012 - RM150.00</td>
                            <td class="border border-gray-300 px-2 py-2 text-right" style="font-size: 11px;">55.0</td>
                            <td class="border border-gray-300 px-2 py-2" style="font-size: 11px;">-</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Summary Footer Table -->
            <div class="mt-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-3" style="font-size: 12px;">KADAR PENGGUNAAN BAHAN API BULANAN</h3>
                <div class="w-full overflow-x-auto">
                    <table class="min-w-full table-auto border-collapse">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border border-gray-300 px-3 py-2 text-center text-gray-800" style="font-size: 11px; width: 8%;">Bulan</th>
                                <th class="border border-gray-300 px-3 py-2 text-right text-gray-800" style="font-size: 11px; width: 15%;">Jumlah Jarak Perjalanan (KM)</th>
                                <th class="border border-gray-300 px-3 py-2 text-right text-gray-800" style="font-size: 11px; width: 15%;">Jumlah Penggunaan Bahan Api (Liter)</th>
                                <th class="border border-gray-300 px-3 py-2 text-right text-gray-800" style="font-size: 11px; width: 15%;">Jumlah Pembelian Bahan Api (RM)</th>
                                <th class="border border-gray-300 px-3 py-2 text-right text-gray-800" style="font-size: 11px; width: 15%;">Kadar Penggunaan Bahan Api (KM/Liter)</th>
                                <th class="border border-gray-300 px-3 py-2 text-center text-gray-800" style="font-size: 11px; width: 32%;">Disahkan Oleh</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bg-white">
                                <td class="border border-gray-300 px-3 py-2 text-center" style="font-size: 11px;">9</td>
                                <td class="border border-gray-300 px-3 py-2 text-right font-semibold" style="font-size: 11px;">3,780 KM</td>
                                <td class="border border-gray-300 px-3 py-2 text-right font-semibold" style="font-size: 11px;">284.89 Liter</td>
                                <td class="border border-gray-300 px-3 py-2 text-right font-semibold" style="font-size: 11px;">RM 634.24</td>
                                <td class="border border-gray-300 px-3 py-2 text-right font-semibold" style="font-size: 11px;">11.02 KM/Liter</td>
                                <td class="border border-gray-300 px-3 py-2" style="font-size: 11px;">
                                    <div class="flex flex-col gap-1 text-center">
                                        <div class="text-xs text-gray-500">Tandatangan: __________________</div>
                                        <div class="font-semibold">Ahmad Bin Abdullah</div>
                                        <div class="text-xs text-gray-600">Pengurus Bahagian</div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Record count -->
            <div class="mt-4 text-gray-800" style="font-size: 12px;">
                Jumlah Rekod: <span class="font-semibold">26</span>
            </div>

            <!-- Notes -->
            <div class="mt-6 text-gray-700" style="font-size: 10px;">
                <p class="italic">* Potong yang tidak berkenaan</p>
                <p class="italic">** Formula Pengiraan: (e) = (b) / (c)</p>
            </div>
        </div>

        <!-- Design Notes -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-sm p-4">
            <h3 class="font-semibold text-blue-900 mb-2" style="font-size: 13px;">📝 Design Notes</h3>
            <ul class="text-blue-800 space-y-1" style="font-size: 11px;">
                <li>• Filter: 5 kolum (Jenis Laporan, Bulan/Tahun, Jenis Kenderaan, No. Pendaftaran, Bahagian/Unit)</li>
                <li>• Thead utama: 2 baris (rowspan/colspan untuk grouping)</li>
                <li>• Kolum "Masa" dibahagi: Mulai | Hingga</li>
                <li>• Kolum "Tujuan & Destinasi" dibahagi: Pelulus | Pengguna</li>
                <li>• Kolum "Nama Tandatangan" dibahagi: Pelulus | Pengguna</li>
                <li>• Kolum "Pembelian Bahan Api" dibahagi: No. Resit & RM | Liter</li>
                <li>• Footer table: Summary bulanan dengan kolum "Disahkan Oleh" (Tandatangan, Nama, Jawatan dalam satu sel)</li>
                <li>• Font Poppins 10-12px, border radius 0-2px, Tailwind CSS</li>
            </ul>
        </div>
    </div>
</body>
</html>

