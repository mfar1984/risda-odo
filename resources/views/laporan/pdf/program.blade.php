<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Laporan Program - {{ $program->nama_program }}</title>
    <style>
        * {
            font-family: "Poppins", Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
        }

        body {
            color: #1f2937;
            margin: 24px;
        }

        h1 {
            font-size: 18px;
            margin-bottom: 6px;
        }

        h2 {
            font-size: 14px;
            margin-bottom: 4px;
            color: #2563eb;
        }

        .section {
            margin-bottom: 18px;
        }

        .grid {
            display: table;
            width: 100%;
        }

        .grid-row {
            display: table-row;
        }

        .grid-cell {
            display: table-cell;
            padding: 4px 6px 4px 0;
            vertical-align: top;
            width: 50%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #d1d5db;
            padding: 6px 8px;
            text-align: left;
        }

        th {
            background: #f3f4f6;
            font-weight: 600;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            background: #e0f2fe;
            color: #0369a1;
            font-weight: 600;
        }

        .stat-grid {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -6px;
        }

        .stat-card {
            flex: 0 0 33%;
            padding: 6px;
        }

        .stat-card-inner {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 10px;
            background: #fafafa;
        }

        .stat-value {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .stat-label {
            font-size: 10px;
            letter-spacing: .05em;
            text-transform: uppercase;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <h1>Laporan Program</h1>
    <div class="section">
        <h2>{{ $program->nama_program }}</h2>
        <span class="badge" style="background: #e0f2fe; color: #0369a1;">Status: {{ ucfirst($program->status) }}</span>
    </div>

    <div class="section">
        <h2>Maklumat Utama</h2>
        <div class="grid">
            <div class="grid-row">
                <div class="grid-cell">
                    <strong>Tarikh Mula</strong><br>
                    {{ optional($program->tarikh_mula)->format('d/m/Y H:i') ?? '-' }}
                </div>
                <div class="grid-cell">
                    <strong>Tarikh Tamat</strong><br>
                    {{ optional($program->tarikh_selesai)->format('d/m/Y H:i') ?? '-' }}
                </div>
            </div>
            <div class="grid-row">
                <div class="grid-cell">
                    <strong>Lokasi</strong><br>
                    {{ $program->lokasi_program ?? '-' }}
                </div>
                <div class="grid-cell">
                    <strong>Anggaran KM</strong><br>
                    {{ $program->jarak_anggaran ? number_format($program->jarak_anggaran, 1) . ' km' : '-' }}
                </div>
            </div>
            <div class="grid-row">
                <div class="grid-cell">
                    <strong>Pemohon</strong><br>
                    {{ $program->pemohon->nama_penuh ?? '-' }}
                </div>
                <div class="grid-cell">
                    <strong>Pemandu Tugasan</strong><br>
                    {{ $program->pemandu->nama_penuh ?? '-' }}
                </div>
            </div>
            <div class="grid-row">
                <div class="grid-cell">
                    <strong>Kenderaan</strong><br>
                    {{ $program->kenderaan ? $program->kenderaan->no_plat . ' - ' . trim(($program->kenderaan->jenama ?? '') . ' ' . ($program->kenderaan->model ?? '')) : '-' }}
                </div>
                <div class="grid-cell">
                    <strong>Penerangan</strong><br>
                    {{ $program->penerangan ?? '-' }}
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Ringkasan Statistik</h2>
        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-card-inner">
                    <div class="stat-value">{{ number_format($stats['jumlah_log']) }}</div>
                    <div class="stat-label">Jumlah Log</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-card-inner">
                    <div class="stat-value">{{ number_format($stats['jumlah_pemandu']) }}</div>
                    <div class="stat-label">Pemandu Terlibat</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-card-inner">
                    <div class="stat-value">{{ number_format($stats['jumlah_kenderaan']) }}</div>
                    <div class="stat-label">Kenderaan Digunakan</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-card-inner">
                    <div class="stat-value">{{ number_format($stats['jumlah_checkin']) }}</div>
                    <div class="stat-label">Jumlah Check-in</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-card-inner">
                    <div class="stat-value">{{ number_format($stats['jumlah_checkout']) }}</div>
                    <div class="stat-label">Jumlah Check-out</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-card-inner">
                    <div class="stat-value">{{ number_format($stats['jarak_km'], 1) }} km</div>
                    <div class="stat-label">Jarak Tmp. (Log)</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-card-inner">
                    <div class="stat-value">RM {{ number_format($stats['kos'], 2) }}</div>
                    <div class="stat-label">Kos Bahan Api</div>
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Log Pemandu</h2>
        <table>
            <thead>
                <tr>
                    <th>Tarikh</th>
                    <th>Pemandu</th>
                    <th>Kenderaan</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Odometer</th>
                    <th>Status</th>
                    <th>Jarak (km)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ optional($log->tarikh_perjalanan)->format('d/m/Y') ?? '-' }}</td>
                        <td>{{ $log->pemandu->name ?? '-' }}</td>
                        <td>{{ $log->kenderaan->no_plat ?? '-' }}</td>
                        <td>{{ $log->masa_keluar_label ?? '-' }}</td>
                        <td>{{ $log->masa_masuk_label ?? '-' }}</td>
                        <td>{{ $log->odometer_keluar ? number_format($log->odometer_keluar) : '-' }} / {{ $log->odometer_masuk ? number_format($log->odometer_masuk) : '-' }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $log->status)) }}</td>
                        <td>{{ $log->jarak ? number_format($log->jarak, 1) : '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center;">Tiada log direkod.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p style="font-size: 9px; color: #9ca3af; text-align: center; margin-top: 24px;">Dijana pada {{ now()->format('d/m/Y H:i') }} melalui Sistem RISDA Odometer.</p>
</body>
</html>

