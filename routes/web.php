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

    // Settings Route
    Route::get('/settings', function () {
        return view('settings.index');
    })->name('settings.index');

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

        // RISDA Routes - Specific routes first, then dynamic routes
        Route::get('/senarai-risda', [App\Http\Controllers\RisdaBahagianController::class, 'index'])->name('senarai-risda');

        // RISDA Bahagian specific routes
        Route::get('/senarai-risda/tambah-bahagian', [App\Http\Controllers\RisdaBahagianController::class, 'create'])->name('tambah-bahagian');
        Route::post('/senarai-risda/tambah-bahagian', [App\Http\Controllers\RisdaBahagianController::class, 'store'])->name('store-bahagian');

        // RISDA Stesen specific routes
        Route::get('/senarai-risda/tambah-stesen', [App\Http\Controllers\RisdaStesenController::class, 'create'])->name('tambah-stesen');
        Route::post('/senarai-risda/tambah-stesen', [App\Http\Controllers\RisdaStesenController::class, 'store'])->name('store-stesen');
        Route::get('/senarai-risda/stesen/{risdaStesen}', [App\Http\Controllers\RisdaStesenController::class, 'show'])->name('show-stesen');
        Route::get('/senarai-risda/stesen/{risdaStesen}/edit', [App\Http\Controllers\RisdaStesenController::class, 'edit'])->name('edit-stesen');
        Route::put('/senarai-risda/stesen/{risdaStesen}', [App\Http\Controllers\RisdaStesenController::class, 'update'])->name('update-stesen');
        Route::delete('/senarai-risda/stesen/{risdaStesen}', [App\Http\Controllers\RisdaStesenController::class, 'destroy'])->name('delete-stesen');

        // RISDA Staf specific routes
        Route::get('/senarai-risda/tambah-staf', [App\Http\Controllers\RisdaStafController::class, 'create'])->name('tambah-staf');
        Route::post('/senarai-risda/tambah-staf', [App\Http\Controllers\RisdaStafController::class, 'store'])->name('store-staf');
        Route::get('/senarai-risda/staf/{risdaStaf}', [App\Http\Controllers\RisdaStafController::class, 'show'])->name('show-staf');
        Route::get('/senarai-risda/staf/{risdaStaf}/edit', [App\Http\Controllers\RisdaStafController::class, 'edit'])->name('edit-staf');
        Route::put('/senarai-risda/staf/{risdaStaf}', [App\Http\Controllers\RisdaStafController::class, 'update'])->name('update-staf');
        Route::delete('/senarai-risda/staf/{risdaStaf}', [App\Http\Controllers\RisdaStafController::class, 'destroy'])->name('delete-staf');

        // RISDA Bahagian dynamic routes (must be last)
        Route::get('/senarai-risda/{risdaBahagian}', [App\Http\Controllers\RisdaBahagianController::class, 'show'])->name('show-bahagian');
        Route::get('/senarai-risda/{risdaBahagian}/edit', [App\Http\Controllers\RisdaBahagianController::class, 'edit'])->name('edit-bahagian');
        Route::put('/senarai-risda/{risdaBahagian}', [App\Http\Controllers\RisdaBahagianController::class, 'update'])->name('update-bahagian');
        Route::delete('/senarai-risda/{risdaBahagian}', [App\Http\Controllers\RisdaBahagianController::class, 'destroy'])->name('delete-bahagian');

        // User Group Routes
        Route::get('/senarai-kumpulan', [App\Http\Controllers\UserGroupController::class, 'index'])->name('senarai-kumpulan');
        Route::get('/senarai-kumpulan/tambah-kumpulan', [App\Http\Controllers\UserGroupController::class, 'create'])->name('tambah-kumpulan');
        Route::post('/senarai-kumpulan/tambah-kumpulan', [App\Http\Controllers\UserGroupController::class, 'store'])->name('store-kumpulan');
        Route::get('/senarai-kumpulan/{userGroup}', [App\Http\Controllers\UserGroupController::class, 'show'])->name('show-kumpulan');
        Route::get('/senarai-kumpulan/{userGroup}/edit', [App\Http\Controllers\UserGroupController::class, 'edit'])->name('edit-kumpulan');
        Route::put('/senarai-kumpulan/{userGroup}', [App\Http\Controllers\UserGroupController::class, 'update'])->name('update-kumpulan');
        Route::delete('/senarai-kumpulan/{userGroup}', [App\Http\Controllers\UserGroupController::class, 'destroy'])->name('delete-kumpulan');

        // Senarai Pengguna Routes
        Route::get('/senarai-pengguna', [App\Http\Controllers\PenggunaController::class, 'index'])->name('senarai-pengguna');
        Route::get('/senarai-pengguna/tambah-pengguna', [App\Http\Controllers\PenggunaController::class, 'create'])->name('tambah-pengguna');
        Route::post('/senarai-pengguna/tambah-pengguna', [App\Http\Controllers\PenggunaController::class, 'store'])->name('store-pengguna');
        Route::get('/senarai-pengguna/get-stesen/{bahagianId}', [App\Http\Controllers\PenggunaController::class, 'getStesenByBahagian'])->name('get-stesen-by-bahagian');
Route::get('/senarai-pengguna/get-all-stesen', [App\Http\Controllers\PenggunaController::class, 'getAllStesen'])->name('get-all-stesen');
        Route::get('/senarai-pengguna/{pengguna}', [App\Http\Controllers\PenggunaController::class, 'show'])->name('show-pengguna');
        Route::get('/senarai-pengguna/{pengguna}/edit', [App\Http\Controllers\PenggunaController::class, 'edit'])->name('edit-pengguna');
        Route::put('/senarai-pengguna/{pengguna}', [App\Http\Controllers\PenggunaController::class, 'update'])->name('update-pengguna');
        Route::delete('/senarai-pengguna/{pengguna}', [App\Http\Controllers\PenggunaController::class, 'destroy'])->name('delete-pengguna');




        Route::get('/senarai-kenderaan', function () {
            return view('pengurusan.senarai-kenderaan');
        })->name('senarai-kenderaan');

        Route::get('/aktiviti-log', function () {
            return view('pengurusan.aktiviti-log');
        })->name('aktiviti-log');

        Route::get('/aktiviti-log-keselamatan', function () {
            return view('pengurusan.aktiviti-log-keselamatan');
        })->name('aktiviti-log-keselamatan');
    });

    // Help Routes
    Route::prefix('help')->name('help.')->group(function () {
        Route::get('/panduan-pengguna', function () {
            return view('help.panduan-pengguna');
        })->name('panduan-pengguna');

        Route::get('/faq', function () {
            return view('help.faq');
        })->name('faq');

        Route::get('/hubungi-sokongan', function () {
            return view('help.hubungi-sokongan');
        })->name('hubungi-sokongan');

        Route::get('/element', function () {
            return view('help.element');
        })->name('element');

        Route::get('/komponen', function () {
            return view('help.komponen');
        })->name('komponen');

        Route::get('/status-sistem', function () {
            return view('help.status-sistem');
        })->name('status-sistem');

        Route::get('/nota-keluaran', function () {
            return view('help.nota-keluaran');
        })->name('nota-keluaran');
    });
});

require __DIR__.'/auth.php';
