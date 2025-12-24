<?php

namespace App\Http\Controllers;

use App\Models\SelenggaraKenderaan;
use App\Models\Kenderaan;
use App\Models\KategoriKosSelenggara;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class SelenggaraKenderaanController extends Controller
{
    /**
     * Display a listing of the maintenance records.
     */
    public function index(Request $request)
    {
        $currentUser = auth()->user();

        // Start building query
        $query = SelenggaraKenderaan::with(['kenderaan', 'kategoriKos', 'pelaksana'])
            ->forCurrentUser($currentUser);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('kenderaan', function($q) use ($search) {
                $q->where('no_plat', 'like', "%{$search}%")
                  ->orWhere('jenama', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('kategori_kos_id')) {
            $query->where('kategori_kos_id', $request->kategori_kos_id);
        }

        if ($request->filled('tarikh_dari')) {
            $query->where('tarikh_mula', '>=', $request->tarikh_dari);
        }

        if ($request->filled('tarikh_hingga')) {
            $query->where('tarikh_selesai', '<=', $request->tarikh_hingga);
        }

        // Paginate results
        $selenggaraRecords = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Get categories for filter dropdown
        $kategoriList = KategoriKosSelenggara::aktif()->get();

        return view('pengurusan.senarai-selenggara', compact('selenggaraRecords', 'kategoriList'));
    }

    /**
     * Check if current user is Administrator.
     */
    private function isAdministrator()
    {
        $user = auth()->user();
        return $user && $user->jenis_organisasi === 'semua';
    }

    /**
     * Show the form for creating a new maintenance record.
     */
    public function create(Request $request)
    {
        $kenderaanId = $request->query('kenderaan_id');
        $kenderaan = null;

        if ($kenderaanId) {
            $kenderaan = Kenderaan::findOrFail($kenderaanId);
            // Ensure user has access to this vehicle
            $this->ensureVehicleAccessible($kenderaan);
        }

        // Get active cost categories
        $kategoriList = KategoriKosSelenggara::aktif()->orderBy('nama_kategori')->get();

        return view('pengurusan.tambah-selenggara', compact('kenderaan', 'kategoriList'));
    }

    /**
     * Store a newly created maintenance record in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kenderaan_id' => 'required|exists:kenderaans,id',
            'kategori_kos_id' => 'required|exists:kategori_kos_selenggara,id',
            'tarikh_mula' => 'required|date',
            'tarikh_selesai' => 'required|date|after_or_equal:tarikh_mula',
            'jumlah_kos' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:1000',
            'tukar_minyak' => 'nullable|boolean',
            'jangka_hayat_km' => 'nullable|integer|min:1000|max:50000|required_if:tukar_minyak,1',
            'fail_invois' => 'nullable|file|mimes:pdf,jpeg,jpg,png|max:10240', // 10MB
            'status' => 'required|in:dijadualkan,dalam_proses,selesai',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $currentUser = auth()->user();
        $kenderaan = Kenderaan::findOrFail($request->kenderaan_id);
        
        // Ensure user has access to this vehicle
        $this->ensureVehicleAccessible($kenderaan);

        $data = $request->all();
        $data['dilaksana_oleh'] = $currentUser->id;
        $data['tukar_minyak'] = $request->has('tukar_minyak');

        // Set organizational data based on current user
        if ($currentUser->jenis_organisasi === 'semua') {
            $data['jenis_organisasi'] = 'semua';
            $data['organisasi_id'] = null;
        } elseif ($currentUser->jenis_organisasi === 'bahagian') {
            $data['jenis_organisasi'] = 'bahagian';
            $data['organisasi_id'] = $currentUser->organisasi_id;
        } elseif ($currentUser->jenis_organisasi === 'stesen') {
            $data['jenis_organisasi'] = 'stesen';
            $data['organisasi_id'] = $currentUser->organisasi_id;
        }

        // Handle file upload
        $invoiceUploaded = false;
        if ($request->hasFile('fail_invois')) {
            $file = $request->file('fail_invois');
            $path = $file->store('selenggara-invois', 'public');
            $data['fail_invois'] = $path;
            $invoiceUploaded = true;
        }

        $selenggara = SelenggaraKenderaan::create($data);

        // Log activity
        activity()
            ->performedOn($selenggara)
            ->causedBy($currentUser)
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'kenderaan_id' => $selenggara->kenderaan_id,
                'kenderaan_no_plat' => $kenderaan->no_plat,
                'kategori_kos' => $selenggara->kategoriKos->nama_kategori ?? 'N/A',
                'jumlah_kos' => $selenggara->jumlah_kos,
                'status' => $selenggara->status,
                'tarikh_mula' => $selenggara->tarikh_mula,
                'tarikh_selesai' => $selenggara->tarikh_selesai,
                'tukar_minyak' => $selenggara->tukar_minyak,
                'invoice_uploaded' => $invoiceUploaded,
            ])
            ->event('created')
            ->log("Rekod penyelenggaraan untuk kenderaan '{$kenderaan->no_plat}' telah dicipta (RM {$selenggara->jumlah_kos})");

        return redirect()->route('pengurusan.senarai-selenggara')
            ->with('success', 'Rekod penyelenggaraan kenderaan berjaya disimpan.');
    }

    /**
     * Display the specified maintenance record.
     */
    public function show(SelenggaraKenderaan $selenggara)
    {
        $this->ensureMaintenanceAccessible($selenggara);
        
        $selenggara->load(['kenderaan', 'kategoriKos', 'pelaksana']);
        return view('pengurusan.show-selenggara', compact('selenggara'));
    }

    /**
     * Show the form for editing the specified maintenance record.
     */
    public function edit(SelenggaraKenderaan $selenggara)
    {
        $this->ensureMaintenanceAccessible($selenggara);
        
        $selenggara->load(['kenderaan', 'kategoriKos']);
        $kategoriList = KategoriKosSelenggara::aktif()->orderBy('nama_kategori')->get();

        return view('pengurusan.edit-selenggara', compact('selenggara', 'kategoriList'));
    }

    /**
     * Update the specified maintenance record in storage.
     */
    public function update(Request $request, SelenggaraKenderaan $selenggara)
    {
        $this->ensureMaintenanceAccessible($selenggara);

        $validator = Validator::make($request->all(), [
            'kategori_kos_id' => 'required|exists:kategori_kos_selenggara,id',
            'tarikh_mula' => 'required|date',
            'tarikh_selesai' => 'required|date|after_or_equal:tarikh_mula',
            'jumlah_kos' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:1000',
            'tukar_minyak' => 'nullable|boolean',
            'jangka_hayat_km' => 'nullable|integer|min:1000|max:50000|required_if:tukar_minyak,1',
            'fail_invois' => 'nullable|file|mimes:pdf,jpeg,jpg,png|max:10240',
            'status' => 'required|in:dijadualkan,dalam_proses,selesai',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Store old values for logging
        $oldStatus = $selenggara->status;
        $oldJumlahKos = $selenggara->jumlah_kos;
        $oldKategoriKosId = $selenggara->kategori_kos_id;

        $data = $request->all();
        $data['tukar_minyak'] = $request->has('tukar_minyak');

        // Handle file upload
        $newInvoiceUploaded = false;
        if ($request->hasFile('fail_invois')) {
            // Delete old file if exists
            if ($selenggara->fail_invois) {
                Storage::disk('public')->delete($selenggara->fail_invois);
            }
            
            $file = $request->file('fail_invois');
            $path = $file->store('selenggara-invois', 'public');
            $data['fail_invois'] = $path;
            $newInvoiceUploaded = true;
        }

        $selenggara->update($data);

        // Detect changes
        $changes = [];
        if ($oldStatus != $selenggara->status) {
            $changes['status'] = ['old' => $oldStatus, 'new' => $selenggara->status];
        }
        if ($oldJumlahKos != $selenggara->jumlah_kos) {
            $changes['jumlah_kos'] = ['old' => $oldJumlahKos, 'new' => $selenggara->jumlah_kos];
        }
        if ($oldKategoriKosId != $selenggara->kategori_kos_id) {
            $changes['kategori_kos_id'] = ['old' => $oldKategoriKosId, 'new' => $selenggara->kategori_kos_id];
        }

        // Log activity
        activity()
            ->performedOn($selenggara)
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'kenderaan_no_plat' => $selenggara->kenderaan->no_plat ?? 'N/A',
                'changes' => $changes,
                'invoice_updated' => $newInvoiceUploaded,
                'total_changes' => count($changes) + ($newInvoiceUploaded ? 1 : 0),
            ])
            ->event('updated')
            ->log("Rekod penyelenggaraan untuk kenderaan '{$selenggara->kenderaan->no_plat}' telah dikemaskini (" . (count($changes) + ($newInvoiceUploaded ? 1 : 0)) . " medan diubah)");

        return redirect()->route('pengurusan.senarai-selenggara')
            ->with('success', 'Rekod penyelenggaraan kenderaan berjaya dikemaskini.');
    }

    /**
     * Remove the specified maintenance record from storage.
     */
    public function destroy(SelenggaraKenderaan $selenggara)
    {
        $this->ensureMaintenanceAccessible($selenggara);

        // Store data for logging before deletion
        $selenggaraId = $selenggara->id;
        $kenderaanNoPlat = $selenggara->kenderaan->no_plat ?? 'N/A';
        $kategoriKos = $selenggara->kategoriKos->nama_kategori ?? 'N/A';
        $jumlahKos = $selenggara->jumlah_kos;
        $status = $selenggara->status;
        $tarikhMula = $selenggara->tarikh_mula;
        $invoiceExists = !empty($selenggara->fail_invois);

        // Delete associated file if exists
        if ($selenggara->fail_invois) {
            Storage::disk('public')->delete($selenggara->fail_invois);
        }

        $selenggara->delete();

        // Log activity
        activity()
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'selenggara_id' => $selenggaraId,
                'kenderaan_no_plat' => $kenderaanNoPlat,
                'kategori_kos' => $kategoriKos,
                'jumlah_kos' => $jumlahKos,
                'status' => $status,
                'tarikh_mula' => $tarikhMula,
                'invoice_deleted' => $invoiceExists,
            ])
            ->event('deleted')
            ->log("Rekod penyelenggaraan untuk kenderaan '{$kenderaanNoPlat}' telah dipadam (RM {$jumlahKos})");

        return redirect()->route('pengurusan.senarai-selenggara')
            ->with('success', 'Rekod penyelenggaraan kenderaan berjaya dipadam.');
    }

    /**
     * Ensure the current user has access to the maintenance record.
     */
    private function ensureMaintenanceAccessible(SelenggaraKenderaan $selenggara)
    {
        $currentUser = auth()->user();

        // Administrator can access all
        if ($this->isAdministrator()) {
            return;
        }

        // Check organizational access
        if ($selenggara->jenis_organisasi === 'semua') {
            // All users can see records marked as 'semua'
            return;
        }

        if ($currentUser->jenis_organisasi === 'bahagian') {
            $stesenIds = $this->getStesenIdsForBahagian($currentUser->organisasi_id, $currentUser->stesen_akses_ids);

            // Allow if maintenance record belongs to same bahagian
            if ($selenggara->jenis_organisasi === 'bahagian' && 
                $selenggara->organisasi_id == $currentUser->organisasi_id) {
                return;
            }

            // Allow if maintenance record belongs to any stesen under this bahagian
            if ($selenggara->jenis_organisasi === 'stesen' && 
                $stesenIds->contains((int) $selenggara->organisasi_id)) {
                return;
            }
        } elseif ($currentUser->jenis_organisasi === 'stesen') {
            if ($selenggara->jenis_organisasi === 'stesen' && 
                $selenggara->organisasi_id == $currentUser->organisasi_id) {
                return;
            }
            
            // Also allow access to bahagian records if stesen belongs to that bahagian
            $stesen = \App\Models\RisdaStesen::find($currentUser->organisasi_id);
            if ($stesen && $stesen->bahagian_id && 
                $selenggara->jenis_organisasi === 'bahagian' && 
                $selenggara->organisasi_id == $stesen->bahagian_id) {
                return;
            }
        }

        abort(403, 'ANDA TIDAK MEMPUNYAI AKSES KEPADA REKOD PENYELENGGARAAN INI.');
    }

    /**
     * Get stesen IDs for a bahagian. If stesen_akses_ids is empty, returns ALL stesen under bahagian.
     */
    private function getStesenIdsForBahagian($bahagianId, $stesenAksesIds = null)
    {
        $userStesenIds = collect($stesenAksesIds ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter();

        if ($userStesenIds->isNotEmpty()) {
            return $userStesenIds;
        }

        return \App\Models\RisdaStesen::where('risda_bahagian_id', $bahagianId)->pluck('id');
    }

    /**
     * Ensure the current user has access to the vehicle.
     */
    private function ensureVehicleAccessible(Kenderaan $kenderaan)
    {
        $currentUser = auth()->user();

        // Administrator can access all vehicles
        if ($this->isAdministrator()) {
            return;
        }

        // Check organizational access based on vehicle's bahagian/stesen
        if ($currentUser->jenis_organisasi === 'bahagian') {
            $stesenIds = $this->getStesenIdsForBahagian($currentUser->organisasi_id, $currentUser->stesen_akses_ids);

            // Allow if vehicle belongs to same bahagian
            if ($kenderaan->bahagian_id == $currentUser->organisasi_id) {
                return;
            }

            // Allow if vehicle belongs to any stesen under this bahagian
            if ($kenderaan->stesen_id && $stesenIds->contains((int) $kenderaan->stesen_id)) {
                return;
            }
        } elseif ($currentUser->jenis_organisasi === 'stesen') {
            if ($kenderaan->stesen_id == $currentUser->organisasi_id) {
                return;
            }
            
            // Also allow if vehicle belongs to the same bahagian
            $stesen = \App\Models\RisdaStesen::find($currentUser->organisasi_id);
            if ($stesen && $stesen->bahagian_id && 
                $kenderaan->bahagian_id == $stesen->bahagian_id && 
                !$kenderaan->stesen_id) {
                return;
            }
        }

        abort(403, 'ANDA TIDAK MEMPUNYAI AKSES KEPADA KENDERAAN INI.');
    }
}