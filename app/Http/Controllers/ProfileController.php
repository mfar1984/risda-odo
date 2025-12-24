<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $risdaStaf = $user->risdaStaf;
        
        // Store old values for logging
        $oldName = $user->name;
        $oldEmail = $user->email;
        
        $user->fill($request->validated());

        // Track if email changed
        $emailChanged = false;
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
            $emailChanged = true;
        }

        $user->save();

        // Update RISDA Staf if exists
        if ($risdaStaf) {
            $risdaStafData = $request->only([
                'no_pekerja',
                'nama_penuh',
                'no_kad_pengenalan',
                'jantina',
                'jawatan',
                'no_telefon',
                'email',
                'no_fax',
                'alamat_1',
                'alamat_2',
                'poskod',
                'bandar',
                'negeri',
                'negara',
            ]);

            $risdaStaf->update($risdaStafData);
        }

        // Prepare changes
        $changes = [];
        if ($oldName != $user->name) {
            $changes['name'] = ['old' => $oldName, 'new' => $user->name];
        }
        if ($oldEmail != $user->email) {
            $changes['email'] = ['old' => $oldEmail, 'new' => $user->email];
        }

        // Log activity
        activity()
            ->performedOn($user)
            ->causedBy($user)
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'changes' => $changes,
                'email_verification_reset' => $emailChanged,
                'risda_staf_updated' => $risdaStaf ? true : false,
            ])
            ->event('updated_profile')
            ->log("Profile '{$user->name}' telah dikemaskini");

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Store data for logging before deletion
        $userId = $user->id;
        $userName = $user->name;
        $userEmail = $user->email;
        $userJenisOrganisasi = $user->jenis_organisasi;

        // Log activity before logout
        activity()
            ->causedBy($user)
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'user_id' => $userId,
                'user_name' => $userName,
                'user_email' => $userEmail,
                'jenis_organisasi' => $userJenisOrganisasi,
                'self_deleted' => true,
            ])
            ->event('deleted_account')
            ->log("Akaun '{$userName}' ({$userEmail}) telah dipadam oleh pengguna sendiri");

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
