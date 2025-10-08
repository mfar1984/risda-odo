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
    public function index(Request $request)
    {
        $currentUser = auth()->user();

        // Start building query
        $query = User::with(['kumpulan']);

        if (!$this->isAdministrator()) {
            $query->where('jenis_organisasi', '!=', 'semua')
                ->where(function ($q) use ($currentUser) {
                    if ($currentUser->jenis_organisasi === 'bahagian') {
                        $stesenIds = collect($currentUser->stesen_akses_ids ?? [])
                            ->map(fn ($id) => (int) $id)
                            ->filter();

                        if ($stesenIds->isEmpty()) {
                            $stesenIds = RisdaStesen::where('risda_bahagian_id', $currentUser->organisasi_id)
                                ->pluck('id');
                        }

                        $q->where(function ($inner) use ($currentUser, $stesenIds) {
                            $inner->where(function ($bahagianQuery) use ($currentUser) {
                                $bahagianQuery->where('jenis_organisasi', 'bahagian')
                                    ->where('organisasi_id', $currentUser->organisasi_id);
                            })
                            ->orWhere(function ($stesenQuery) use ($stesenIds) {
                                $stesenQuery->where('jenis_organisasi', 'stesen')
                                    ->whereIn('organisasi_id', $stesenIds->all());
                            });
                        });
                    } elseif ($currentUser->jenis_organisasi === 'stesen') {
                        $q->where(function ($inner) use ($currentUser) {
                            $inner->where('id', $currentUser->id)
                                ->orWhere(function ($stesenQuery) use ($currentUser) {
                                    $stesenQuery->where('jenis_organisasi', 'stesen')
                                        ->where('organisasi_id', $currentUser->organisasi_id);
                                });
                        });
                    }
                });
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Paginate results
        $penggunas = $query->orderBy('created_at', 'desc')
            ->paginate(5)
            ->withQueryString();

        return view('pengurusan.senarai-pengguna', compact('penggunas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $currentUser = auth()->user();
        $existingStafIds = RisdaStaf::whereIn('email', User::pluck('email'))
            ->pluck('id')
            ->toArray();

        $stafs = RisdaStaf::query()
            ->where('status', 'aktif')
            ->whereNotIn('id', $existingStafIds)
            ->when(!$this->isAdministrator(), function ($query) {
                $currentUser = auth()->user();

                if ($currentUser->jenis_organisasi === 'bahagian') {
                    $stesenIds = collect($currentUser->stesen_akses_ids ?? [])
                        ->map(fn ($id) => (int) $id)
                        ->filter();

                    $query->where(function ($inner) use ($currentUser, $stesenIds) {
                        $inner->where('bahagian_id', $currentUser->organisasi_id);

                        if ($stesenIds->isNotEmpty()) {
                            $inner->orWhereIn('stesen_id', $stesenIds->all());
                        }
                    });
                } elseif ($currentUser->jenis_organisasi === 'stesen') {
                    $query->where('stesen_id', $currentUser->organisasi_id);
                }
            })
            ->orderBy('nama_penuh')
            ->get();

        $currentUser = auth()->user();

        $kumpulans = UserGroup::query()
            ->where('status', 'aktif')
            ->when(!$this->isAdministrator(), function ($query) use ($currentUser) {
                $query->where(function ($inner) use ($currentUser) {
                    $inner->where('dicipta_oleh', $currentUser->id)
                        ->orWhereHas('pengguna', function ($sub) use ($currentUser) {
                            if ($currentUser->jenis_organisasi === 'bahagian') {
                                $sub->where(function ($userQuery) use ($currentUser) {
                                    $userQuery->where('jenis_organisasi', 'bahagian')
                                        ->where('organisasi_id', $currentUser->organisasi_id);

                                    $userQuery->orWhere(function ($stesenQuery) use ($currentUser) {
                                        $stesenIds = collect($currentUser->stesen_akses_ids ?? [])
                                            ->map(fn ($id) => (int) $id)
                                            ->filter();

                                        if ($stesenIds->isNotEmpty()) {
                                            $stesenQuery->where('jenis_organisasi', 'stesen')
                                                ->whereIn('organisasi_id', $stesenIds->all());
                                        } else {
                                            $stesenQuery->where('jenis_organisasi', 'stesen')
                                                ->where('organisasi_id', 0);
                                        }
                                    });
                                });
                            } elseif ($currentUser->jenis_organisasi === 'stesen') {
                                $sub->where('jenis_organisasi', 'stesen')
                                    ->where('organisasi_id', $currentUser->organisasi_id);
                            }
                        });
                });
            })
            ->orderBy('nama_kumpulan')
            ->get();

        $bahagians = RisdaBahagian::query()
            ->where('status_dropdown', 'aktif')
            ->when(!$this->isAdministrator(), function ($query) use ($currentUser) {
                if ($currentUser->jenis_organisasi === 'bahagian') {
                    $query->where('id', $currentUser->organisasi_id);
                } elseif ($currentUser->jenis_organisasi === 'stesen') {
                    $query->where('id', 0);
                }
            })
            ->orderBy('nama_bahagian')
            ->get();

        $stesens = RisdaStesen::query()
            ->where('status_dropdown', 'aktif')
            ->when(!$this->isAdministrator(), function ($query) use ($currentUser) {
                if ($currentUser->jenis_organisasi === 'bahagian') {
                    $stesenIds = collect($currentUser->stesen_akses_ids ?? [])
                        ->map(fn ($id) => (int) $id)
                        ->filter();

                    $query->where(function ($inner) use ($currentUser, $stesenIds) {
                        $inner->where('risda_bahagian_id', $currentUser->organisasi_id);

                        if ($stesenIds->isNotEmpty()) {
                            $inner->orWhereIn('id', $stesenIds->all());
                        }
                    });
                } elseif ($currentUser->jenis_organisasi === 'stesen') {
                    $query->where('id', $currentUser->organisasi_id);
                }
            })
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

        if ($request->filled('stesen_akses_ids')) {
            $decoded = json_decode($request->stesen_akses_ids, true) ?? [];
            if (in_array('semua', $decoded)) {
                $jenisOrganisasi = 'bahagian';
                $organisasiId = (int) $request->bahagian_akses_id;
                $stesenAksesIds = null;
            } elseif (!empty($decoded)) {
                $jenisOrganisasi = 'stesen';
                $organisasiId = (int) $decoded[0];
                $stesenAksesIds = $decoded;
            }
        } elseif ($request->filled('bahagian_akses_id')) {
            $jenisOrganisasi = 'bahagian';
            $organisasiId = (int) $request->bahagian_akses_id;
        }

        if (!$jenisOrganisasi) {
            // If no stesen selected and bahagian provided, default to bahagian access
            if ($request->filled('bahagian_akses_id')) {
                $jenisOrganisasi = 'bahagian';
                $organisasiId = (int) $request->bahagian_akses_id;
            } else {
                // Fallback: inherit creator's scope if not Administrator
                $currentUser = auth()->user();
                $jenisOrganisasi = 'bahagian';
                $organisasiId = $currentUser && $currentUser->jenis_organisasi !== 'semua'
                    ? (int) $currentUser->organisasi_id
                    : null;
            }
        }

        // Create user
        $user = User::create([
            'name' => $staf->nama_penuh,
            'email' => $staf->email,
            'password' => $request->password,
            'kumpulan_id' => $request->kumpulan_id,
            'staf_id' => $staf->id,
            'jenis_organisasi' => $jenisOrganisasi,
            'organisasi_id' => $organisasiId,
            'stesen_akses_ids' => $stesenAksesIds,
            'status' => $request->status_akaun,
        ]);

        // Resolve group name for logging
        $groupName = null;
        if ($request->kumpulan_id) {
            $group = UserGroup::find($request->kumpulan_id);
            $groupName = $group?->nama_kumpulan;
        }

        // Log activity
        activity('pengguna')
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'user_name' => $user->name,
                'user_email' => $user->email,
                'jenis_organisasi' => $jenisOrganisasi,
                'organisasi_id' => $organisasiId,
                'kumpulan_id' => $request->kumpulan_id,
                'group_name' => $groupName,
                'status' => $request->status_akaun,
                'staf_id' => $request->staf_id,
                'stesen_akses_ids' => $stesenAksesIds,
            ])
            ->event('created')
            ->log("Pengguna '{$user->name}' ({$user->email}) telah ditambah");

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

        // Store old values for logging
        $oldName = $pengguna->name;
        $oldEmail = $pengguna->email;
        $oldStatus = $pengguna->status;
        $oldJenisOrganisasi = $pengguna->jenis_organisasi;
        $oldOrganisasiId = $pengguna->organisasi_id;
        $oldKumpulanId = $pengguna->kumpulan_id;
        $oldStesenAksesIds = $pengguna->stesen_akses_ids;

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

        // Track if password was changed
        $passwordChanged = false;
        // Only update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = $request->password;
            $passwordChanged = true;
        }

        $pengguna->update($updateData);

        // Prepare changes array
        $changes = [];
        if ($oldName != $pengguna->name) {
            $changes['name'] = ['old' => $oldName, 'new' => $pengguna->name];
        }
        if ($oldEmail != $pengguna->email) {
            $changes['email'] = ['old' => $oldEmail, 'new' => $pengguna->email];
        }
        if ($oldStatus != $pengguna->status) {
            $changes['status'] = ['old' => $oldStatus, 'new' => $pengguna->status];
        }
        if ($oldJenisOrganisasi != $pengguna->jenis_organisasi) {
            $changes['jenis_organisasi'] = ['old' => $oldJenisOrganisasi, 'new' => $pengguna->jenis_organisasi];
        }
        if ($oldOrganisasiId != $pengguna->organisasi_id) {
            $changes['organisasi_id'] = ['old' => $oldOrganisasiId, 'new' => $pengguna->organisasi_id];
        }
        if ($oldKumpulanId != $pengguna->kumpulan_id) {
            $changes['kumpulan_id'] = ['old' => $oldKumpulanId, 'new' => $pengguna->kumpulan_id];
        }
        if (json_encode($oldStesenAksesIds) != json_encode($stesenAksesIds)) {
            $changes['stesen_akses_ids'] = ['old' => $oldStesenAksesIds, 'new' => $stesenAksesIds];
        }

        // Resolve group names for before/after
        $oldGroupName = $oldKumpulanId ? optional(UserGroup::find($oldKumpulanId))->nama_kumpulan : null;
        $newGroupName = $pengguna->kumpulan_id ? optional(UserGroup::find($pengguna->kumpulan_id))->nama_kumpulan : null;

        // Log activity
        activity('pengguna')
            ->performedOn($pengguna)
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'user_name' => $pengguna->name,
                'user_email' => $pengguna->email,
                'changes' => $changes,
                'password_changed' => $passwordChanged,
                'total_fields_changed' => count($changes) + ($passwordChanged ? 1 : 0),
                'group_name_before' => $oldGroupName,
                'group_name_after' => $newGroupName,
            ])
            ->event('updated')
            ->log("Pengguna '{$pengguna->name}' telah dikemaskini (" . (count($changes) + ($passwordChanged ? 1 : 0)) . " medan diubah)");

        return redirect()->route('pengurusan.senarai-pengguna')
            ->with('success', 'Pengguna berjaya dikemaskini!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $pengguna)
    {
        // Prevent deleting Administrator
        if ($pengguna->jenis_organisasi === 'semua') {
            return redirect()->route('pengurusan.senarai-pengguna')
                ->with('error', 'Tidak dapat memadamkan Administrator sistem!');
        }

        // Check if other users are assigned to user groups created by this user
        $createdGroups = UserGroup::where('dicipta_oleh', $pengguna->id)->get();
        $groupsWithUsers = [];

        foreach ($createdGroups as $group) {
            $usersInGroup = User::where('kumpulan_id', $group->id)->where('id', '!=', $pengguna->id)->count();
            if ($usersInGroup > 0) {
                $groupsWithUsers[] = $group->nama_kumpulan . " ({$usersInGroup} pengguna)";
            }
        }

        if (!empty($groupsWithUsers)) {
            $groupList = implode(', ', $groupsWithUsers);
            return redirect()->route('pengurusan.senarai-pengguna')
                ->with('error', "Tidak dapat memadamkan pengguna kerana masih ada pengguna lain dalam kumpulan yang dicipta: {$groupList}. Sila pindahkan pengguna tersebut ke kumpulan lain terlebih dahulu.");
        }

        if ($createdGroups->count() > 0) {
            // Find Administrator to transfer ownership
            $administrator = User::where('jenis_organisasi', 'semua')->first();

            if ($administrator) {
                // Transfer ownership of all user groups to Administrator
                foreach ($createdGroups as $group) {
                    $group->dicipta_oleh = $administrator->id;
                    $group->save();
                }

                $groupNames = $createdGroups->pluck('nama_kumpulan')->implode(', ');
                \Log::info("Transferred ownership of user groups ({$groupNames}) from user {$pengguna->name} to Administrator before deletion.");
            } else {
                // No administrator found - prevent deletion
                return redirect()->route('pengurusan.senarai-pengguna')
                    ->with('error', 'Tidak dapat memadamkan pengguna kerana tiada Administrator untuk memindahkan pemilikan kumpulan pengguna.');
            }
        }

        // Store data for logging before deletion
        $userId = $pengguna->id;
        $userName = $pengguna->name;
        $userEmail = $pengguna->email;
        $userStatus = $pengguna->status;
        $userJenisOrganisasi = $pengguna->jenis_organisasi;
        $userOrganisasiId = $pengguna->organisasi_id;
        $transferredGroups = $createdGroups->count() > 0 ? $createdGroups->pluck('nama_kumpulan')->toArray() : [];

        // Now safe to delete the user
        $pengguna->delete();

        // Log activity
        activity('pengguna')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'user_id' => $userId,
                'user_name' => $userName,
                'user_email' => $userEmail,
                'status' => $userStatus,
                'jenis_organisasi' => $userJenisOrganisasi,
                'organisasi_id' => $userOrganisasiId,
                'transferred_groups' => $transferredGroups,
            ])
            ->event('deleted')
            ->log("Pengguna '{$userName}' ({$userEmail}) telah dipadam");

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

        if (!$user) {
            return false;
        }

        // Only users with jenis_organisasi = 'semua' are true administrators
        // This ensures only admin@jara.my (or similar) have full access
        return $user->jenis_organisasi === 'semua';
    }
}
