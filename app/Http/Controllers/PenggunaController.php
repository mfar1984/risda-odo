<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\RisdaStaf;
use App\Models\UserGroup;
use App\Models\RisdaBahagian;
use App\Models\RisdaStesen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class PenggunaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $penggunas = User::with(['kumpulan'])
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('pengurusan.senarai-pengguna', compact('penggunas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $stafs = RisdaStaf::where('status', 'aktif')
                         ->orderBy('nama_penuh')
                         ->get();

        $kumpulans = UserGroup::where('status', 'aktif')
                             ->orderBy('nama_kumpulan')
                             ->get();

        $bahagians = RisdaBahagian::where('status_dropdown', 'aktif')
                                 ->orderBy('nama_bahagian')
                                 ->get();

        $stesens = RisdaStesen::where('status_dropdown', 'aktif')
                             ->orderBy('nama_stesen')
                             ->get();

        // Check if current user is Administrator (can access all bahagians)
        $isAdministrator = $this->isAdministrator();

        return view('pengurusan.tambah-pengguna', compact('stafs', 'kumpulans', 'bahagians', 'stesens', 'isAdministrator'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'staf_id' => 'required|exists:risda_stafs,id',
            'password' => 'required|string|min:8|confirmed',
            'kumpulan_id' => 'nullable|exists:user_groups,id',
            'bahagian_akses_id' => 'nullable|exists:risda_bahagians,id',
            'stesen_akses_ids' => 'nullable|string',
            'status_akaun' => 'required|in:aktif,tidak_aktif,digantung',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Get staf data
        $staf = RisdaStaf::findOrFail($request->staf_id);

        // Check if user already exists for this staf
        $existingUser = User::where('email', $staf->email)->first();
        if ($existingUser) {
            return redirect()->back()
                ->withErrors(['staf_id' => 'Pengguna untuk staf ini sudah wujud.'])
                ->withInput();
        }

        // Determine organisation type and ID
        $jenisOrganisasi = null;
        $organisasiId = null;
        $stesenAksesIds = null;

        // Parse stesen akses IDs from JSON (RISDA Pattern)
        if ($request->stesen_akses_ids) {
            $stesenAksesIds = json_decode($request->stesen_akses_ids, true);
        }

        // Determine jenis_organisasi and organisasi_id
        if ($request->bahagian_akses_id === 'semua') {
            $jenisOrganisasi = 'semua';
            $organisasiId = null;
            // If "Semua Stesen" selected, set stesen_akses_ids to null for all access
            if (!empty($stesenAksesIds) && in_array('semua', $stesenAksesIds)) {
                $stesenAksesIds = null;
            }
        } elseif (!empty($stesenAksesIds)) {
            // Check if "Semua Stesen" selected
            if (in_array('semua', $stesenAksesIds)) {
                $jenisOrganisasi = 'semua';
                $organisasiId = null;
                $stesenAksesIds = null; // Null means access to all stesen
            } else {
                $jenisOrganisasi = 'stesen';
                // For backward compatibility, set organisasi_id to first stesen
                $organisasiId = $stesenAksesIds[0];
            }
        } elseif ($request->bahagian_akses_id) {
            $jenisOrganisasi = 'bahagian';
            $organisasiId = $request->bahagian_akses_id;
        }
        // If all are null, user has access to all organisations

        // Create user
        User::create([
            'name' => $staf->nama_penuh,
            'email' => $staf->email,
            'password' => $request->password,
            'kumpulan_id' => $request->kumpulan_id,
            'jenis_organisasi' => $jenisOrganisasi,
            'organisasi_id' => $organisasiId,
            'stesen_akses_ids' => $stesenAksesIds,
            'status' => $request->status_akaun,
        ]);

        return redirect()->route('pengurusan.senarai-pengguna')
            ->with('success', 'Pengguna berjaya ditambah!');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $pengguna)
    {
        $pengguna->load(['kumpulan']);
        return view('pengurusan.show-pengguna', compact('pengguna'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $pengguna)
    {
        $stafs = RisdaStaf::where('status', 'aktif')
                         ->orderBy('nama_penuh')
                         ->get();

        $kumpulans = UserGroup::where('status', 'aktif')
                             ->orderBy('nama_kumpulan')
                             ->get();

        $bahagians = RisdaBahagian::where('status_dropdown', 'aktif')
                                 ->orderBy('nama_bahagian')
                                 ->get();

        $stesens = RisdaStesen::where('status_dropdown', 'aktif')
                             ->orderBy('nama_stesen')
                             ->get();

        // Check if current user is Administrator (can access all bahagians)
        $isAdministrator = $this->isAdministrator();

        return view('pengurusan.edit-pengguna', compact('pengguna', 'stafs', 'kumpulans', 'bahagians', 'stesens', 'isAdministrator'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $pengguna)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $pengguna->id,
            'password' => 'nullable|string|min:8|confirmed',
            'kumpulan_id' => 'nullable|exists:user_groups,id',
            'bahagian_akses_id' => 'required|string',
            'stesen_akses_ids' => 'nullable|string',
            'status' => 'required|in:aktif,tidak_aktif,digantung',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Parse stesen akses IDs (RISDA Pattern)
        $stesenAksesIds = null;
        if ($request->stesen_akses_ids) {
            $stesenAksesIds = json_decode($request->stesen_akses_ids, true);
        }

        // Determine jenis_organisasi and organisasi_id (same logic as create)
        $jenisOrganisasi = null;
        $organisasiId = null;

        if ($request->bahagian_akses_id === 'semua') {
            $jenisOrganisasi = 'semua';
            $organisasiId = null;
            // If "Semua Stesen" selected, set stesen_akses_ids to null for all access
            if (!empty($stesenAksesIds) && in_array('semua', $stesenAksesIds)) {
                $stesenAksesIds = null;
            }
        } elseif (!empty($stesenAksesIds)) {
            // Check if "Semua Stesen" selected
            if (in_array('semua', $stesenAksesIds)) {
                $jenisOrganisasi = 'semua';
                $organisasiId = null;
                $stesenAksesIds = null; // Null means access to all stesen
            } else {
                $jenisOrganisasi = 'stesen';
                // For backward compatibility, set organisasi_id to first stesen
                $organisasiId = $stesenAksesIds[0];
            }
        } elseif ($request->bahagian_akses_id) {
            $jenisOrganisasi = 'bahagian';
            $organisasiId = $request->bahagian_akses_id;
        }

        // Update user data
        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'kumpulan_id' => $request->kumpulan_id,
            'jenis_organisasi' => $jenisOrganisasi,
            'organisasi_id' => $organisasiId,
            'stesen_akses_ids' => $stesenAksesIds,
            'status' => $request->status,
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = $request->password;
        }

        $pengguna->update($updateData);

        return redirect()->route('pengurusan.senarai-pengguna')
            ->with('success', 'Pengguna berjaya dikemaskini!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $pengguna)
    {
        $pengguna->delete();

        return redirect()->route('pengurusan.senarai-pengguna')
            ->with('success', 'Pengguna berjaya dipadam!');
    }

    /**
     * Get stesen by bahagian ID for AJAX calls.
     */
    public function getStesenByBahagian($bahagianId)
    {
        $stesens = RisdaStesen::where('risda_bahagian_id', $bahagianId)
                             ->where('status_dropdown', 'aktif')
                             ->orderBy('nama_stesen')
                             ->get(['id', 'nama_stesen']);

        return response()->json($stesens);
    }

    /**
     * Get ALL stesen from ALL bahagians for Administrator.
     */
    public function getAllStesen()
    {
        $stesens = RisdaStesen::where('status_dropdown', 'aktif')
                             ->orderBy('nama_stesen')
                             ->get(['id', 'nama_stesen']);

        return response()->json($stesens);
    }

    /**
     * Check if current user is Administrator.
     */
    private function isAdministrator()
    {
        $user = auth()->user();

        if (!$user || !$user->kumpulan) {
            return false;
        }

        // Check if user group contains "Admin" or has highest privileges
        $kumpulanName = strtolower($user->kumpulan->nama_kumpulan);

        return str_contains($kumpulanName, 'admin') ||
               $user->jenis_organisasi === 'semua' ||
               $user->organisasi_id === null;
    }
}
