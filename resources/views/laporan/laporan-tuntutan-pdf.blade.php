<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Tuntutan</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #1a202c;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary {
            background-color: #f7fafc;
            border: 1px solid #e2e8f0;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .summary table {
            width: 100%;
        }
        .summary td {
            padding: 5px;
        }
        .summary strong {
            color: #2d3748;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table.data-table th {
            background-color: #4a5568;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
        }
        table.data-table td {
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f7fafc;
        }
        .status {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
        }
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-diluluskan { background-color: #d1fae5; color: #065f46; }
        .status-ditolak { background-color: #fee2e2; color: #991b1b; }
        .status-digantung { background-color: #e5e7eb; color: #1f2937; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .signature-section {
            margin-top: 50px;
            page-break-inside: avoid;
        }
        .signature-box {
            display: inline-block;
            width: 45%;
            text-align: center;
            vertical-align: top;
        }
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN TUNTUTAN</h1>
        <p>RISDA Odometer System</p>
        @if(request()->filled('tarikh_dari') && request()->filled('tarikh_hingga'))
        <p>{{ \Carbon\Carbon::parse(request('tarikh_dari'))->format('d/m/Y') }} - {{ \Carbon\Carbon::parse(request('tarikh_hingga'))->format('d/m/Y') }}</p>
        @else
        <p>Semua Tempoh</p>
        @endif
    </div>

    <div class="summary">
        <table>
            <tr>
                <td width="50%"><strong>Tarikh Laporan:</strong> {{ now()->format('d/m/Y H:i') }}</td>
                <td width="50%"><strong>Jumlah Tuntutan:</strong> {{ $tuntutan->count() }}</td>
            </tr>
            <tr>
                <td><strong>Jumlah Diluluskan:</strong> RM {{ number_format($total_diluluskan, 2) }}</td>
                <td><strong>Jumlah Pending:</strong> RM {{ number_format($total_pending, 2) }}</td>
            </tr>
            @if(request()->filled('status'))
            <tr>
                <td colspan="2"><strong>Filter Status:</strong> {{ ucfirst(request('status')) }}</td>
            </tr>
            @endif
            @if(request()->filled('kategori'))
            <tr>
                <td colspan="2"><strong>Filter Kategori:</strong> {{ request('kategori') }}</td>
            </tr>
            @endif
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="8%">Bil</th>
                <th width="12%">Tarikh</th>
                <th width="20%">Pemandu</th>
                <th width="20%">Program</th>
                <th width="15%">Kategori</th>
                <th width="12%" class="text-right">Jumlah (RM)</th>
                <th width="13%" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tuntutan as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->created_at->format('d/m/Y') }}</td>
                <td>{{ $item->logPemandu->pemandu->risdaStaf->nama_penuh ?? '-' }}</td>
                <td>{{ Str::limit($item->logPemandu->program->nama_program ?? '-', 30) }}</td>
                <td>{{ $item->kategori_label }}</td>
                <td class="text-right">{{ number_format($item->jumlah, 2) }}</td>
                <td class="text-center">
                    <span class="status status-{{ $item->status }}">
                        {{ $item->status_label }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Tiada rekod dijumpai</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #edf2f7; font-weight: bold;">
                <td colspan="5" class="text-right">JUMLAH KESELURUHAN:</td>
                <td class="text-right">RM {{ number_format($tuntutan->sum('jumlah'), 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="signature-section">
        <table width="100%">
            <tr>
                <td width="50%" class="signature-box">
                    <div class="signature-line">
                        <strong>Disediakan Oleh</strong><br>
                        {{ Auth::user()->name }}<br>
                        {{ now()->format('d/m/Y') }}
                    </div>
                </td>
                <td width="50%" class="signature-box">
                    <div class="signature-line">
                        <strong>Diluluskan Oleh</strong><br>
                        <br><br>
                        _______________________
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Dokumen ini dijana secara automatik oleh RISDA Odometer System</p>
        <p>Dicetak pada {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>

