<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LogPemandu;
use App\Models\Tuntutan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get Dashboard Statistics
     * Returns current month stats and comparison with last month
     */
    public function statistics(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Current month dates
            $currentMonthStart = Carbon::now()->startOfMonth();
            $currentMonthEnd = Carbon::now()->endOfMonth();
            
            // Last month dates
            $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
            $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();
            
            // Current Month Stats
            $currentStats = $this->getMonthStats($user->id, $currentMonthStart, $currentMonthEnd);
            
            // Last Month Stats (for comparison)
            $lastStats = $this->getMonthStats($user->id, $lastMonthStart, $lastMonthEnd);
            
            // Calculate percentages
            $tripsChange = $this->calculatePercentageChange($currentStats['total_trips'], $lastStats['total_trips']);
            $distanceChange = $this->calculatePercentageChange($currentStats['total_distance'], $lastStats['total_distance']);
            $fuelCostChange = $this->calculatePercentageChange($currentStats['fuel_cost'], $lastStats['fuel_cost']);
            $maintenanceChange = $this->calculatePercentageChange($currentStats['maintenance_cost'], $lastStats['maintenance_cost']);
            $parkingChange = $this->calculatePercentageChange($currentStats['parking_cost'], $lastStats['parking_cost']);
            $fnbChange = $this->calculatePercentageChange($currentStats['fnb_cost'], $lastStats['fnb_cost']);
            $accommodationChange = $this->calculatePercentageChange($currentStats['accommodation_cost'], $lastStats['accommodation_cost']);
            $othersChange = $this->calculatePercentageChange($currentStats['others_cost'], $lastStats['others_cost']);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total_trips' => $currentStats['total_trips'],
                    'total_trips_change' => $tripsChange,
                    
                    'total_distance' => $currentStats['total_distance'],
                    'total_distance_change' => $distanceChange,
                    
                    'fuel_cost' => $currentStats['fuel_cost'],
                    'fuel_cost_change' => $fuelCostChange,
                    
                    'maintenance_cost' => $currentStats['maintenance_cost'],
                    'maintenance_change' => $maintenanceChange,
                    
                    'parking_cost' => $currentStats['parking_cost'],
                    'parking_change' => $parkingChange,
                    
                    'fnb_cost' => $currentStats['fnb_cost'],
                    'fnb_change' => $fnbChange,
                    
                    'accommodation_cost' => $currentStats['accommodation_cost'],
                    'accommodation_change' => $accommodationChange,
                    
                    'others_cost' => $currentStats['others_cost'],
                    'others_change' => $othersChange,
                    
                    'current_month' => $currentMonthStart->format('F Y'),
                    'last_month' => $lastMonthStart->format('F Y'),
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ralat mendapatkan statistik dashboard',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get statistics for a specific month
     */
    private function getMonthStats($userId, $startDate, $endDate)
    {
        // Total trips (completed journeys)
        $totalTrips = LogPemandu::where('pemandu_id', $userId)
            ->where('status', 'selesai')
            ->whereBetween('tarikh_perjalanan', [$startDate, $endDate])
            ->count();
        
        // Total distance
        $totalDistance = LogPemandu::where('pemandu_id', $userId)
            ->where('status', 'selesai')
            ->whereBetween('tarikh_perjalanan', [$startDate, $endDate])
            ->sum('jarak') ?? 0;
        
        // Fuel cost (COMBINED: from log_pemandu + fuel claims)
        // 1. Direct fuel cost from End Journey
        $fuelCostDirect = LogPemandu::where('pemandu_id', $userId)
            ->where('status', 'selesai')
            ->whereBetween('tarikh_perjalanan', [$startDate, $endDate])
            ->sum('kos_minyak') ?? 0;
        
        // 2. Fuel cost from Claims (kategori='fuel')
        $fuelCostClaims = Tuntutan::whereHas('logPemandu', function($query) use ($userId, $startDate, $endDate) {
                $query->where('pemandu_id', $userId)
                      ->whereBetween('tarikh_perjalanan', [$startDate, $endDate]);
            })
            ->where('kategori', 'fuel')
            ->whereIn('status', ['pending', 'diluluskan'])
            ->sum('jumlah') ?? 0;
        
        // TOTAL Fuel Cost = Direct + Claims
        $fuelCost = $fuelCostDirect + $fuelCostClaims;
        
        // Maintenance cost (from tuntutan with category 'car_maintenance')
        $maintenanceCost = Tuntutan::whereHas('logPemandu', function($query) use ($userId, $startDate, $endDate) {
                $query->where('pemandu_id', $userId)
                      ->whereBetween('tarikh_perjalanan', [$startDate, $endDate]);
            })
            ->where('kategori', 'car_maintenance')
            ->whereIn('status', ['pending', 'diluluskan'])
            ->sum('jumlah') ?? 0;
        
        // Parking cost (from tuntutan with category 'parking')
        $parkingCost = Tuntutan::whereHas('logPemandu', function($query) use ($userId, $startDate, $endDate) {
                $query->where('pemandu_id', $userId)
                      ->whereBetween('tarikh_perjalanan', [$startDate, $endDate]);
            })
            ->where('kategori', 'parking')
            ->whereIn('status', ['pending', 'diluluskan'])
            ->sum('jumlah') ?? 0;
        
        // F&B cost (from tuntutan with category 'f&b')
        $fnbCost = Tuntutan::whereHas('logPemandu', function($query) use ($userId, $startDate, $endDate) {
                $query->where('pemandu_id', $userId)
                      ->whereBetween('tarikh_perjalanan', [$startDate, $endDate]);
            })
            ->where('kategori', 'f&b')
            ->whereIn('status', ['pending', 'diluluskan'])
            ->sum('jumlah') ?? 0;
        
        // Accommodation cost (from tuntutan with category 'accommodation')
        $accommodationCost = Tuntutan::whereHas('logPemandu', function($query) use ($userId, $startDate, $endDate) {
                $query->where('pemandu_id', $userId)
                      ->whereBetween('tarikh_perjalanan', [$startDate, $endDate]);
            })
            ->where('kategori', 'accommodation')
            ->whereIn('status', ['pending', 'diluluskan'])
            ->sum('jumlah') ?? 0;
        
        // Others cost (from tuntutan with category 'others')
        $othersCost = Tuntutan::whereHas('logPemandu', function($query) use ($userId, $startDate, $endDate) {
                $query->where('pemandu_id', $userId)
                      ->whereBetween('tarikh_perjalanan', [$startDate, $endDate]);
            })
            ->where('kategori', 'others')
            ->whereIn('status', ['pending', 'diluluskan'])
            ->sum('jumlah') ?? 0;
        
        return [
            'total_trips' => $totalTrips,
            'total_distance' => round($totalDistance, 2),
            'fuel_cost' => round($fuelCost, 2),
            'maintenance_cost' => round($maintenanceCost, 2),
            'parking_cost' => round($parkingCost, 2),
            'fnb_cost' => round($fnbCost, 2),
            'accommodation_cost' => round($accommodationCost, 2),
            'others_cost' => round($othersCost, 2),
        ];
    }
    
    /**
     * Calculate percentage change between two values
     */
    private function calculatePercentageChange($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        
        $change = (($current - $previous) / $previous) * 100;
        return round($change, 1);
    }
}

