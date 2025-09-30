<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kos {{ $program->nama_program }}</title>
    <style>
        body { font-family: "DejaVu Sans", sans-serif; font-size: 12px; color: #111827; margin: 24px; }
        h1 { font-size: 18px; margin-bottom: 6px; }
        h2 { font-size: 14px; margin-bottom: 4px; color: #2563eb; }
        .section { margin-bottom: 18px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .table th, .table td { border: 1px solid #E5E7EB; padding: 6px 8px; text-align: left; }
        .table th { background: #F3F4F6; font-weight: 600; font-size: 11px; }
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-top: 8px; }
        .stat-card { border: 1px solid #E5E7EB; padding: 10px; border-radius: 6px; background: #F9FAFB; }
        .stat-label { font-size: 10px; color: #6B7280; text-transform: uppercase; letter-spacing: .05em; }
        .stat-value { font-size: 16px; font-weight: 700; margin-top: 4px; }
    </style>
</head>
<body>
    <div class="section">
        <h1>Laporan Kos Program</h1>
        <p>Program: {{ $program->nama_program }}</p>
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="section">
        <h2>Maklumat Program</h2>
        <table class="table">
            <tbody>
                <tr><th>Nama Program</th><td>{{ $program->nama_program }}</td></tr>
                <tr><th>Lokasi</th><td>{{ $program->lokasi_program ?? '-' }}</td></tr>
                <tr><th>Tarikh</th><td>{{ $program->tarikh_mula?->format('d/m/Y H:i') ?? '-' }} hingga {{ $program->tarikh_selesai?->format('d/m/Y H:i') ?? '-' }}</td></tr>
                <tr><th>Pemandu</th><td>{{ $program->pemandu->nama_penuh ?? '-' }}</td></tr>
                <tr><th>Kenderaan</th><td>{{ $program->kenderaan ? $program->kenderaan->no_plat . ' - ' . trim(($program->kenderaan->jenama ?? '') . ' ' . ($program->kenderaan->model ?? '')) : '-' }}</td></tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Statistik Kos</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Jumlah Log</div>
                <div class="stat-value">{{ number_format($stats['jumlah_log']) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Jumlah Kos</div>
                <div class="stat-value">RM {{ number_format($stats['jumlah_kos'], 2) }}</div>
            </div>
            <div class="stat-card">
                <div kelas="stat-label">Jumlah Liter</div>
                <div class="stat-value">{{ number_format($stats['jumlah_liter'] ?? 0, 2) }} L</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Senarai Log Kos</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Tarikh</th>
                    <th>Pemandu</th>
                    <th>Kenderaan</th>
                    <th>Kos (RM)</th>
                    <th>Liter (L)</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->tarikh_perjalanan?->format('d/m/Y') ?? '-' }}</td>
                        <td>{{ $log->pemandu->name ?? '-' }}</td>
                        <td>{{ $log->kenderaan->no_plat ?? '-' }}</td>
                        <td>{{ $log->kos_minyak ? number_format($log->kos_minyak, 2) : '0.00' }}</td>
                        <td>{{ $log->liter_minyak ? number_format($log->liter_minyak, 2) : '0.00' }}</td>
                        <td>{{ $log->catatan ?? '-' }}</td>
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
