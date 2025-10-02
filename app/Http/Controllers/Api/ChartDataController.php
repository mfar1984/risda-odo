<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LogPemandu;
use App\Models\Tuntutan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ChartDataController extends Controller
{
    /**
     * Get overview chart data (Fuel Cost vs Total Claims)
     */
    public function overview(Request $request)
    {
        try {
            $user = auth()->user();
            $period = $request->input('period', '6months'); // '1month' or '6months'
            
            // Determine date range
            if ($period === '1month') {
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                $groupBy = 'day';
            } else {
                $startDate = now()->subMonths(5)->startOfMonth(); // Last 6 months
                $endDate = now()->endOfMonth();
                $groupBy = 'month';
            }

            // Get Fuel Cost Data
            $fuelData = LogPemandu::where('pemandu_id', $user->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereNotNull('kos_minyak')
                ->select(
                    $groupBy === 'day' 
                        ? DB::raw('DAY(created_at) as period')
                        : DB::raw('MONTH(created_at) as period'),
                    DB::raw('SUM(kos_minyak) as total')
                )
                ->groupBy('period')
                ->orderBy('period')
                ->get()
                ->pluck('total', 'period')
                ->toArray();

            // Get Claims Data (approved + pending)
            $claimsData = Tuntutan::whereHas('logPemandu', function($q) use ($user) {
                    $q->where('pemandu_id', $user->id);
                })
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereIn('status', ['pending', 'diluluskan'])
                ->select(
                    $groupBy === 'day' 
                        ? DB::raw('DAY(created_at) as period')
                        : DB::raw('MONTH(created_at) as period'),
                    DB::raw('SUM(jumlah) as total')
                )
                ->groupBy('period')
                ->orderBy('period')
                ->get()
                ->pluck('total', 'period')
                ->toArray();

            // Format data for chart
            $chartData = [];
            
            if ($groupBy === 'day') {
                // 1 month: Days 1-31
                $daysInMonth = $endDate->day;
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $chartData[] = [
                        'period' => $day,
                        'label' => (string) $day,
                        'fuel_cost' => $fuelData[$day] ?? 0,
                        'claims' => $claimsData[$day] ?? 0,
                    ];
                }
            } else {
                // 6 months: Last 6 months
                for ($i = 5; $i >= 0; $i--) {
                    $date = now()->subMonths($i);
                    $month = $date->month;
                    $chartData[] = [
                        'period' => $month,
                        'label' => $date->format('M'), // Jan, Feb, Mar...
                        'fuel_cost' => $fuelData[$month] ?? 0,
                        'claims' => $claimsData[$month] ?? 0,
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'period' => $period,
                    'period_label' => $period === '1month' ? 'Last Month' : 'Last 6 Months',
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                    'chart_data' => $chartData,
                    'totals' => [
                        'fuel_cost' => array_sum(array_column($chartData, 'fuel_cost')),
                        'claims' => array_sum(array_column($chartData, 'claims')),
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch chart data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get Do tab chart data (Start Journey vs End Journey counts)
     */
    public function doActivity(Request $request)
    {
        try {
            $user = auth()->user();
            $period = $request->input('period', '6months');
            
            // Determine date range
            if ($period === '1month') {
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                $groupBy = 'day';
            } else {
                $startDate = now()->subMonths(5)->startOfMonth();
                $endDate = now()->endOfMonth();
                $groupBy = 'month';
            }

            // Get Start Journey counts
            $startJourneyData = LogPemandu::where('pemandu_id', $user->id)
                ->whereBetween('masa_keluar', [$startDate, $endDate])
                ->whereNotNull('masa_keluar')
                ->select(
                    $groupBy === 'day' 
                        ? DB::raw('DAY(masa_keluar) as period')
                        : DB::raw('MONTH(masa_keluar) as period'),
                    DB::raw('COUNT(*) as total')
                )
                ->groupBy('period')
                ->orderBy('period')
                ->get()
                ->pluck('total', 'period')
                ->toArray();

            // Get End Journey counts
            $endJourneyData = LogPemandu::where('pemandu_id', $user->id)
                ->whereBetween('masa_masuk', [$startDate, $endDate])
                ->whereNotNull('masa_masuk')
                ->select(
                    $groupBy === 'day' 
                        ? DB::raw('DAY(masa_masuk) as period')
                        : DB::raw('MONTH(masa_masuk) as period'),
                    DB::raw('COUNT(*) as total')
                )
                ->groupBy('period')
                ->orderBy('period')
                ->get()
                ->pluck('total', 'period')
                ->toArray();

            // Format data for chart
            $chartData = [];
            
            if ($groupBy === 'day') {
                $daysInMonth = $endDate->day;
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $chartData[] = [
                        'period' => $day,
                        'label' => (string) $day,
                        'start_journey' => $startJourneyData[$day] ?? 0,
                        'end_journey' => $endJourneyData[$day] ?? 0,
                    ];
                }
            } else {
                for ($i = 5; $i >= 0; $i--) {
                    $date = now()->subMonths($i);
                    $month = $date->month;
                    $chartData[] = [
                        'period' => $month,
                        'label' => $date->format('M'),
                        'start_journey' => $startJourneyData[$month] ?? 0,
                        'end_journey' => $endJourneyData[$month] ?? 0,
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'period' => $period,
                    'period_label' => $period === '1month' ? 'Last Month' : 'Last 6 Months',
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                    'chart_data' => $chartData,
                    'totals' => [
                        'start_journey' => array_sum(array_column($chartData, 'start_journey')),
                        'end_journey' => array_sum(array_column($chartData, 'end_journey')),
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch chart data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

