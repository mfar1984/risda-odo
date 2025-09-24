<?php

namespace App\Http\Controllers;

use App\Models\RisdaStesen;
use App\Models\RisdaBahagian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RisdaStesenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stesens = RisdaStesen::with('risdaBahagian')->latest()->get();
        return view('pengurusan.senarai-stesen', compact('stesens'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $bahagians = RisdaBahagian::where('status_dropdown', 'aktif')->orderBy('nama_bahagian')->get();
        return view('pengurusan.tambah-stesen', compact('bahagians'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'risda_bahagian_id' => 'required|exists:risda_bahagians,id',
            'nama_stesen' => 'required|string|max:255',
            'no_telefon' => 'required|string|max:20',
            'no_fax' => 'nullable|string|max:20',
            'email' => 'required|email|max:255|unique:risda_stesens,email',
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

        RisdaStesen::create($data);

        return redirect()->route('pengurusan.senarai-risda')
            ->with('success', 'RISDA Stesen berjaya ditambah!');
    }

    /**
     * Display the specified resource.
     */
    public function show(RisdaStesen $risdaStesen)
    {
        $risdaStesen->load('risdaBahagian');
        return view('pengurusan.show-stesen', compact('risdaStesen'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RisdaStesen $risdaStesen)
    {
        $bahagians = RisdaBahagian::where('status_dropdown', 'aktif')->orderBy('nama_bahagian')->get();
        $risdaStesen->load('risdaBahagian');
        return view('pengurusan.edit-stesen', compact('risdaStesen', 'bahagians'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RisdaStesen $risdaStesen)
    {
        $validator = Validator::make($request->all(), [
            'risda_bahagian_id' => 'required|exists:risda_bahagians,id',
            'nama_stesen' => 'required|string|max:255',
            'no_telefon' => 'required|string|max:20',
            'no_fax' => 'nullable|string|max:20',
            'email' => 'required|email|max:255|unique:risda_stesens,email,' . $risdaStesen->id,
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

        $risdaStesen->update($data);

        return redirect()->route('pengurusan.senarai-risda')
            ->with('success', 'RISDA Stesen berjaya dikemaskini!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RisdaStesen $risdaStesen)
    {
        $risdaStesen->delete();

        return redirect()->route('pengurusan.senarai-risda')
            ->with('success', 'RISDA Stesen berjaya dipadam!');
    }
}
