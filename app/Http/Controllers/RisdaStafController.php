<?php

namespace App\Http\Controllers;

use App\Models\RisdaStaf;
use App\Models\RisdaBahagian;
use App\Models\RisdaStesen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RisdaStafController extends Controller
{
    /**
     * Display a listing of the resource with organizational filtering.
     */
    public function index(Request $request)
    {
        $currentUser = auth()->user();
        
        $query = RisdaStaf::with(['bahagian', 'stesen']);

        // Apply organizational scope with hierarchy
        if (!$this->isAdministrator()) {
            $this->applyOrganizationalScope($query, $currentUser);
        }

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_penuh', 'like', "%{$search}%")
                  ->orWhere('no_pekerja', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('jawatan', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $stafs = $query->orderBy('created_at', 'desc')->paginate(10);

        // Return different view based on user type
        if ($this->isAdministrator()) {
            // Admin uses the main senarai-risda view (with tabs)
            return view('pengurusan.senarai-risda', compact('stafs'));
        }

        // Non-admin uses dedicated senarai-staf view
        return view('pengurusan.senarai-staf', compact('stafs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $currentUser = auth()->user();
        
        // Get bahagians based on user access
        $bahagians = $this->getBahagianForCurrentUser();
        
        // Get stesens based on user access
        $stesens = $this->getStesenForCurrentUser();

        return view('pengurusan.tambah-staf', compact('bahagians', 'stesens'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'no_pekerja' => 'required|string|max:20|unique:risda_stafs,no_pekerja',
            'nama_penuh' => 'required|string|max:255',
            'no_kad_pengenalan' => 'required|string|size:14|unique:risda_stafs,no_kad_pengenalan',
            'jantina' => 'required|in:lelaki,perempuan',
            'bahagian_id' => 'required|exists:risda_bahagians,id',
            'stesen_id' => 'nullable|exists:risda_stesens,id',
            'jawatan' => 'required|string|max:255',
            'no_telefon' => 'required|string|max:20',
            'email' => 'required|email|max:255|unique:risda_stafs,email',
            'no_fax' => 'nullable|string|max:20',
            'status' => 'required|in:aktif,tidak_aktif,gantung',
            'alamat_1' => 'required|string|max:255',
            'alamat_2' => 'nullable|string|max:255',
            'poskod' => 'required|string|size:5',
            'bandar' => 'required|string|max:255',
            'negeri' => 'required|string|max:255',
            'negara' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $staf = RisdaStaf::create($request->all());

        activity('risda')
            ->performedOn($staf)
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'entity' => 'staf',
                'no_pekerja' => $staf->no_pekerja,
                'nama_penuh' => $staf->nama_penuh,
                'bahagian' => optional($staf->bahagian)->nama_bahagian,
                'stesen' => optional($staf->stesen)->nama_stesen,
                'jawatan' => $staf->jawatan,
                'status' => $staf->status,
            ])
            ->event('created')
            ->log("Staf '{$staf->nama_penuh}' telah ditambah");

        // Redirect based on user type
        $redirectRoute = $this->isAdministrator() 
            ? 'pengurusan.senarai-risda' 
            : 'pengurusan.senarai-staf';

        return redirect()->route($redirectRoute)
            ->with('success', 'RISDA Staf berjaya ditambah!');
    }

    /**
     * Display the specified resource.
     */
    public function show(RisdaStaf $risdaStaf)
    {
        // Check access permission
        $this->checkStafAccess($risdaStaf);
        
        $risdaStaf->load(['bahagian', 'stesen']);
        return view('pengurusan.show-staf', compact('risdaStaf'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RisdaStaf $risdaStaf)
    {
        // Check access permission
        $this->checkStafAccess($risdaStaf);
        
        // Get bahagians based on user access
        $bahagians = $this->getBahagianForCurrentUser();
        
        // Get stesens based on user access
        $stesens = $this->getStesenForCurrentUser();

        return view('pengurusan.edit-staf', compact('risdaStaf', 'bahagians', 'stesens'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RisdaStaf $risdaStaf)
    {
        $validator = Validator::make($request->all(), [
            'no_pekerja' => 'required|string|max:20|unique:risda_stafs,no_pekerja,' . $risdaStaf->id,
            'nama_penuh' => 'required|string|max:255',
            'no_kad_pengenalan' => 'required|string|size:14|unique:risda_stafs,no_kad_pengenalan,' . $risdaStaf->id,
            'jantina' => 'required|in:lelaki,perempuan',
            'bahagian_id' => 'required|exists:risda_bahagians,id',
            'stesen_id' => 'nullable|exists:risda_stesens,id',
            'jawatan' => 'required|string|max:255',
            'no_telefon' => 'required|string|max:20',
            'email' => 'required|email|max:255|unique:risda_stafs,email,' . $risdaStaf->id,
            'no_fax' => 'nullable|string|max:20',
            'status' => 'required|in:aktif,tidak_aktif,gantung',
            'alamat_1' => 'required|string|max:255',
            'alamat_2' => 'nullable|string|max:255',
            'poskod' => 'required|string|size:5',
            'bandar' => 'required|string|max:255',
            'negeri' => 'required|string|max:255',
            'negara' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $old = $risdaStaf->only(['no_pekerja','nama_penuh','bahagian_id','stesen_id','jawatan','email','no_telefon','status']);
        $risdaStaf->update($request->all());

        $changes = [];
        foreach ($old as $k => $v) {
            if ($v != $risdaStaf->$k) {
                $changes[$k] = ['old' => $v, 'new' => $risdaStaf->$k];
            }
        }

        activity('risda')
            ->performedOn($risdaStaf)
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'entity' => 'staf',
                'no_pekerja' => $risdaStaf->no_pekerja,
                'nama_penuh' => $risdaStaf->nama_penuh,
                'changes' => $changes,
            ])
            ->event('updated')
            ->log("Staf '{$risdaStaf->nama_penuh}' telah dikemaskini (" . count($changes) . " medan diubah)");

        // Redirect based on user type
        $redirectRoute = $this->isAdministrator() 
            ? 'pengurusan.senarai-risda' 
            : 'pengurusan.senarai-staf';

        return redirect()->route($redirectRoute)
            ->with('success', 'RISDA Staf berjaya dikemaskini!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RisdaStaf $risdaStaf)
    {
        // Check access permission
        $this->checkStafAccess($risdaStaf);
        
        $id = $risdaStaf->id;
        $name = $risdaStaf->nama_penuh;
        $risdaStaf->delete();

        activity('risda')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'entity' => 'staf',
                'staf_id' => $id,
                'nama_penuh' => $name,
            ])
            ->event('deleted')
            ->log("Staf '{$name}' telah dipadam");

        // Redirect based on user type
        $redirectRoute = $this->isAdministrator() 
            ? 'pengurusan.senarai-risda' 
            : 'pengurusan.senarai-staf';

        return redirect()->route($redirectRoute)
            ->with('success', 'RISDA Staf berjaya dipadam!');
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
     * Get stesen IDs for a bahagian with optional filtering by stesen_akses_ids.
     */
    private function getStesenIdsForBahagian($bahagianId, $stesenAksesIds = null)
    {
        $userStesenIds = collect($stesenAksesIds ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter();

        if ($userStesenIds->isNotEmpty()) {
            return $userStesenIds;
        }

        return RisdaStesen::where('risda_bahagian_id', $bahagianId)
            ->pluck('id');
    }

    /**
     * Apply organizational scope to query with hierarchy support.
     */
    private function applyOrganizationalScope($query, $currentUser)
    {
        if ($currentUser->jenis_organisasi === 'bahagian') {
            $stesenIds = $this->getStesenIdsForBahagian($currentUser->organisasi_id, $currentUser->stesen_akses_ids);
            
            $query->where(function ($q) use ($currentUser, $stesenIds) {
                // Staf directly under bahagian (no stesen)
                $q->where(function ($inner) use ($currentUser) {
                    $inner->where('bahagian_id', $currentUser->organisasi_id)
                          ->whereNull('stesen_id');
                });
                
                // Staf under any stesen in this bahagian
                if ($stesenIds->isNotEmpty()) {
                    $q->orWhereIn('stesen_id', $stesenIds->all());
                }
            });
        } elseif ($currentUser->jenis_organisasi === 'stesen') {
            $query->where('stesen_id', $currentUser->organisasi_id);
        }
    }

    /**
     * Check if current user has access to the staf with hierarchy support.
     */
    private function checkStafAccess(RisdaStaf $staf)
    {
        $currentUser = auth()->user();

        if ($this->isAdministrator()) {
            return;
        }

        if ($currentUser->jenis_organisasi === 'bahagian') {
            $stesenIds = $this->getStesenIdsForBahagian($currentUser->organisasi_id, $currentUser->stesen_akses_ids);
            
            // Allow if staf is directly under bahagian
            if ($staf->bahagian_id == $currentUser->organisasi_id && !$staf->stesen_id) {
                return;
            }
            
            // Allow if staf is under any stesen in this bahagian
            if ($staf->stesen_id && $stesenIds->contains($staf->stesen_id)) {
                return;
            }
            
            abort(403, 'Anda tidak mempunyai kebenaran untuk mengakses staf ini.');
        }

        if ($currentUser->jenis_organisasi === 'stesen') {
            if ($staf->stesen_id != $currentUser->organisasi_id) {
                abort(403, 'Anda tidak mempunyai kebenaran untuk mengakses staf ini.');
            }
        }
    }

    /**
     * Get bahagian list for current user.
     */
    private function getBahagianForCurrentUser()
    {
        $currentUser = auth()->user();
        
        if ($this->isAdministrator()) {
            return RisdaBahagian::where('status_dropdown', 'aktif')
                               ->orderBy('nama_bahagian')
                               ->get();
        }

        if ($currentUser->jenis_organisasi === 'bahagian') {
            return RisdaBahagian::where('id', $currentUser->organisasi_id)
                               ->where('status_dropdown', 'aktif')
                               ->get();
        }

        if ($currentUser->jenis_organisasi === 'stesen') {
            $stesen = RisdaStesen::find($currentUser->organisasi_id);
            if ($stesen) {
                return RisdaBahagian::where('id', $stesen->risda_bahagian_id)
                                   ->where('status_dropdown', 'aktif')
                                   ->get();
            }
        }

        return collect();
    }

    /**
     * Get stesen list for current user.
     */
    private function getStesenForCurrentUser()
    {
        $currentUser = auth()->user();
        
        if ($this->isAdministrator()) {
            return RisdaStesen::where('status_dropdown', 'aktif')
                             ->orderBy('nama_stesen')
                             ->get();
        }

        if ($currentUser->jenis_organisasi === 'bahagian') {
            $stesenIds = $this->getStesenIdsForBahagian($currentUser->organisasi_id, $currentUser->stesen_akses_ids);
            
            return RisdaStesen::whereIn('id', $stesenIds->all())
                             ->where('status_dropdown', 'aktif')
                             ->orderBy('nama_stesen')
                             ->get();
        }

        if ($currentUser->jenis_organisasi === 'stesen') {
            return RisdaStesen::where('id', $currentUser->organisasi_id)
                             ->where('status_dropdown', 'aktif')
                             ->get();
        }

        return collect();
    }
}
