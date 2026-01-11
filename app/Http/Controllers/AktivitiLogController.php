<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\AuditTrail;
use App\Models\User;
use App\Services\AuditTrailService;
use Carbon\Carbon;

class AktivitiLogController extends Controller
{
    protected AuditTrailService $auditTrailService;

    public function __construct(AuditTrailService $auditTrailService)
    {
        $this->auditTrailService = $auditTrailService;
    }

    /**
     * Display a listing of activity logs
     */
    public function index(Request $request)
    {
        $currentUser = auth()->user();
        $isAdmin = $currentUser->jenis_organisasi === 'semua';
        
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

        // Get users list for audit trail dropdown (admin only)
        $users = $isAdmin ? User::orderBy('name')->get(['id', 'name', 'email']) : collect();

        // Get audit trail data if requested (admin only)
        $auditTrails = null;
        $selectedUser = null;
        $dateFrom = null;
        $dateTo = null;

        if ($isAdmin && $request->filled('audit_user_id')) {
            $selectedUser = User::find($request->audit_user_id);
            $dateFrom = $request->filled('date_from') 
                ? Carbon::parse($request->date_from) 
                : now()->subDays(7);
            $dateTo = $request->filled('date_to') 
                ? Carbon::parse($request->date_to) 
                : now();

            $auditTrails = $this->auditTrailService->getAuditTrailPaginated(
                $request->audit_user_id,
                $dateFrom,
                $dateTo,
                10
            );
        }

        return view('pengurusan.aktiviti-log', compact(
            'activities',
            'isAdmin',
            'users',
            'auditTrails',
            'selectedUser',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Generate audit trail report for a specific user
     */
    public function generateAuditTrail(Request $request)
    {
        $currentUser = auth()->user();
        
        // Only admin can access
        if ($currentUser->jenis_organisasi !== 'semua') {
            abort(403, 'Akses ditolak');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        $user = User::findOrFail($request->user_id);
        $dateFrom = Carbon::parse($request->date_from);
        $dateTo = Carbon::parse($request->date_to);

        $auditTrails = $this->auditTrailService->getAuditTrailPaginated(
            $request->user_id,
            $dateFrom,
            $dateTo,
            10
        );

        $users = User::orderBy('name')->get(['id', 'name', 'email']);

        return view('pengurusan.aktiviti-log', [
            'activities' => Activity::with(['causer', 'subject'])
                ->orderBy('created_at', 'desc')
                ->paginate(5),
            'isAdmin' => true,
            'users' => $users,
            'auditTrails' => $auditTrails,
            'selectedUser' => $user,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'activeTab' => 'audit-trail',
        ]);
    }

    /**
     * Export audit trail to PDF
     */
    public function exportAuditTrailPdf(Request $request)
    {
        $currentUser = auth()->user();
        
        // Only admin can access
        if ($currentUser->jenis_organisasi !== 'semua') {
            abort(403, 'Akses ditolak');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        $user = User::findOrFail($request->user_id);
        $dateFrom = Carbon::parse($request->date_from);
        $dateTo = Carbon::parse($request->date_to);

        $auditTrails = $this->auditTrailService->getAuditTrail(
            $request->user_id,
            $dateFrom,
            $dateTo
        );

        $pdf = \PDF::loadView('pengurusan.audit-trail-pdf', [
            'user' => $user,
            'auditTrails' => $auditTrails,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'generatedAt' => now(),
            'generatedBy' => $currentUser,
        ]);

        $filename = 'audit-trail-' . $user->id . '-' . $dateFrom->format('Ymd') . '-' . $dateTo->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

}
