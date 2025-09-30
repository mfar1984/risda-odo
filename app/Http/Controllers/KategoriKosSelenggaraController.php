<?php

namespace App\Http\Controllers;

use App\Models\KategoriKosSelenggara;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KategoriKosSelenggaraController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(Request $request)
    {
        $query = KategoriKosSelenggara::query();

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_kategori', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if ($request->filled('aktif')) {
            $query->where('aktif', $request->aktif);
        }

        $kategoris = $query->orderBy('nama_kategori')
            ->paginate(10)
            ->withQueryString();

        return view('pengurusan.senarai-kategori-kos', compact('kategoris'));
    }

    /**
     * Store a newly created category via AJAX.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_kategori' => 'required|string|max:255|unique:kategori_kos_selenggara,nama_kategori',
            'keterangan' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $kategori = KategoriKosSelenggara::create([
            'nama_kategori' => $request->nama_kategori,
            'keterangan' => $request->keterangan,
            'aktif' => true,
        ]);

        return response()->json([
            'success' => true,
            'kategori' => $kategori,
            'message' => 'Kategori berjaya ditambah.',
        ]);
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, KategoriKosSelenggara $kategori)
    {
        $validator = Validator::make($request->all(), [
            'nama_kategori' => 'required|string|max:255|unique:kategori_kos_selenggara,nama_kategori,' . $kategori->id,
            'keterangan' => 'nullable|string|max:500',
            'aktif' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $kategori->update($request->all());

        return redirect()->route('pengurusan.senarai-kategori-kos')
            ->with('success', 'Kategori kos berjaya dikemaskini.');
    }

    /**
     * Remove the specified category.
     */
    public function destroy(KategoriKosSelenggara $kategori)
    {
        // Check if category is being used
        if ($kategori->selenggaraKenderaan()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak boleh padam kategori yang sedang digunakan oleh rekod penyelenggaraan.',
            ], 422);
        }

        $kategori->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori kos berjaya dipadam.',
        ]);
    }

    /**
     * Toggle category active status.
     */
    public function toggleStatus(KategoriKosSelenggara $kategori)
    {
        $kategori->update(['aktif' => !$kategori->aktif]);

        return redirect()->back()
            ->with('success', 'Status kategori berjaya dikemaskini.');
    }
}