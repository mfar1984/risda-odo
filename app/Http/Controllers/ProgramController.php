<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\RisdaStaf;
use App\Models\Kenderaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $currentUser = auth()->user();

        // Start building query
        $query = Program::with(['pemohon', 'pemandu', 'kenderaan', 'pencipta']);

        // Apply organizational scope
        if ($this->isAdministrator()) {
            // Administrator can see all programs
        } else {
            // Regular users can only see programs within their organizational scope
            $query->where(function($q) use ($currentUser) {
                if ($currentUser->jenis_organisasi === 'bahagian') {
                    $q->where('jenis_organisasi', 'bahagian')
                      ->where('organisasi_id', $currentUser->organisasi_id);
                } elseif ($currentUser->jenis_organisasi === 'stesen') {
                    $q->where('jenis_organisasi', 'stesen')
                      ->where('organisasi_id', $currentUser->organisasi_id);
                }
            });
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_program', 'like', "%{$search}%")
                  ->orWhere('lokasi_program', 'like', "%{$search}%")
                  ->orWhere('penerangan', 'like', "%{$search}%")
                  ->orWhereHas('pemohon', function($subQ) use ($search) {
                      $subQ->where('nama_penuh', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tarikh_dari') && $request->filled('tarikh_hingga')) {
            $query->whereBetween('tarikh_mula', [$request->tarikh_dari, $request->tarikh_hingga]);
        }

        // Paginate results
        $programs = $query->orderBy('created_at', 'desc')->paginate(15);

        // Append query parameters to pagination links
        $programs->appends($request->query());

        return view('program.index', compact('programs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $currentUser = auth()->user();

        // Get staff based on user's organization
        $stafs = $this->getStafForCurrentUser();

        // Get vehicles based on user's organization
        $kenderaans = $this->getKenderaanForCurrentUser();

        return view('program.tambah-program', compact('stafs', 'kenderaans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_program' => 'required|string|max:255',
            'tarikh_mula' => 'required|date',
            'tarikh_selesai' => 'required|date|after_or_equal:tarikh_mula',
            'lokasi_program' => 'required|string|max:255',
            'penerangan' => 'nullable|string|max:1000',
            'permohonan_dari' => 'required|exists:risda_stafs,id',
            'pemandu_id' => 'required|exists:risda_stafs,id',
            'kenderaan_id' => 'required|exists:kenderaans,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $currentUser = auth()->user();
        $data = $request->all();

        // Set organizational context
        $data['jenis_organisasi'] = $currentUser->jenis_organisasi;
        $data['organisasi_id'] = $currentUser->organisasi_id;
        $data['dicipta_oleh'] = $currentUser->id;

        // Set default status to 'draf' for new programs
        $data['status'] = 'draf';

        Program::create($data);

        return redirect()->route('program.index')
            ->with('success', 'Program berjaya dicipta.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Program $program)
    {
        // Check access permission
        $this->checkProgramAccess($program);

        $program->load(['pemohon', 'pemandu', 'kenderaan', 'pencipta', 'pengemas_kini']);

        return view('program.show-program', compact('program'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Program $program)
    {
        // Check access permission
        $this->checkProgramAccess($program);

        // Get staff and vehicles for current user
        $stafs = $this->getStafForCurrentUser();
        $kenderaans = $this->getKenderaanForCurrentUser();

        return view('program.edit-program', compact('program', 'stafs', 'kenderaans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Program $program)
    {
        // Check access permission
        $this->checkProgramAccess($program);

        $validator = Validator::make($request->all(), [
            'nama_program' => 'required|string|max:255',
            'status' => 'required|in:draf,lulus,tolak,aktif,tertunda,selesai',
            'tarikh_mula' => 'required|date',
            'tarikh_selesai' => 'required|date|after_or_equal:tarikh_mula',
            'lokasi_program' => 'required|string|max:255',
            'penerangan' => 'nullable|string|max:1000',
            'permohonan_dari' => 'required|exists:risda_stafs,id',
            'pemandu_id' => 'required|exists:risda_stafs,id',
            'kenderaan_id' => 'required|exists:kenderaans,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['dikemaskini_oleh'] = auth()->id();

        $program->update($data);

        return redirect()->route('program.index')
            ->with('success', 'Program berjaya dikemaskini.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Program $program)
    {
        // Check access permission
        $this->checkProgramAccess($program);

        $program->delete();

        return redirect()->route('program.index')
            ->with('success', 'Program berjaya dipadam.');
    }

    /**
     * Get staff for current user based on organizational access.
     */
    private function getStafForCurrentUser()
    {
        $currentUser = auth()->user();

        if ($currentUser->jenis_organisasi === 'semua') {
            // Administrator can see all active staff
            return RisdaStaf::where('status', 'aktif')
                           ->with(['bahagian', 'stesen'])
                           ->orderBy('nama_penuh')
                           ->get();
        }

        // Filter staff based on user's organization
        $query = RisdaStaf::where('status', 'aktif');

        if ($currentUser->jenis_organisasi === 'bahagian') {
            $query->where('bahagian_id', $currentUser->organisasi_id);
        } elseif ($currentUser->jenis_organisasi === 'stesen') {
            $query->where('stesen_id', $currentUser->organisasi_id);
        }

        return $query->with(['bahagian', 'stesen'])
                    ->orderBy('nama_penuh')
                    ->get();
    }

    /**
     * Get vehicles for current user based on organizational access.
     */
    private function getKenderaanForCurrentUser()
    {
        $currentUser = auth()->user();

        if ($currentUser->jenis_organisasi === 'semua') {
            // Administrator can see all active vehicles
            return Kenderaan::where('status', 'aktif')
                           ->with(['bahagian', 'stesen'])
                           ->orderBy('no_plat')
                           ->get();
        }

        // Filter vehicles based on user's organization
        $query = Kenderaan::where('status', 'aktif');

        if ($currentUser->jenis_organisasi === 'bahagian') {
            $query->where('bahagian_id', $currentUser->organisasi_id);
        } elseif ($currentUser->jenis_organisasi === 'stesen') {
            $query->where('stesen_id', $currentUser->organisasi_id);
        }

        return $query->with(['bahagian', 'stesen'])
                    ->orderBy('no_plat')
                    ->get();
    }

    /**
     * Approve a program (change status from 'draf' to 'lulus').
     */
    public function approve(Program $program)
    {
        // Check access permission
        $this->checkProgramAccess($program);

        // Only allow approval for 'draf' status
        if ($program->status !== 'draf') {
            return redirect()->route('program.index')
                ->with('error', 'Hanya program dengan status draf boleh diluluskan.');
        }

        $program->update([
            'status' => 'lulus',
            'dikemaskini_oleh' => auth()->id(),
        ]);

        return redirect()->route('program.index')
            ->with('success', "Program '{$program->nama_program}' berjaya diluluskan.");
    }

    /**
     * Reject a program (change status from 'draf' to 'tolak').
     */
    public function reject(Program $program)
    {
        // Check access permission
        $this->checkProgramAccess($program);

        // Only allow rejection for 'draf' status
        if ($program->status !== 'draf') {
            return redirect()->route('program.index')
                ->with('error', 'Hanya program dengan status draf boleh ditolak.');
        }

        $program->update([
            'status' => 'tolak',
            'dikemaskini_oleh' => auth()->id(),
        ]);

        return redirect()->route('program.index')
            ->with('success', "Program '{$program->nama_program}' berjaya ditolak.");
    }

    /**
     * Check if current user has access to the program.
     */
    private function checkProgramAccess(Program $program)
    {
        $currentUser = auth()->user();

        // Administrator has access to all programs
        if ($currentUser->jenis_organisasi === 'semua') {
            return;
        }

        // Check if program belongs to user's organization
        if ($program->jenis_organisasi !== $currentUser->jenis_organisasi ||
            $program->organisasi_id !== $currentUser->organisasi_id) {
            abort(403, 'Anda tidak mempunyai kebenaran untuk mengakses program ini.');
        }
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
