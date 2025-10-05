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
    public function index(Request $request)
    {
        // Build query for bahagians with search and pagination
        $bahagianQuery = RisdaBahagian::query();

        if ($request->filled('search_bahagian')) {
            $search = $request->search_bahagian;
            $bahagianQuery->where(function($q) use ($search) {
                $q->where('nama_bahagian', 'like', "%{$search}%")
                  ->orWhere('alamat_1', 'like', "%{$search}%")
                  ->orWhere('alamat_2', 'like', "%{$search}%")
                  ->orWhere('bandar', 'like', "%{$search}%")
                  ->orWhere('negeri', 'like', "%{$search}%")
                  ->orWhere('no_telefon', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status_bahagian')) {
            $bahagianQuery->where('status_dropdown', $request->status_bahagian);
        }

        $bahagians = $bahagianQuery->latest()
            ->paginate(5, ['*'], 'bahagian_page')
            ->withQueryString();

        // Build query for stesens with search and pagination
        $stesenQuery = \App\Models\RisdaStesen::with('risdaBahagian');

        if ($request->filled('search_stesen')) {
            $search = $request->search_stesen;
            $stesenQuery->where(function($q) use ($search) {
                $q->where('nama_stesen', 'like', "%{$search}%")
                  ->orWhere('alamat_1', 'like', "%{$search}%")
                  ->orWhere('alamat_2', 'like', "%{$search}%")
                  ->orWhere('bandar', 'like', "%{$search}%")
                  ->orWhere('negeri', 'like', "%{$search}%")
                  ->orWhere('no_telefon', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('risdaBahagian', function($subQ) use ($search) {
                      $subQ->where('nama_bahagian', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status_stesen')) {
            $stesenQuery->where('status_dropdown', $request->status_stesen);
        }

        if ($request->filled('bahagian_stesen')) {
            $stesenQuery->where('risda_bahagian_id', $request->bahagian_stesen);
        }

        $stesens = $stesenQuery->latest()
            ->paginate(5, ['*'], 'stesen_page')
            ->withQueryString();

        // Build query for stafs with search and pagination
        $stafQuery = \App\Models\RisdaStaf::with(['bahagian', 'stesen']);

        if ($request->filled('search_staf')) {
            $search = $request->search_staf;
            $stafQuery->where(function($q) use ($search) {
                $q->where('no_pekerja', 'like', "%{$search}%")
                  ->orWhere('nama_penuh', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('jawatan', 'like', "%{$search}%")
                  ->orWhere('no_telefon', 'like', "%{$search}%")
                  ->orWhereHas('bahagian', function($subQ) use ($search) {
                      $subQ->where('nama_bahagian', 'like', "%{$search}%");
                  })
                  ->orWhereHas('stesen', function($subQ) use ($search) {
                      $subQ->where('nama_stesen', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status_staf')) {
            $stafQuery->where('status', $request->status_staf);
        }

        if ($request->filled('bahagian_staf')) {
            $stafQuery->where('risda_bahagian_id', $request->bahagian_staf);
        }

        if ($request->filled('stesen_staf')) {
            $stafQuery->where('risda_stesen_id', $request->stesen_staf);
        }

        $stafs = $stafQuery->latest()
            ->paginate(5, ['*'], 'staf_page')
            ->withQueryString();

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

        $bahagian = RisdaBahagian::create($data);

        // Log activity
        activity('risda')
            ->performedOn($bahagian)
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'entity' => 'bahagian',
                'nama_bahagian' => $bahagian->nama_bahagian,
                'email' => $bahagian->email,
                'no_telefon' => $bahagian->no_telefon,
                'status' => $bahagian->status,
            ])
            ->event('created')
            ->log("Bahagian '{$bahagian->nama_bahagian}' telah ditambah");

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

        $old = $risdaBahagian->only(['nama_bahagian','email','no_telefon','status']);
        $risdaBahagian->update($data);

        $changes = [];
        foreach ($old as $k => $v) {
            if ($v != $risdaBahagian->$k) {
                $changes[$k] = ['old' => $v, 'new' => $risdaBahagian->$k];
            }
        }

        activity('risda')
            ->performedOn($risdaBahagian)
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'entity' => 'bahagian',
                'nama_bahagian' => $risdaBahagian->nama_bahagian,
                'changes' => $changes,
            ])
            ->event('updated')
            ->log("Bahagian '{$risdaBahagian->nama_bahagian}' telah dikemaskini (" . count($changes) . " medan diubah)");

        return redirect()->route('pengurusan.senarai-risda')
            ->with('success', 'RISDA Bahagian berjaya dikemaskini!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RisdaBahagian $risdaBahagian)
    {
        $name = $risdaBahagian->nama_bahagian;
        $id = $risdaBahagian->id;
        $risdaBahagian->delete();

        activity('risda')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'entity' => 'bahagian',
                'bahagian_id' => $id,
                'nama_bahagian' => $name,
            ])
            ->event('deleted')
            ->log("Bahagian '{$name}' telah dipadam");

        return redirect()->route('pengurusan.senarai-risda')
            ->with('success', 'RISDA Bahagian berjaya dipadam!');
    }
}
