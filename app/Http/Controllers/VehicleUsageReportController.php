<?php

namespace App\Http\Controllers;

use App\Models\VehicleUsageReport;
use App\Models\NoSiriSequence;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class VehicleUsageReportController extends Controller
{
    public function index(Request $request)
    {
        $kenderaanId = $request->integer('kenderaan_id');
        $bulan = $request->date('bulan');
        $query = VehicleUsageReport::query()
            ->when($kenderaanId, fn($q) => $q->where('kenderaan_id', $kenderaanId))
            ->when($bulan, fn($q) => $q->whereDate('bulan', '=', $bulan))
            ->orderByDesc('created_at')
            ->limit(100);
        return response()->json([
            'success' => true,
            'data' => $query->get()->map(function ($r) {
                return [
                    'id' => $r->id,
                    'noSiri' => $r->no_siri,
                    'noSiriFrom' => $r->no_siri_from,
                    'noSiriTo' => $r->no_siri_to,
                    'numPages' => $r->num_pages,
                    'bulan' => $r->bulan->format('m/Y'),
                    'noPlat' => $r->header['noPlat'] ?? '-',
                    'jenis' => $r->header['jenis'] ?? '-',
                    'disimpanOleh' => $r->header['disimpanOleh'] ?? ($r->relUser->name ?? 'Admin'),
                    'tarikhSimpan' => $r->created_at->format('d/m/Y H:i'),
                ];
            })
        ]);
    }

    public function show(int $id)
    {
        $r = VehicleUsageReport::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $r->id,
                'noSiri' => $r->no_siri,
                'noSiriFrom' => $r->no_siri_from,
                'noSiriTo' => $r->no_siri_to,
                'numPages' => $r->num_pages,
                'bulan' => $r->bulan?->format('Y-m') ?? null,
                'header' => $r->header,
                'rows' => $r->rows,
                'summary' => $r->summary,
            ]
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kenderaan_id' => 'required|integer',
            'bulan' => 'required|date',
            'header' => 'required|array',
            'rows' => 'required|array',
            'summary' => 'required|array',
        ]);

        // Precompute pagination and serial range (8 rows per page)
        $rows = $data['rows'];
        $maxRowsPerPage = 8;
        $numPages = max(1, (int) ceil(count($rows) / $maxRowsPerPage));

        // Reserve serial numbers for all pages
        $reservedSerials = [];
        for ($i = 0; $i < $numPages; $i++) {
            $reservedSerials[] = NoSiriSequence::next('vehicle_usage');
        }
        $noSiriFrom = $reservedSerials[0];
        $noSiriTo = $reservedSerials[count($reservedSerials) - 1];
        $noSiri = $noSiriFrom; // snapshot display

        $report = VehicleUsageReport::create([
            'no_siri' => $noSiri,
            'no_siri_from' => $noSiriFrom,
            'no_siri_to' => $noSiriTo,
            'kenderaan_id' => $data['kenderaan_id'],
            'bulan' => $data['bulan'],
            'header' => $data['header'],
            'rows' => $rows,
            'summary' => array_merge($data['summary'], [
                'maxRowsPerPage' => $maxRowsPerPage,
            ]),
            'num_pages' => $numPages,
            'disimpan_oleh' => Auth::id() ?? 1,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $report->id,
                'noSiri' => $report->no_siri,
                'numPages' => $report->num_pages,
                'noSiriFrom' => $report->no_siri_from,
                'noSiriTo' => $report->no_siri_to,
            ]
        ]);
    }

    public function destroy(int $id)
    {
        $r = VehicleUsageReport::findOrFail($id);
        $r->delete();
        return response()->json(['success' => true]);
    }

    public function pdf(int $id)
    {
        $report = VehicleUsageReport::findOrFail($id);

        $header = $report->header ?? [];
        $rows = $report->rows ?? [];
        $summary = $report->summary ?? [];
        $maxRowsPerPage = (int)($summary['maxRowsPerPage'] ?? 22);
        $numPages = max(1, (int)($report->num_pages ?? ceil(count($rows)/$maxRowsPerPage)));

        // Build serials per page from stored range
        $serials = [];
        $from = $report->no_siri_from ?: $report->no_siri;
        $to = $report->no_siri_to ?: $report->no_siri;
        [$prefixFrom, $numFrom] = array_pad(explode(' ', (string)$from, 2), 2, '');
        [$prefixTo, $numTo] = array_pad(explode(' ', (string)$to, 2), 2, '');
        $numFrom = (int) preg_replace('/\D/', '', $numFrom);
        $numTo = (int) preg_replace('/\D/', '', $numTo);
        $prefix = trim($prefixFrom ?: 'A');
        if ($numTo < $numFrom) { $numTo = $numFrom; }
        $wanted = max($numPages, 1);
        for ($i = 0; $i < $wanted; $i++) {
            $n = $numFrom + $i;
            if ($n > $numTo) { $n = $numTo; }
            $serials[] = trim($prefix . ' ' . $n);
        }

        // Chunk rows per page (8 rows per page for landscape PDF)
        $pages = [];
        $rowsPerPage = 8;
        $chunks = array_chunk($rows, $rowsPerPage);
        foreach ($chunks as $idx => $chunk) {
            $sumJarak = 0.0; $sumRM = 0.0; $sumLiter = 0.0;
            foreach ($chunk as $rr) {
                $sumJarak += isset($rr['jarak_raw']) ? (float)$rr['jarak_raw'] : (float)preg_replace('/[^0-9.]/', '', $rr['jarakPerjalanan'] ?? '0');
                $sumRM += isset($rr['kos_raw']) ? (float)$rr['kos_raw'] : (float)preg_replace('/[^0-9.]/', '', $rr['resitRM'] ?? '0');
                $sumLiter += isset($rr['liter_raw']) ? (float)$rr['liter_raw'] : (float)preg_replace('/[^0-9.]/', '', $rr['liter'] ?? '0');
            }
            $pages[] = [
                'no_siri' => $serials[$idx] ?? ($serials[0] ?? $report->no_siri),
                'rows' => $chunk,
                'totals' => [
                    'jarak' => $sumJarak,
                    'rm' => $sumRM,
                    'liter' => $sumLiter,
                ],
            ];
        }

        $pdf = Pdf::loadView('reports.vehicle_usage_pdf', [
            'header' => $header,
            'summary' => $summary,
            'pages' => $pages,
            // Pass DB-backed serials for each page to the view (used by line 94)
            'serials' => $serials,
            'serialPrefix' => $prefix,
            'serialStart' => $numFrom,
        ])->setPaper('a4', 'landscape');

        // Enable inline PHP for page_script rendering of No. Siri
        if (method_exists($pdf, 'setOptions')) {
            $pdf->setOptions(['isPhpEnabled' => true, 'enable_php' => true]);
        }
        if (method_exists($pdf, 'getDomPDF')) {
            $dompdf = $pdf->getDomPDF();
            if (method_exists($dompdf, 'set_option')) {
                $dompdf->set_option('enable_php', true);
                $dompdf->set_option('isPhpEnabled', true);
            }
        }

        $filename = 'penggunaan-kenderaan-' . ($header['noPlat'] ?? 'kenderaan') . '-' . now()->format('Ymd-Hi') . '.pdf';
        return $pdf->download($filename);
    }
}


