<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Program Routes
    Route::get('/program', function () {
        return view('program.index');
    })->name('program.index');

    // Log Pemandu Routes
    Route::get('/log-pemandu', function () {
        return view('log-pemandu.index');
    })->name('log-pemandu.index');

    // Laporan Routes
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/senarai-program', function () {
            return view('laporan.senarai-program');
        })->name('senarai-program');

        Route::get('/laporan-kenderaan', function () {
            return view('laporan.laporan-kenderaan');
        })->name('laporan-kenderaan');

        Route::get('/laporan-kilometer', function () {
            return view('laporan.laporan-kilometer');
        })->name('laporan-kilometer');

        Route::get('/laporan-kos', function () {
            return view('laporan.laporan-kos');
        })->name('laporan-kos');

        Route::get('/laporan-pemandu', function () {
            return view('laporan.laporan-pemandu');
        })->name('laporan-pemandu');
    });

    // Pengurusan Routes
    Route::prefix('pengurusan')->name('pengurusan.')->group(function () {
        Route::get('/tetapan-umum', function () {
            return view('pengurusan.tetapan-umum');
        })->name('tetapan-umum');

        Route::get('/senarai-risda', function () {
            return view('pengurusan.senarai-risda');
        })->name('senarai-risda');

        Route::get('/senarai-kumpulan', function () {
            return view('pengurusan.senarai-kumpulan');
        })->name('senarai-kumpulan');

        Route::get('/senarai-pengguna', function () {
            return view('pengurusan.senarai-pengguna');
        })->name('senarai-pengguna');

        Route::get('/aktiviti-log', function () {
            return view('pengurusan.aktiviti-log');
        })->name('aktiviti-log');

        Route::get('/aktiviti-log-keselamatan', function () {
            return view('pengurusan.aktiviti-log-keselamatan');
        })->name('aktiviti-log-keselamatan');
    });
});

require __DIR__.'/auth.php';
