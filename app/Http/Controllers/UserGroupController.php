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
     * Display a listing of the resource with organizational hierarchy.
     */
    public function index(Request $request)
    {
        $currentUser = auth()->user();

        // Start building query
        $query = UserGroup::with('pencipta');

        // Apply organizational scope with hierarchy
        if (!$this->isAdministrator()) {
            $this->applyOrganizationalScope($query, $currentUser);
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
        $kumpulans = $query->orderBy('created_at', 'desc')
            ->paginate(5)
            ->withQueryString();

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
     * Bahagian user can see groups created by themselves OR groups used by users in their hierarchy.
     */
    private function applyOrganizationalScope($query, $currentUser)
    {
        $query->where(function ($q) use ($currentUser) {
            // Groups created by current user
            $q->where('dicipta_oleh', $currentUser->id);
            
            // Groups used by users in same organizational hierarchy
            $q->orWhereHas('pengguna', function ($userQuery) use ($currentUser) {
                if ($currentUser->jenis_organisasi === 'bahagian') {
                    $stesenIds = $this->getStesenIdsForBahagian($currentUser->organisasi_id, $currentUser->stesen_akses_ids);
                    
                    $userQuery->where(function ($inner) use ($currentUser, $stesenIds) {
                        // Users directly under bahagian
                        $inner->where(function ($bahagianQuery) use ($currentUser) {
                            $bahagianQuery->where('jenis_organisasi', 'bahagian')
                                         ->where('organisasi_id', $currentUser->organisasi_id);
                        });
                        
                        // Users under any stesen in this bahagian
                        if ($stesenIds->isNotEmpty()) {
                            $inner->orWhere(function ($stesenQuery) use ($stesenIds) {
                                $stesenQuery->where('jenis_organisasi', 'stesen')
                                           ->whereIn('organisasi_id', $stesenIds->all());
                            });
                        }
                    });
                } elseif ($currentUser->jenis_organisasi === 'stesen') {
                    $userQuery->where('jenis_organisasi', 'stesen')
                             ->where('organisasi_id', $currentUser->organisasi_id);
                }
            });
        });
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

        $userGroup = UserGroup::create($data);

        // Build granted permissions details for logging
        $grantedDetailed = [];
        $moduleLabels = UserGroup::getModuleLabels();
        $permissionLabels = UserGroup::getPermissionLabels();
        foreach (($userGroup->kebenaran_matrix ?? []) as $module => $actions) {
            foreach (($actions ?? []) as $action => $granted) {
                if ($granted) {
                    $grantedDetailed[] = [
                        'module' => $module,
                        'module_label' => $moduleLabels[$module] ?? $module,
                        'action' => $action,
                        'action_label' => $permissionLabels[$action] ?? $action,
                    ];
                }
            }
        }

        // Log activity
        activity('kumpulan')
            ->performedOn($userGroup)
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'group_id' => $userGroup->id,
                'group_name' => $userGroup->nama_kumpulan,
                'status' => $userGroup->status,
                'description' => $userGroup->keterangan,
                'permissions_count' => count($userGroup->kebenaran_matrix ?? []),
                'permissions_granted_count' => count($grantedDetailed),
                'permissions_granted_detailed' => $grantedDetailed,
            ])
            ->event('created')
            ->log("Kumpulan pengguna '{$userGroup->nama_kumpulan}' telah dicipta");

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
        // Merge existing permissions with default matrix to include new modules
        $defaultMatrix = UserGroup::getDefaultPermissionMatrix();
        $existingMatrix = $userGroup->kebenaran_matrix ?: [];
        
        // Merge: existing values take priority, but new modules are added
        $permissionMatrix = array_merge($defaultMatrix, $existingMatrix);
        
        // Ensure all modules from default exist (for new modules added to system)
        foreach ($defaultMatrix as $module => $actions) {
            if (!isset($permissionMatrix[$module])) {
                $permissionMatrix[$module] = $actions;
            }
        }
        
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

        // Store old values for comparison
        $oldName = $userGroup->nama_kumpulan;
        $oldStatus = $userGroup->status;
        $oldMatrix = $userGroup->kebenaran_matrix;

        $userGroup->update($request->all());

        // Detect permission changes
        $permissionChanges = [];
        $oldPermissions = $oldMatrix ?? [];
        $newPermissions = $userGroup->kebenaran_matrix ?? [];
        
        foreach ($newPermissions as $module => $actions) {
            foreach ($actions as $action => $granted) {
                $oldValue = $oldPermissions[$module][$action] ?? false;
                if ($oldValue != $granted) {
                    $permissionChanges[] = [
                        'module' => $module,
                        'action' => $action,
                        'old' => $oldValue,
                        'new' => $granted,
                    ];
                }
            }
        }

        // Build detailed permission change labels
        $moduleLabels = UserGroup::getModuleLabels();
        $permissionLabels = UserGroup::getPermissionLabels();
        $permissionChangesDetailed = array_map(function ($change) use ($moduleLabels, $permissionLabels) {
            $change['module_label'] = $moduleLabels[$change['module']] ?? $change['module'];
            $change['action_label'] = $permissionLabels[$change['action']] ?? $change['action'];
            $change['change'] = ($change['new'] ? 'grant' : 'revoke');
            return $change;
        }, $permissionChanges);

        // Count before/after grants
        $grantedBefore = 0; foreach (($oldPermissions ?? []) as $m => $acts) { foreach (($acts ?? []) as $a => $v) { if ($v) { $grantedBefore++; } } }
        $grantedAfter = 0; foreach (($newPermissions ?? []) as $m => $acts) { foreach (($acts ?? []) as $a => $v) { if ($v) { $grantedAfter++; } } }

        // Log activity
        activity('kumpulan')
            ->performedOn($userGroup)
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'group_id' => $userGroup->id,
                'group_name' => $userGroup->nama_kumpulan,
                'old_name' => $oldName,
                'old_status' => $oldStatus,
                'new_status' => $userGroup->status,
                'permission_changes' => $permissionChanges,
                'permission_changes_detailed' => $permissionChangesDetailed,
                'total_permission_changes' => count($permissionChanges),
                'granted_before' => $grantedBefore,
                'granted_after' => $grantedAfter,
            ])
            ->event('updated')
            ->log("Kumpulan pengguna '{$userGroup->nama_kumpulan}' telah dikemaskini (" . count($permissionChanges) . " kebenaran diubah)");

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

        // Store data for logging before deletion
        $groupId = $userGroup->id;
        $groupName = $userGroup->nama_kumpulan;
        $groupStatus = $userGroup->status;

        $userGroup->delete();

        // Log activity
        activity('kumpulan')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'group_id' => $groupId,
                'group_id' => $groupId,
                'group_name' => $groupName,
                'status' => $groupStatus,
            ])
            ->event('deleted')
            ->log("Kumpulan pengguna '{$groupName}' telah dipadam");

        return redirect()->route('pengurusan.senarai-kumpulan')
            ->with('success', 'Kumpulan pengguna berjaya dipadam.');
    }


}
