<?php

namespace App\Http\Controllers;

use App\Models\UserGroup;
use App\Models\RisdaBahagian;
use App\Models\RisdaStesen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $currentUser = auth()->user();

        // Start building query
        $query = UserGroup::with('pencipta');

        // Apply organizational scope
        if ($this->isAdministrator()) {
            // Administrator can see all groups
        } else {
            // Regular users can only see groups within their organizational scope
            $query->with(['pencipta', 'pengguna'])
                ->whereHas('pengguna', function($q) use ($currentUser) {
                    if ($currentUser->jenis_organisasi === 'bahagian') {
                        // Bahagian users can see groups with users in their bahagian
                        $q->where('organisasi_id', $currentUser->organisasi_id);
                    } elseif ($currentUser->jenis_organisasi === 'stesen') {
                        // Stesen users can see groups with users in their bahagian
                        $q->where('organisasi_id', $currentUser->organisasi_id);
                    }
                });
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_kumpulan', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Paginate results
        $kumpulans = $query->orderBy('created_at', 'desc')->paginate(15);

        // Append query parameters to pagination links
        $kumpulans->appends($request->query());

        return view('pengurusan.senarai-kumpulan', compact('kumpulans'));
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissionMatrix = UserGroup::getDefaultPermissionMatrix();
        $moduleLabels = UserGroup::getModuleLabels();
        $permissionLabels = UserGroup::getPermissionLabels();

        return view('pengurusan.tambah-kumpulan', compact(
            'permissionMatrix',
            'moduleLabels',
            'permissionLabels'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_kumpulan' => 'required|string|max:255',
            'keterangan' => 'nullable|string|max:500',
            'status' => 'required|in:aktif,tidak_aktif,gantung',
            'kebenaran_matrix' => 'required|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['dicipta_oleh'] = auth()->id();

        UserGroup::create($data);

        return redirect()->route('pengurusan.senarai-kumpulan')
            ->with('success', 'Kumpulan pengguna berjaya dicipta.');
    }

    /**
     * Display the specified resource.
     */
    public function show(UserGroup $userGroup)
    {
        $userGroup->load(['pencipta']);
        $moduleLabels = UserGroup::getModuleLabels();
        $permissionLabels = UserGroup::getPermissionLabels();

        return view('pengurusan.show-kumpulan', compact('userGroup', 'moduleLabels', 'permissionLabels'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserGroup $userGroup)
    {
        $permissionMatrix = $userGroup->kebenaran_matrix ?: UserGroup::getDefaultPermissionMatrix();
        $moduleLabels = UserGroup::getModuleLabels();
        $permissionLabels = UserGroup::getPermissionLabels();

        return view('pengurusan.edit-kumpulan', compact(
            'userGroup',
            'permissionMatrix',
            'moduleLabels',
            'permissionLabels'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserGroup $userGroup)
    {
        $validator = Validator::make($request->all(), [
            'nama_kumpulan' => 'required|string|max:255',
            'keterangan' => 'nullable|string|max:500',
            'status' => 'required|in:aktif,tidak_aktif,gantung',
            'kebenaran_matrix' => 'required|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $userGroup->update($request->all());

        return redirect()->route('pengurusan.senarai-kumpulan')
            ->with('success', 'Kumpulan pengguna berjaya dikemaskini.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserGroup $userGroup)
    {
        // Check if group has users
        if ($userGroup->pengguna()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Tidak boleh padam kumpulan yang mempunyai pengguna.');
        }

        $userGroup->delete();

        return redirect()->route('pengurusan.senarai-kumpulan')
            ->with('success', 'Kumpulan pengguna berjaya dipadam.');
    }


}
