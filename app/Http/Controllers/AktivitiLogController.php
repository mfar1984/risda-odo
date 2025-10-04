<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;

class AktivitiLogController extends Controller
{
    /**
     * Display a listing of activity logs
     */
    public function index(Request $request)
    {
        $currentUser = auth()->user();
        
        $query = Activity::with(['causer', 'subject'])
            ->orderBy('created_at', 'desc');

        // Multi-Tenancy: Filter activity logs based on user's organization
        if ($currentUser->jenis_organisasi === 'semua') {
            // Administrator - can see ALL activity logs
            // No filter needed
        } elseif ($currentUser->jenis_organisasi === 'bahagian') {
            // Bahagian user - only see logs from their bahagian
            $query->whereHas('causer', function($q) use ($currentUser) {
                $q->where('jenis_organisasi', 'bahagian')
                  ->where('organisasi_id', $currentUser->organisasi_id);
            });
        } elseif ($currentUser->jenis_organisasi === 'stesen') {
            // Stesen user - only see logs from their stesen
            $query->whereHas('causer', function($q) use ($currentUser) {
                $q->where('jenis_organisasi', 'stesen')
                  ->where('organisasi_id', $currentUser->organisasi_id);
            });
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('log_name', 'like', "%{$search}%")
                  ->orWhere('subject_type', 'like', "%{$search}%");
            });
        }

        // Log name filter (created, updated, deleted, etc)
        if ($request->filled('log_name')) {
            $query->where('log_name', $request->log_name);
        }

        // Event filter
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        // Pagination: 5 per page (same as log-pemandu)
        $activities = $query->paginate(5);

        return view('pengurusan.aktiviti-log', compact('activities'));
    }

}
