<?php

namespace App\Http\Controllers;

use App\Models\RisdaBahagian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RisdaBahagianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bahagians = RisdaBahagian::latest()->get();
        $stesens = \App\Models\RisdaStesen::with('risdaBahagian')->latest()->get();
        $stafs = \App\Models\RisdaStaf::with(['bahagian', 'stesen'])->latest()->get();
        return view('pengurusan.senarai-risda', compact('bahagians', 'stesens', 'stafs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pengurusan.tambah-bahagian');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_bahagian' => 'required|string|max:255',
            'no_telefon' => 'required|string|max:20',
            'no_fax' => 'nullable|string|max:20',
            'email' => 'required|email|max:255|unique:risda_bahagians,email',
            'status_dropdown' => 'required|in:aktif,tidak_aktif,dalam_pembinaan',
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

        // Set status from status_dropdown
        $data = $request->all();
        $data['status'] = $request->status_dropdown;

        RisdaBahagian::create($data);

        return redirect()->route('pengurusan.senarai-risda')
            ->with('success', 'RISDA Bahagian berjaya ditambah!');
    }

    /**
     * Display the specified resource.
     */
    public function show(RisdaBahagian $risdaBahagian)
    {
        return view('pengurusan.show-bahagian', compact('risdaBahagian'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RisdaBahagian $risdaBahagian)
    {
        return view('pengurusan.edit-bahagian', compact('risdaBahagian'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RisdaBahagian $risdaBahagian)
    {
        $validator = Validator::make($request->all(), [
            'nama_bahagian' => 'required|string|max:255',
            'no_telefon' => 'required|string|max:20',
            'no_fax' => 'nullable|string|max:20',
            'email' => 'required|email|max:255|unique:risda_bahagians,email,' . $risdaBahagian->id,
            'status_dropdown' => 'required|in:aktif,tidak_aktif,dalam_pembinaan',
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

        // Set status from status_dropdown
        $data = $request->all();
        $data['status'] = $request->status_dropdown;

        $risdaBahagian->update($data);

        return redirect()->route('pengurusan.senarai-risda')
            ->with('success', 'RISDA Bahagian berjaya dikemaskini!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RisdaBahagian $risdaBahagian)
    {
        $risdaBahagian->delete();

        return redirect()->route('pengurusan.senarai-risda')
            ->with('success', 'RISDA Bahagian berjaya dipadam!');
    }
}
