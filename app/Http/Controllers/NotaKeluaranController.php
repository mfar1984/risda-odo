<?php

namespace App\Http\Controllers;

use App\Models\NotaKeluaran;
use Illuminate\Http\Request;

class NotaKeluaranController extends Controller
{
    /**
     * Display the release notes.
     */
    public function index()
    {
        $releases = NotaKeluaran::published()
                                ->orderBy('urutan', 'desc')
                                ->orderBy('tarikh_keluaran', 'desc')
                                ->get();

        return view('help.nota-keluaran', compact('releases'));
    }
}
