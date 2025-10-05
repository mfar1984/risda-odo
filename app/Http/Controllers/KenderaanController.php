<?php

namespace App\Http\Controllers;

use App\Models\Kenderaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class KenderaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $currentUser = auth()->user();

        // Start building query
        $query = Kenderaan::with('pencipta');

        // Apply organizational scope
        if ($this->isAdministrator()) {
            // Administrator can see all vehicles
        } else {
            // Regular users can only see vehicles within their organizational scope
            $query->whereHas('pencipta', function($q) use ($currentUser) {
                if ($currentUser->jenis_organisasi === 'bahagian') {
                    $q->where('organisasi_id', $currentUser->organisasi_id);
                } elseif ($currentUser->jenis_organisasi === 'stesen') {
                    $q->where('organisasi_id', $currentUser->organisasi_id);
                }
            });
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_plat', 'like', "%{$search}%")
                  ->orWhere('jenama', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('jenis_bahan_api')) {
            $query->where('jenis_bahan_api', $request->jenis_bahan_api);
        }

        // Paginate results
        $kenderaans = $query->orderBy('created_at', 'desc')
            ->paginate(5)
            ->withQueryString();

        return view('pengurusan.senarai-kenderaan', compact('kenderaans'));
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
        return view('pengurusan.tambah-kenderaan');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'no_plat' => 'required|string|max:20|unique:kenderaans,no_plat',
            'jenama' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'tahun' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'no_enjin' => 'required|string|max:50',
            'no_casis' => 'required|string|max:50',
            'jenis_bahan_api' => 'required|in:petrol,diesel',
            'kapasiti_muatan' => 'nullable|string|max:50',
            'warna' => 'required|string|max:50',
            'cukai_tamat_tempoh' => 'required|date',
            'tarikh_pendaftaran' => 'required|date',
            'status' => 'required|in:aktif,tidak_aktif,penyelenggaraan',
            'dokumen_kenderaan.*' => 'nullable|file|mimes:pdf,jpeg,jpg,png|max:10240', // 10MB
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $currentUser = auth()->user();

        $data = $request->all();
        $data['dicipta_oleh'] = $currentUser?->id;

        if ($currentUser?->jenis_organisasi === 'bahagian') {
            $data['bahagian_id'] = $currentUser->organisasi_id;
            $data['stesen_id'] = null;
        } elseif ($currentUser?->jenis_organisasi === 'stesen') {
            $data['bahagian_id'] = $currentUser->bahagian_akses_id ?? $currentUser->bahagian_id ?? null;
            $data['stesen_id'] = $currentUser->organisasi_id;
        }

        // Handle file uploads
        $uploadedDocuments = [];
        if ($request->hasFile('dokumen_kenderaan')) {
            $dokumenPaths = [];
            foreach ($request->file('dokumen_kenderaan') as $file) {
                $path = $file->store('kenderaan-dokumen', 'public');
                $dokumenPaths[] = [
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'uploaded_at' => now()->toDateTimeString(),
                ];
                $uploadedDocuments[] = $file->getClientOriginalName();
            }
            $data['dokumen_kenderaan'] = $dokumenPaths;
        }

        $kenderaan = Kenderaan::create($data);

        // Log activity
        activity('kenderaan')
            ->performedOn($kenderaan)
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'no_plat' => $kenderaan->no_plat,
                'jenama' => $kenderaan->jenama,
                'model' => $kenderaan->model,
                'tahun' => $kenderaan->tahun,
                'jenis_bahan_api' => $kenderaan->jenis_bahan_api,
                'status' => $kenderaan->status,
                'cukai_tamat_tempoh' => $kenderaan->cukai_tamat_tempoh,
                'tarikh_pendaftaran' => $kenderaan->tarikh_pendaftaran,
                'documents_uploaded' => $uploadedDocuments,
            ])
            ->event('created')
            ->log("Kenderaan '{$kenderaan->no_plat}' ({$kenderaan->jenama} {$kenderaan->model}) telah didaftarkan");

        return redirect()->route('pengurusan.senarai-kenderaan')
            ->with('success', 'Kenderaan berjaya didaftarkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kenderaan $kenderaan)
    {
        $kenderaan->load('pencipta');
        return view('pengurusan.show-kenderaan', compact('kenderaan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kenderaan $kenderaan)
    {
        return view('pengurusan.edit-kenderaan', compact('kenderaan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kenderaan $kenderaan)
    {
        $validator = Validator::make($request->all(), [
            'no_plat' => 'required|string|max:20|unique:kenderaans,no_plat,' . $kenderaan->id,
            'jenama' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'tahun' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'no_enjin' => 'required|string|max:50',
            'no_casis' => 'required|string|max:50',
            'jenis_bahan_api' => 'required|in:petrol,diesel',
            'kapasiti_muatan' => 'nullable|string|max:50',
            'warna' => 'required|string|max:50',
            'cukai_tamat_tempoh' => 'required|date',
            'tarikh_pendaftaran' => 'required|date',
            'status' => 'required|in:aktif,tidak_aktif,penyelenggaraan',
            'dokumen_kenderaan.*' => 'nullable|file|mimes:pdf,jpeg,jpg,png|max:10240', // 10MB
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Store old values for logging
        $oldNoPlat = $kenderaan->no_plat;
        $oldStatus = $kenderaan->status;
        $oldCukaiTamatTempoh = $kenderaan->cukai_tamat_tempoh;

        $data = $request->all();

        // Handle file uploads
        $newDocuments = [];
        if ($request->hasFile('dokumen_kenderaan')) {
            $dokumenPaths = $kenderaan->dokumen_kenderaan ?? [];

            foreach ($request->file('dokumen_kenderaan') as $file) {
                $path = $file->store('kenderaan-dokumen', 'public');
                $dokumenPaths[] = [
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'uploaded_at' => now()->toDateTimeString(),
                ];
                $newDocuments[] = $file->getClientOriginalName();
            }
            $data['dokumen_kenderaan'] = $dokumenPaths;
        }

        $kenderaan->update($data);

        // Prepare changes array
        $changes = [];
        if ($oldNoPlat != $kenderaan->no_plat) {
            $changes['no_plat'] = ['old' => $oldNoPlat, 'new' => $kenderaan->no_plat];
        }
        if ($oldStatus != $kenderaan->status) {
            $changes['status'] = ['old' => $oldStatus, 'new' => $kenderaan->status];
        }
        if ($oldCukaiTamatTempoh != $kenderaan->cukai_tamat_tempoh) {
            $changes['cukai_tamat_tempoh'] = ['old' => $oldCukaiTamatTempoh, 'new' => $kenderaan->cukai_tamat_tempoh];
        }

        // Log activity
        activity('kenderaan')
            ->performedOn($kenderaan)
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'no_plat' => $kenderaan->no_plat,
                'changes' => $changes,
                'new_documents' => $newDocuments,
                'total_changes' => count($changes) + (count($newDocuments) > 0 ? 1 : 0),
            ])
            ->event('updated')
            ->log("Kenderaan '{$kenderaan->no_plat}' telah dikemaskini (" . (count($changes) + (count($newDocuments) > 0 ? 1 : 0)) . " medan diubah)");

        return redirect()->route('pengurusan.senarai-kenderaan')
            ->with('success', 'Kenderaan berjaya dikemaskini.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kenderaan $kenderaan)
    {
        // Store data for logging before deletion
        $kenderaanId = $kenderaan->id;
        $noPlat = $kenderaan->no_plat;
        $jenama = $kenderaan->jenama;
        $model = $kenderaan->model;
        $status = $kenderaan->status;
        $documentsCount = count($kenderaan->dokumen_kenderaan ?? []);

        // Delete associated files
        if ($kenderaan->dokumen_kenderaan) {
            foreach ($kenderaan->dokumen_kenderaan as $dokumen) {
                if (isset($dokumen['path'])) {
                    Storage::disk('public')->delete($dokumen['path']);
                }
            }
        }

        $kenderaan->delete();

        // Log activity
        activity('kenderaan')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'kenderaan_id' => $kenderaanId,
                'no_plat' => $noPlat,
                'jenama' => $jenama,
                'model' => $model,
                'status' => $status,
                'documents_deleted' => $documentsCount,
            ])
            ->event('deleted')
            ->log("Kenderaan '{$noPlat}' ({$jenama} {$model}) telah dipadam");

        return redirect()->route('pengurusan.senarai-kenderaan')
            ->with('success', 'Kenderaan berjaya dipadam.');
    }
}
