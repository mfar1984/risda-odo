<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Audit Trail - {{ $user->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 2px solid #1e40af;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            color: #1e40af;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 11px;
            color: #666;
        }
        .info-section {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
        }
        .info-section h3 {
            font-size: 12px;
            color: #1e40af;
            margin-bottom: 10px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
        }
        .info-grid {
            display: table;
            width: 100%;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            width: 150px;
            font-weight: bold;
            padding: 3px 0;
            color: #64748b;
        }
        .info-value {
            display: table-cell;
            padding: 3px 0;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.data-table th {
            background-color: #1e40af;
            color: white;
            padding: 8px 5px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
        }
        table.data-table td {
            padding: 6px 5px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 9px;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 2px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-page-view { background-color: #dbeafe; color: #1e40af; }
        .badge-button-click { background-color: #f3e8ff; color: #7c3aed; }
        .badge-form-submit { background-color: #dcfce7; color: #166534; }
        .badge-login { background-color: #d1fae5; color: #065f46; }
        .badge-logout { background-color: #f3f4f6; color: #374151; }
        .badge-login-failed { background-color: #fee2e2; color: #991b1b; }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            font-size: 9px;
            color: #64748b;
            text-align: center;
        }
        .page-break {
            page-break-after: always;
        }
        .summary-box {
            display: inline-block;
            width: 30%;
            text-align: center;
            padding: 10px;
            margin: 5px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
        }
        .summary-box .number {
            font-size: 20px;
            font-weight: bold;
            color: #1e40af;
        }
        .summary-box .label {
            font-size: 9px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN AUDIT TRAIL</h1>
        <p>Sistem Pengurusan Odometer RISDA</p>
    </div>

    <!-- User Info Section -->
    <div class="info-section">
        <h3>Maklumat Pengguna</h3>
        <div class="info-grid">
            <div class="info-row">
                <span class="info-label">Nama:</span>
                <span class="info-value">{{ $user->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ $user->email }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Julat Tarikh:</span>
                <span class="info-value">{{ $dateFrom->format('d/m/Y') }} - {{ $dateTo->format('d/m/Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Jumlah Rekod:</span>
                <span class="info-value">{{ $auditTrails->count() }} aktiviti</span>
            </div>
        </div>
    </div>

    <!-- Summary Section -->
    <div class="info-section">
        <h3>Ringkasan Aktiviti</h3>
        <div style="text-align: center;">
            @php
                $pageViews = $auditTrails->where('action_type', 'page_view')->count();
                $buttonClicks = $auditTrails->where('action_type', 'button_click')->count();
                $formSubmits = $auditTrails->where('action_type', 'form_submit')->count();
                $logins = $auditTrails->where('action_type', 'login')->count();
                $logouts = $auditTrails->where('action_type', 'logout')->count();
                $failedLogins = $auditTrails->where('action_type', 'login_failed')->count();
            @endphp
            <div class="summary-box">
                <div class="number">{{ $pageViews }}</div>
                <div class="label">Lawatan Halaman</div>
            </div>
            <div class="summary-box">
                <div class="number">{{ $buttonClicks }}</div>
                <div class="label">Klik Butang</div>
            </div>
            <div class="summary-box">
                <div class="number">{{ $formSubmits }}</div>
                <div class="label">Hantar Borang</div>
            </div>
        </div>
        <div style="text-align: center; margin-top: 10px;">
            <div class="summary-box">
                <div class="number">{{ $logins }}</div>
                <div class="label">Log Masuk</div>
            </div>
            <div class="summary-box">
                <div class="number">{{ $logouts }}</div>
                <div class="label">Log Keluar</div>
            </div>
            <div class="summary-box">
                <div class="number">{{ $failedLogins }}</div>
                <div class="label">Log Masuk Gagal</div>
            </div>
        </div>
    </div>

    <!-- Audit Trail Table -->
    <div class="info-section">
        <h3>Senarai Aktiviti</h3>
        @if($auditTrails->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 80px;">Tarikh/Masa</th>
                        <th style="width: 80px;">Jenis</th>
                        <th>Aktiviti</th>
                        <th style="width: 100px;">IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($auditTrails as $trail)
                        <tr>
                            <td>
                                {{ $trail->created_at->format('d/m/Y') }}<br>
                                <small>{{ $trail->created_at->format('H:i:s') }}</small>
                            </td>
                            <td>
                                @switch($trail->action_type)
                                    @case('page_view')
                                        <span class="badge badge-page-view">Lawatan</span>
                                        @break
                                    @case('button_click')
                                        <span class="badge badge-button-click">Klik</span>
                                        @break
                                    @case('form_submit')
                                        <span class="badge badge-form-submit">Borang</span>
                                        @break
                                    @case('login')
                                        <span class="badge badge-login">Log Masuk</span>
                                        @break
                                    @case('logout')
                                        <span class="badge badge-logout">Log Keluar</span>
                                        @break
                                    @case('login_failed')
                                        <span class="badge badge-login-failed">Gagal</span>
                                        @break
                                    @default
                                        <span class="badge">{{ $trail->action_type }}</span>
                                @endswitch
                            </td>
                            <td>
                                {{ $trail->action_name }}
                                @if($trail->url)
                                    <br><small style="color: #64748b;">{{ Str::limit($trail->url, 60) }}</small>
                                @endif
                            </td>
                            <td style="font-family: monospace;">{{ $trail->ip_address ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="text-align: center; padding: 20px; color: #64748b;">
                Tiada rekod audit trail untuk tempoh yang dipilih.
            </p>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Laporan dijana pada: {{ $generatedAt->format('d/m/Y H:i:s') }}</p>
        <p>Dijana oleh: {{ $generatedBy->name }} ({{ $generatedBy->email }})</p>
        <p style="margin-top: 10px;">© {{ date('Y') }} RISDA - Sistem Pengurusan Odometer</p>
    </div>
</body>
</html>
