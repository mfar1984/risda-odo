<?php

namespace App\Http\Controllers;

use App\Models\RisdaStaf;
use App\Models\RisdaBahagian;
use App\Models\RisdaStesen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RisdaStafController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stafs = RisdaStaf::with(['bahagian', 'stesen'])
                         ->orderBy('created_at', 'desc')
                         ->get();

        return view('pengurusan.senarai-risda', compact('stafs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $bahagians = RisdaBahagian::where('status_dropdown', 'aktif')
                                 ->orderBy('nama_bahagian')
                                 ->get();

        $stesens = RisdaStesen::where('status_dropdown', 'aktif')
                             ->orderBy('nama_stesen')
                             ->get();

        return view('pengurusan.tambah-staf', compact('bahagians', 'stesens'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'no_pekerja' => 'required|string|max:20|unique:risda_stafs,no_pekerja',
            'nama_penuh' => 'required|string|max:255',
            'no_kad_pengenalan' => 'required|string|size:14|unique:risda_stafs,no_kad_pengenalan',
            'jantina' => 'required|in:lelaki,perempuan',
            'bahagian_id' => 'required|exists:risda_bahagians,id',
            'stesen_id' => 'nullable|exists:risda_stesens,id',
            'jawatan' => 'required|string|max:255',
            'no_telefon' => 'required|string|max:20',
            'email' => 'required|email|max:255|unique:risda_stafs,email',
            'no_fax' => 'nullable|string|max:20',
            'status' => 'required|in:aktif,tidak_aktif,gantung',
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

        $staf = RisdaStaf::create($request->all());

        activity('risda')
            ->performedOn($staf)
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'entity' => 'staf',
                'no_pekerja' => $staf->no_pekerja,
                'nama_penuh' => $staf->nama_penuh,
                'bahagian' => optional($staf->bahagian)->nama_bahagian,
                'stesen' => optional($staf->stesen)->nama_stesen,
                'jawatan' => $staf->jawatan,
                'status' => $staf->status,
            ])
            ->event('created')
            ->log("Staf '{$staf->nama_penuh}' telah ditambah");

        return redirect()->route('pengurusan.senarai-risda')
            ->with('success', 'RISDA Staf berjaya ditambah!');
    }

    /**
     * Display the specified resource.
     */
    public function show(RisdaStaf $risdaStaf)
    {
        $risdaStaf->load(['bahagian', 'stesen']);
        return view('pengurusan.show-staf', compact('risdaStaf'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RisdaStaf $risdaStaf)
    {
        $bahagians = RisdaBahagian::where('status_dropdown', 'aktif')
                                 ->orderBy('nama_bahagian')
                                 ->get();

        $stesens = RisdaStesen::where('status_dropdown', 'aktif')
                             ->orderBy('nama_stesen')
                             ->get();

        return view('pengurusan.edit-staf', compact('risdaStaf', 'bahagians', 'stesens'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RisdaStaf $risdaStaf)
    {
        $validator = Validator::make($request->all(), [
            'no_pekerja' => 'required|string|max:20|unique:risda_stafs,no_pekerja,' . $risdaStaf->id,
            'nama_penuh' => 'required|string|max:255',
            'no_kad_pengenalan' => 'required|string|size:14|unique:risda_stafs,no_kad_pengenalan,' . $risdaStaf->id,
            'jantina' => 'required|in:lelaki,perempuan',
            'bahagian_id' => 'required|exists:risda_bahagians,id',
            'stesen_id' => 'nullable|exists:risda_stesens,id',
            'jawatan' => 'required|string|max:255',
            'no_telefon' => 'required|string|max:20',
            'email' => 'required|email|max:255|unique:risda_stafs,email,' . $risdaStaf->id,
            'no_fax' => 'nullable|string|max:20',
            'status' => 'required|in:aktif,tidak_aktif,gantung',
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

        $old = $risdaStaf->only(['no_pekerja','nama_penuh','bahagian_id','stesen_id','jawatan','email','no_telefon','status']);
        $risdaStaf->update($request->all());

        $changes = [];
        foreach ($old as $k => $v) {
            if ($v != $risdaStaf->$k) {
                $changes[$k] = ['old' => $v, 'new' => $risdaStaf->$k];
            }
        }

        activity('risda')
            ->performedOn($risdaStaf)
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'entity' => 'staf',
                'no_pekerja' => $risdaStaf->no_pekerja,
                'nama_penuh' => $risdaStaf->nama_penuh,
                'changes' => $changes,
            ])
            ->event('updated')
            ->log("Staf '{$risdaStaf->nama_penuh}' telah dikemaskini (" . count($changes) . " medan diubah)");

        return redirect()->route('pengurusan.senarai-risda')
            ->with('success', 'RISDA Staf berjaya dikemaskini!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RisdaStaf $risdaStaf)
    {
        $id = $risdaStaf->id;
        $name = $risdaStaf->nama_penuh;
        $risdaStaf->delete();

        activity('risda')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'entity' => 'staf',
                'staf_id' => $id,
                'nama_penuh' => $name,
            ])
            ->event('deleted')
            ->log("Staf '{$name}' telah dipadam");

        return redirect()->route('pengurusan.senarai-risda')
            ->with('success', 'RISDA Staf berjaya dipadam!');
    }
}
