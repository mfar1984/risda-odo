<?php

namespace App\Http\Controllers;

use App\Models\TetapanUmum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TetapanUmumController extends Controller
{
    /**
     * Display the general settings form.
     */
    public function index()
    {
        $tetapan = TetapanUmum::getForCurrentUser();
        return view('pengurusan.tetapan-umum', compact('tetapan'));
    }

    /**
     * Update the general settings.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_sistem' => 'required|string|max:255',
            'alamat_1' => 'nullable|string|max:255',
            'alamat_2' => 'nullable|string|max:255',
            'poskod' => 'nullable|string|max:10',
            'bandar' => 'nullable|string|max:100',
            'negeri' => 'nullable|string|max:100',
            'negara' => 'required|string|max:100',
            'maksimum_percubaan_login' => 'required|integer|min:1|max:10',
            'masa_tamat_sesi_minit' => 'required|integer|min:5|max:1440', // 5 minutes to 24 hours
            'map_provider' => 'nullable|string|max:50',
            'map_api_key' => 'nullable|string|max:255',
            'map_style_url' => 'nullable|string|max:255',
            'map_default_lat' => 'nullable|numeric|between:-90,90',
            'map_default_long' => 'nullable|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = auth()->user();
        $tetapan = TetapanUmum::getForCurrentUser();

        // Store old values for logging
        $oldValues = $tetapan->only([
            'nama_sistem',
            'alamat_1',
            'alamat_2',
            'poskod',
            'bandar',
            'negeri',
            'negara',
            'maksimum_percubaan_login',
            'masa_tamat_sesi_minit',
            'map_provider',
            'map_api_key',
            'map_default_lat',
            'map_default_long',
        ]);

        $data = $request->except(['versi_sistem']); // Exclude version from update
        $data['dikemaskini_oleh'] = $user->id;

        $tetapan->update($data);

        // Get changed values
        $changes = [];
        foreach ($oldValues as $key => $oldValue) {
            $newValue = $tetapan->$key;
            if ($oldValue != $newValue) {
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        // Log activity (tetapan)
        activity('tetapan')
            ->performedOn($tetapan)
            ->causedBy($user)
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'changes' => $changes,
                'total_fields_changed' => count($changes),
                'system_name' => $tetapan->nama_sistem,
            ])
            ->event('updated')
            ->log("Tetapan umum sistem '{$tetapan->nama_sistem}' telah dikemaskini (" . count($changes) . " medan diubah)");

        return redirect()->route('pengurusan.tetapan-umum')
            ->with('success', 'Tetapan umum berjaya dikemaskini.');
    }

    /**
     * Check if current user is Administrator.
     */
    private function isAdministrator()
    {
        $user = auth()->user();
        return $user && $user->jenis_organisasi === 'semua';
    }
}
