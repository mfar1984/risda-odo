<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kenderaan {{ $kenderaan->no_plat }}</title>
    <style>
        body { font-family: "DejaVu Sans", sans-serif; font-size: 12px; color: #111827; }
        h1, h2, h3 { margin: 0; }
        .header { text-align: center; margin-bottom: 24px; }
        .section { margin-bottom: 24px; }
        .section-title { font-size: 16px; font-weight: 700; margin-bottom: 8px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #E5E7EB; padding: 8px 10px; text-align: left; }
        .table th { background: #F3F4F6; font-weight: 600; font-size: 12px; }
        .table td { font-size: 12px; }
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
        .stat-card { border: 1px solid #E5E7EB; padding: 12px; border-radius: 8px; background: #F9FAFB; }
        .stat-label { font-size: 11px; color: #6B7280; text-transform: uppercase; letter-spacing: 0.05em; }
        .stat-value { font-size: 18px; font-weight: 700; margin-top: 4px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Kenderaan</h1>
        <p>No. Plat: {{ $kenderaan->no_plat }}</p>
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="section">
        <div class="section-title">Maklumat Kenderaan</div>
        <table class="table" style="margin-top: 8px;">
            <tbody>
                <tr>
                    <th style="width: 30%;">No. Plat</th>
                    <td>{{ $kenderaan->no_plat }}</td>
                </tr>
                <tr>
                    <th>Jenama & Model</th>
                    <td>{{ $kenderaan->jenama }} {{ $kenderaan->model }}</td>
                </tr>
                <tr>
                    <th>Tahun</th>
                    <td>{{ $kenderaan->tahun ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Jenis Bahan Api</th>
                    <td>{{ ucfirst($kenderaan->jenis_bahan_api_label ?? '-') }}</td>
                </tr>
                <tr>
                    <th>Bahagian</th>
                    <td>{{ $kenderaan->bahagian->nama_bahagian ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Stesen</th>
                    <td>{{ $kenderaan->stesen->nama_stesen ?? 'Semua Stesen' }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Statistik Penggunaan</div>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Jumlah Log</div>
                <div class="stat-value">{{ number_format($stats['jumlah_log']) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Log Aktif</div>
                <div class="stat-value">{{ number_format($stats['jumlah_aktif']) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Log Selesai</div>
                <div class="stat-value">{{ number_format($stats['jumlah_selesai']) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Log Tertunda</div>
                <div class="stat-value">{{ number_format($stats['jumlah_tertunda']) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Jumlah Jarak (km)</div>
                <div class="stat-value">{{ number_format($stats['jarak'], 1) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Kos Bahan Api (RM)</div>
                <div class="stat-value">{{ number_format($stats['kos'], 2) }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Senarai Log Kenderaan</div>
        <table class="table" style="margin-top: 8px;">
            <thead>
                <tr>
                    <th>Tarikh</th>
                    <th>Pemandu</th>
                    <th>Program</th>
                    <th>Jarak (km)</th>
                    <th>Kos (RM)</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->tarikh_perjalanan ? $log->tarikh_perjalanan->format('d/m/Y') : '-' }}</td>
                        <td>{{ $log->pemandu->name ?? '-' }}</td>
                        <td>{{ $log->program->nama_program ?? '-' }}</td>
                        <td>{{ $log->jarak ? number_format($log->jarak, 1) : '0.0' }}</td>
                        <td>{{ $log->kos_minyak ? number_format($log->kos_minyak, 2) : '-' }}</td>
                        <td>{{ ucfirst($log->status ?? '-') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center;">Tiada log direkod.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>

