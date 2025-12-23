<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { 
            margin-top: 10mm; 
            margin-bottom: 5mm; 
            margin-left: 10mm;
            margin-right: 10mm;
        }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; margin: 0; padding: 0; }
        
        header {
            position: fixed;
            top: -25mm;
            left: 0;
            right: 0;
            height: 23mm;
        }
        
        footer {
            position: fixed;
            bottom: -55mm;
            left: 0;
            right: 0;
            height: 55mm;
        }
        
        main { margin: 0; }
        
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 3px 4px; vertical-align: top; }
        th { background: #f0f0f0; text-align: center; font-weight: 700; font-size: 8px; }
        .title { text-align: center; font-weight: bold; font-size: 13px; margin-bottom: 4px; }
        .header-info { font-size: 9px; margin-bottom: 6px; display: flex; justify-content: space-between; }
        .serial { color: #d92d20; font-weight: 700; font-size: 16px; }
        .right { text-align: right; }
        .center { text-align: center; }
        .left { text-align: left; }
        .page-break { page-break-after: always; }
        .footer-summary h3 { text-align: center; font-size: 11px; margin-bottom: 4px; }
        .footer-summary table th { font-size: 8px; padding: 4px; }
        .footer-summary table td { text-align: center; font-weight: 700; padding: 4px; }
        .jumlah-row { background: #f9fafb; font-weight: 700; }
    </style>
</head>
<body>
    {{-- footer removed from fixed position; will be rendered per page below --}}

@foreach($pages as $i => $pg)

    {{-- HEADER (per page; bind No. Siri from DB serials) --}}
    <section>
        <div style="position: relative; margin-bottom: 16px;">
            <div class="title" style="text-align: center;">BUTIR-BUTIR PENGGUNAAN KENDERAAN</div>
            <div style="position: absolute; right: 0; top: 0; font-size: 9px; white-space: nowrap;"><strong>No. Siri</strong> : <span class="serial" style="display:inline-block; text-align: right; line-height: 1; vertical-align: text-bottom;">{{ $serials[$i] ?? ($pg['no_siri'] ?? '') }}</span></div>
        </div>
        <table style="border: none; margin-bottom: 8px;">
            <tr style="border: none;">
                <td style="border: none; width: 33%; font-size: 11px;"><strong>Jenis Kenderaan</strong> : {{ $header['jenis'] ?? '-' }}</td>
                <td style="border: none; width: 33%; font-size: 11px;"><strong>No. Pendaftaran</strong> : {{ $header['noPlat'] ?? '-' }}</td>
                <td style="border: none; width: 34%; font-size: 11px;"><strong>Bahagian/Unit</strong> : {{ $header['bahagian'] ?? '-' }}</td>
            </tr>
        </table>
    </section>

    {{-- DATA TABLE (main content, will flow between header and footer) --}}
    <main>
    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 6%; vertical-align: middle;">Tarikh</th>
                <th colspan="2" style="width: 8%; vertical-align: middle;">Masa</th>
                <th rowspan="2" style="width: 10%; vertical-align: middle;">Nama Pemandu</th>
                <th rowspan="2" style="width: 15%; vertical-align: middle;">Tujuan & Destinasi<br/>(dari — ke)</th>
                <th colspan="2" style="width: 12%; vertical-align: middle;">Nama Tandatangan</th>
                <th rowspan="2" style="width: 8%;vertical-align: middle;">Bacaan<br/>Odometer<br/>(KM)</th>
                <th rowspan="2" style="width: 7%;vertical-align: middle;">Jarak<br/>Perjalanan<br/>(KM)</th>
                <th colspan="3" style="width: 18%; vertical-align: middle;">Pembelian Bahan Api<br/>(Petrol/Diesel/Gas)</th>
                <th rowspan="2" style="width: 10%; vertical-align: middle;">Arahan Khas<br/>Pengguna<br/>Kenderaan</th>
            </tr>
            <tr>
                <th style="width: 4%;">Mulai</th>
                <th style="width: 4%;">Hingga</th>
                <th style="width: 6%;">Pelulus</th>
                <th style="width: 6%;">Pengguna</th>
                <th style="width: 6%;">No.Resit</th>
                <th style="width: 6%;">RM</th>
                <th style="width: 6%;">Liter</th>
            </tr>
        </thead>
        <tbody>
        @foreach($pg['rows'] as $r)
            <tr>
                <td class="center" style="font-size: 8px; vertical-align: middle;">{{ $r['tarikh'] ?? '-' }}</td>
                <td class="center" style="font-size: 8px; vertical-align: middle;">{{ $r['masaMulai'] ?? '-' }}</td>
                <td class="center" style="font-size: 8px; vertical-align: middle;">{{ $r['masaHingga'] ?? '-' }}</td>
                <td style="font-size: 8px; vertical-align: middle;">{{ $r['pemandu'] ?? '-' }}</td>
                <td style="font-size: 7px; line-height: 1.3;">
                    {{ $r['destinasiDari'] ?? '-' }}<br/>
                    <span style="color: #666;">ke</span><br/>
                    {{ $r['destinasiKe'] ?? '-' }}
                </td>
                <td></td>
                <td></td>
                <td class="center" style="font-size: 8px;">
                    {{ $r['odometerKeluar'] ?? '-' }} KM<br/><br/>
                    {{ $r['odometerMasuk'] ?? '-' }} KM
                </td>
                <td class="center" style="font-size: 8px; vertical-align: middle;">{{ ($r['jarakPerjalanan'] ?? '-') }} KM</td>
                <td class="center" style="font-size: 8px; vertical-align: middle;">{{ $r['resitNo'] ?? '-' }}</td>
                <td class="center" style="font-size: 8px; vertical-align: middle;">{{ $r['resitRM'] ?? '-' }}</td>
                <td class="center" style="font-size: 8px; vertical-align: middle;">{{ $r['liter'] ?? '-' }}</td>
                <td class="center" style="font-size: 7px; vertical-align: top; text-align: left;">{{ $r['arahanKhas'] ?? '-' }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
            <tr class="jumlah-row">
                <td colspan="7" style="text-align: right; font-size: 9px;">JUMLAH</td>
                <td></td> <!-- Bacaan Odometer (no total) -->
                <td class="center" style="font-size: 9px; vertical-align: middle;">{{ number_format($pg['totals']['jarak'], 0) }} KM</td>
                <td></td> <!-- No. Resit (no total) -->
                <td class="center" style="font-size: 9px; vertical-align: middle;">RM {{ number_format($pg['totals']['rm'], 2) }}</td>
                <td class="center" style="font-size: 9px; vertical-align: middle;">{{ number_format($pg['totals']['liter'], 2) }} L</td>
                <td></td> <!-- Arahan Khas (no total) -->
            </tr>
        </tfoot>
    </table>
    </main>

    {{-- FOOTER (per page; summary computed from this page only) --}}
    <div class="footer-summary" style="margin-top: 6px;">
        <h3>KADAR PENGGUNAAN BAHAN API BULANAN</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 8%;">Bulan</th>
                    <th style="width: 15%;">Jumlah Jarak Perjalanan<br/>(KM)</th>
                    <th style="width: 15%;">Jumlah Penggunaan Bahan Api<br/>(Liter)</th>
                    <th style="width: 15%;">Jumlah Pembelian Bahan Api<br/>(RM)</th>
                    <th style="width: 15%;">Kadar Penggunaan Bahan Api<br/>(KM/Liter)</th>
                    <th style="width: 32%;">Disahkan Oleh</th>
                </tr>
            </thead>
        <tbody>
            @php
                $pageJarak = $pg['totals']['jarak'] ?? 0;
                $pageLiter = $pg['totals']['liter'] ?? 0;
                $pageRM = $pg['totals']['rm'] ?? 0;
                $pageKadar = $pageLiter > 0 ? ($pageJarak / max($pageLiter, 0.000001)) : 0;
            @endphp
            <tr>
                <td class="center" style="font-size: 9px; vertical-align: middle;">{{ $summary['bulan'] ?? '-' }}</td>
                <td class="center" style="font-size: 9px; vertical-align: middle;">{{ number_format($pageJarak, 1) }} KM</td>
                <td class="center" style="font-size: 9px; vertical-align: middle;">{{ number_format($pageLiter, 2) }} Liter</td>
                <td class="center" style="font-size: 9px; vertical-align: middle;">RM {{ number_format($pageRM, 2) }}</td>
                <td class="center" style="font-size: 9px; vertical-align: middle;">{{ number_format($pageKadar, 2) }} KM/Liter</td>
                <td style="font-size: 8px; vertical-align: bottom; padding: 8px 4px; text-align: left;">
                    <div style="display: flex; flex-direction: column; gap: 2px; min-height: 40px; justify-content: flex-end;">
                        <div><span style="display: inline-block; width: 70px;">Tandatangan</span><span>:</span></div><br>
                        <div><span style="display: inline-block; width: 70px;">Nama</span><span>:</span></div><br>
                        <div><span style="display: inline-block; width: 70px;">Jawatan</span><span>:</span></div>
                    </div>
                </td>
            </tr>
        </tbody>
        </table>
        <div style="font-size: 8px; margin-top: 6px; color: #666;">
            <div>Jumlah Rekod (halaman ini): {{ count($pg['rows'] ?? []) }}</div>
            <div style="font-style: italic; margin-top: 2px;">* Potong yang tidak berkenaan</div>
            <div style="font-style: italic;">** Formula Pengiraan: Kadar = Jumlah Jarak / Jumlah Liter</div>
        </div>
    </div>

    @if($i < count($pages)-1)
        <div class="page-break"></div>
    @endif
@endforeach

{{-- OVERALL SUMMARY (at the end of PDF, after all pages) --}}
@php
    $overallJarak = 0;
    $overallLiter = 0;
    $overallRM = 0;
    foreach($pages as $pg) {
        $overallJarak += $pg['totals']['jarak'] ?? 0;
        $overallLiter += $pg['totals']['liter'] ?? 0;
        $overallRM += $pg['totals']['rm'] ?? 0;
    }
    $overallKadar = $overallLiter > 0 ? ($overallJarak / max($overallLiter, 0.000001)) : 0;
@endphp

<div class="page-break"></div>

<section>
    <div style="position: relative; margin-bottom: 16px;">
        <div class="title" style="text-align: center;">BUTIR-BUTIR PENGGUNAAN KENDERAAN</div>
        <div style="position: absolute; right: 0; top: 0; font-size: 9px; white-space: nowrap;"><strong>No. Siri</strong> : <span class="serial" style="display:inline-block; text-align: right; line-height: 1; vertical-align: text-bottom;">KESELURUHAN</span></div>
    </div>
    <table style="border: none; margin-bottom: 8px;">
        <tr style="border: none;">
            <td style="border: none; width: 33%; font-size: 11px;"><strong>Jenis Kenderaan</strong> : {{ $header['jenis'] ?? '-' }}</td>
            <td style="border: none; width: 33%; font-size: 11px;"><strong>No. Pendaftaran</strong> : {{ $header['noPlat'] ?? '-' }}</td>
            <td style="border: none; width: 34%; font-size: 11px;"><strong>Bahagian/Unit</strong> : {{ $header['bahagian'] ?? '-' }}</td>
        </tr>
    </table>
</section>

<div class="footer-summary" style="margin-top: 6px;">
    <h3>KADAR PENGGUNAAN BAHAN API BULANAN KESELURUHAN</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 8%;">Bulan</th>
                <th style="width: 15%;">Jumlah Jarak Perjalanan<br/>(KM)</th>
                <th style="width: 15%;">Jumlah Penggunaan Bahan Api<br/>(Liter)</th>
                <th style="width: 15%;">Jumlah Pembelian Bahan Api<br/>(RM)</th>
                <th style="width: 15%;">Kadar Penggunaan Bahan Api<br/>(KM/Liter)</th>
                <th style="width: 32%;">Disahkan Oleh</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="center" style="font-size: 9px; vertical-align: middle;">{{ $summary['bulan'] ?? '-' }}</td>
                <td class="center" style="font-size: 9px; vertical-align: middle;">{{ number_format($overallJarak, 1) }} KM</td>
                <td class="center" style="font-size: 9px; vertical-align: middle;">{{ number_format($overallLiter, 2) }} Liter</td>
                <td class="center" style="font-size: 9px; vertical-align: middle;">RM {{ number_format($overallRM, 2) }}</td>
                <td class="center" style="font-size: 9px; vertical-align: middle;">{{ number_format($overallKadar, 2) }} KM/Liter</td>
                <td style="font-size: 8px; vertical-align: bottom; padding: 8px 4px; text-align: left;">
                    <div style="display: flex; flex-direction: column; gap: 2px; min-height: 40px; justify-content: flex-end;">
                        <div><span style="display: inline-block; width: 70px;">Tandatangan</span><span>:</span></div><br>
                        <div><span style="display: inline-block; width: 70px;">Nama</span><span>:</span></div><br>
                        <div><span style="display: inline-block; width: 70px;">Jawatan</span><span>:</span></div>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    <div style="font-size: 8px; margin-top: 6px; color: #666;">
        <div>Jumlah Rekod (keseluruhan): {{ array_sum(array_map(fn($p) => count($p['rows'] ?? []), $pages)) }}</div>
        <div>Jumlah Halaman: {{ count($pages) }}</div>
        <div style="font-style: italic; margin-top: 2px;">* Potong yang tidak berkenaan</div>
        <div style="font-style: italic;">** Formula Pengiraan: Kadar = Jumlah Jarak / Jumlah Liter</div>
    </div>
</div>

</body>
</html>
