<?php

use App\Http\Controllers\LogPemanduController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'permission:dashboard,lihat'])->name('dashboard');

Route::post('/dashboard/generate-report', [App\Http\Controllers\TuntutanController::class, 'getDashboardReport'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.generate-report');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Settings Route
    Route::get('/settings', function () {
        return view('settings.index');
    })->name('settings.index');

    // Notification Routes (Web - for Bell Icon)
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [App\Http\Controllers\NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/mark-as-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/mark-all-as-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('mark-all-as-read');
    });

    // Program Routes (Permission-based)
    Route::middleware('permission:program,lihat')->group(function () {
        Route::get('/program', [App\Http\Controllers\ProgramController::class, 'index'])->name('program.index');
    });

    Route::middleware('permission:program,tambah')->group(function () {
        Route::get('/program/tambah-program', [App\Http\Controllers\ProgramController::class, 'create'])->name('program.create');
        Route::post('/program/tambah-program', [App\Http\Controllers\ProgramController::class, 'store'])->name('store-program');
    });

    Route::middleware('permission:program,kemaskini')->group(function () {
        Route::get('/program/{program}/edit', [App\Http\Controllers\ProgramController::class, 'edit'])->name('edit-program');
        Route::put('/program/{program}', [App\Http\Controllers\ProgramController::class, 'update'])->name('update-program');
    });

    Route::middleware('permission:program,lihat')->group(function () {
        Route::get('/program/{program}', [App\Http\Controllers\ProgramController::class, 'show'])->name('show-program');
    });

    Route::middleware('permission:program,padam')->group(function () {
        Route::delete('/program/{program}', [App\Http\Controllers\ProgramController::class, 'destroy'])->name('delete-program');
    });

    Route::middleware('permission:program,terima')->group(function () {
        Route::patch('/program/{program}/approve', [App\Http\Controllers\ProgramController::class, 'approve'])->name('approve-program');
    });

    Route::middleware('permission:program,tolak')->group(function () {
        Route::patch('/program/{program}/reject', [App\Http\Controllers\ProgramController::class, 'reject'])->name('reject-program');
    });

    Route::middleware('permission:program,lihat')->group(function () {
        Route::post('/program/{program}/log-export', [App\Http\Controllers\ProgramController::class, 'logExport'])->name('program.log-export');
    });

    // Log Pemandu Routes (Permission-based)
    Route::get('/log-pemandu', [LogPemanduController::class, 'index'])->name('log-pemandu.index');
    Route::get('/log-pemandu/tab-counts', [LogPemanduController::class, 'getTabCounts'])->name('log-pemandu.tab-counts');
    Route::get('/log-pemandu/{logPemandu}', [LogPemanduController::class, 'show'])->name('log-pemandu.show');
    Route::get('/log-pemandu/{logPemandu}/edit', [LogPemanduController::class, 'edit'])->name('log-pemandu.edit');
    Route::put('/log-pemandu/{logPemandu}', [LogPemanduController::class, 'update'])->name('log-pemandu.update');
    Route::delete('/log-pemandu/{logPemandu}', [LogPemanduController::class, 'destroy'])->name('log-pemandu.destroy');

    // Laporan Routes (Permission-based)
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::middleware('permission:laporan_senarai_program,lihat')->group(function () {
            Route::get('/senarai-program', [\App\Http\Controllers\Laporan\SenaraiProgramController::class, 'index'])->name('senarai-program');
            Route::get('/senarai-program/{program}', [\App\Http\Controllers\Laporan\SenaraiProgramController::class, 'show'])->name('senarai-program.show');
            Route::get('/senarai-program/{program}/pdf', [\App\Http\Controllers\Laporan\SenaraiProgramController::class, 'pdf'])->name('senarai-program.pdf');
        });

        Route::middleware('permission:laporan_kenderaan,lihat')->group(function () {
            Route::get('/laporan-kenderaan', [\App\Http\Controllers\Laporan\KenderaanController::class, 'index'])->name('laporan-kenderaan');
            Route::get('/laporan-kenderaan/{kenderaan}', [\App\Http\Controllers\Laporan\KenderaanController::class, 'show'])
                ->name('laporan-kenderaan.show');
            Route::get('/laporan-kenderaan/{kenderaan}/pdf', [\App\Http\Controllers\Laporan\KenderaanController::class, 'pdf'])
                ->name('laporan-kenderaan.pdf')
                ->middleware('permission:laporan_kenderaan,eksport');
        });

        Route::middleware('permission:laporan_kilometer,lihat')->group(function () {
            Route::get('/laporan-kilometer', [\App\Http\Controllers\Laporan\KilometerController::class, 'index'])->name('laporan-kilometer');
            Route::get('/laporan-kilometer/{program}', [\App\Http\Controllers\Laporan\KilometerController::class, 'show'])
                ->name('laporan-kilometer.show');
            Route::get('/laporan-kilometer/{program}/pdf', [\App\Http\Controllers\Laporan\KilometerController::class, 'pdf'])
                ->name('laporan-kilometer.pdf')
                ->middleware('permission:laporan_kilometer,eksport');
        });

        Route::middleware('permission:laporan_kos,lihat')->group(function () {
            Route::get('/laporan-kos', [\App\Http\Controllers\Laporan\KosController::class, 'index'])->name('laporan-kos');
            Route::get('/laporan-kos/{program}', [\App\Http\Controllers\Laporan\KosController::class, 'show'])
                ->name('laporan-kos.show');
            Route::get('/laporan-kos/{program}/pdf', [\App\Http\Controllers\Laporan\KosController::class, 'pdf'])
                ->name('laporan-kos.pdf')
                ->middleware('permission:laporan_kos,eksport');
        });

        Route::middleware('permission:laporan_pemandu,lihat')->group(function () {
            Route::get('/laporan-pemandu', [\App\Http\Controllers\Laporan\PemanduController::class, 'index'])->name('laporan-pemandu');
            Route::get('/laporan-pemandu/{driver}', [\App\Http\Controllers\Laporan\PemanduController::class, 'show'])
                ->name('laporan-pemandu.show');
            Route::get('/laporan-pemandu/{driver}/pdf', [\App\Http\Controllers\Laporan\PemanduController::class, 'pdf'])
                ->name('laporan-pemandu.pdf')
                ->middleware('permission:laporan_pemandu,eksport');
        });

        Route::middleware('permission:laporan_tuntutan,lihat')->group(function () {
            Route::get('/laporan-tuntutan', [\App\Http\Controllers\TuntutanController::class, 'index'])->name('laporan-tuntutan');
            Route::get('/laporan-tuntutan/{tuntutan}', [\App\Http\Controllers\TuntutanController::class, 'show'])
                ->name('laporan-tuntutan.show');
            Route::get('/laporan-tuntutan/export/pdf', [\App\Http\Controllers\TuntutanController::class, 'exportPdf'])
                ->name('laporan-tuntutan.export-pdf');
            
            // Action routes (permission-based)
            Route::post('/laporan-tuntutan/{tuntutan}/approve', [\App\Http\Controllers\TuntutanController::class, 'approve'])
                ->name('laporan-tuntutan.approve')
                ->middleware('permission:laporan_tuntutan,terima');
            Route::post('/laporan-tuntutan/{tuntutan}/reject', [\App\Http\Controllers\TuntutanController::class, 'reject'])
                ->name('laporan-tuntutan.reject')
                ->middleware('permission:laporan_tuntutan,tolak');
            Route::post('/laporan-tuntutan/{tuntutan}/cancel', [\App\Http\Controllers\TuntutanController::class, 'cancel'])
                ->name('laporan-tuntutan.cancel')
                ->middleware('permission:laporan_tuntutan,gantung');
            Route::delete('/laporan-tuntutan/{tuntutan}', [\App\Http\Controllers\TuntutanController::class, 'destroy'])
                ->name('laporan-tuntutan.destroy')
                ->middleware('permission:laporan_tuntutan,padam');
        });
    });

    // Pengurusan Routes
    Route::prefix('pengurusan')->name('pengurusan.')->middleware('auth')->group(function () {
        // Tetapan Umum Routes (Permission-based)
        Route::middleware('permission:tetapan_umum,lihat')->group(function () {
            Route::get('/tetapan-umum', [App\Http\Controllers\TetapanUmumController::class, 'index'])->name('tetapan-umum');
            Route::put('/tetapan-umum', [App\Http\Controllers\TetapanUmumController::class, 'update'])->name('update-tetapan-umum')->middleware('permission:tetapan_umum,kemaskini');
        });



        // RISDA Routes (Administrator Only)
        Route::middleware('admin')->group(function () {
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
        });

        // User Group Routes (Permission-based) - Specific routes first, then dynamic routes
        Route::middleware('permission:senarai_kumpulan,lihat')->group(function () {
            Route::get('/senarai-kumpulan', [App\Http\Controllers\UserGroupController::class, 'index'])->name('senarai-kumpulan');
        });
        Route::middleware('permission:senarai_kumpulan,tambah')->group(function () {
            Route::get('/senarai-kumpulan/tambah-kumpulan', [App\Http\Controllers\UserGroupController::class, 'create'])->name('tambah-kumpulan');
            Route::post('/senarai-kumpulan/tambah-kumpulan', [App\Http\Controllers\UserGroupController::class, 'store'])->name('store-kumpulan');
        });
        Route::middleware('permission:senarai_kumpulan,kemaskini')->group(function () {
            Route::get('/senarai-kumpulan/{userGroup}/edit', [App\Http\Controllers\UserGroupController::class, 'edit'])->name('edit-kumpulan');
            Route::put('/senarai-kumpulan/{userGroup}', [App\Http\Controllers\UserGroupController::class, 'update'])->name('update-kumpulan');
        });
        Route::middleware('permission:senarai_kumpulan,lihat')->group(function () {
            Route::get('/senarai-kumpulan/{userGroup}', [App\Http\Controllers\UserGroupController::class, 'show'])->name('show-kumpulan');
        });
        Route::middleware('permission:senarai_kumpulan,padam')->group(function () {
            Route::delete('/senarai-kumpulan/{userGroup}', [App\Http\Controllers\UserGroupController::class, 'destroy'])->name('delete-kumpulan');
        });

        // Senarai Pengguna Routes (Permission-based) - Specific routes first, then dynamic routes
        Route::middleware('permission:senarai_pengguna,lihat')->group(function () {
            Route::get('/senarai-pengguna', [App\Http\Controllers\PenggunaController::class, 'index'])->name('senarai-pengguna');
            Route::get('/senarai-pengguna/get-stesen/{bahagianId}', [App\Http\Controllers\PenggunaController::class, 'getStesenByBahagian'])->name('get-stesen-by-bahagian');
            Route::get('/senarai-pengguna/get-all-stesen', [App\Http\Controllers\PenggunaController::class, 'getAllStesen'])->name('get-all-stesen');
        });
        Route::middleware('permission:senarai_pengguna,tambah')->group(function () {
            Route::get('/senarai-pengguna/tambah-pengguna', [App\Http\Controllers\PenggunaController::class, 'create'])->name('tambah-pengguna');
            Route::post('/senarai-pengguna/tambah-pengguna', [App\Http\Controllers\PenggunaController::class, 'store'])->name('store-pengguna');
        });
        Route::middleware('permission:senarai_pengguna,kemaskini')->group(function () {
            Route::get('/senarai-pengguna/{pengguna}/edit', [App\Http\Controllers\PenggunaController::class, 'edit'])->name('edit-pengguna');
            Route::put('/senarai-pengguna/{pengguna}', [App\Http\Controllers\PenggunaController::class, 'update'])->name('update-pengguna');
        });
        Route::middleware('permission:senarai_pengguna,lihat')->group(function () {
            Route::get('/senarai-pengguna/{pengguna}', [App\Http\Controllers\PenggunaController::class, 'show'])->name('show-pengguna');
        });
        Route::middleware('permission:senarai_pengguna,padam')->group(function () {
            Route::delete('/senarai-pengguna/{pengguna}', [App\Http\Controllers\PenggunaController::class, 'destroy'])->name('delete-pengguna');
        });




        // Senarai Kenderaan Routes (Permission-based) - Specific routes first, then dynamic routes
        Route::middleware('permission:senarai_kenderaan,lihat')->group(function () {
            Route::get('/senarai-kenderaan', [App\Http\Controllers\KenderaanController::class, 'index'])->name('senarai-kenderaan');
        });
        Route::middleware('permission:senarai_kenderaan,tambah')->group(function () {
            Route::get('/senarai-kenderaan/tambah-kenderaan', [App\Http\Controllers\KenderaanController::class, 'create'])->name('tambah-kenderaan');
            Route::post('/senarai-kenderaan/tambah-kenderaan', [App\Http\Controllers\KenderaanController::class, 'store'])->name('store-kenderaan');
        });
        Route::middleware('permission:senarai_kenderaan,kemaskini')->group(function () {
            Route::get('/senarai-kenderaan/{kenderaan}/edit', [App\Http\Controllers\KenderaanController::class, 'edit'])->name('edit-kenderaan');
            Route::put('/senarai-kenderaan/{kenderaan}', [App\Http\Controllers\KenderaanController::class, 'update'])->name('update-kenderaan');
        });
        Route::middleware('permission:senarai_kenderaan,lihat')->group(function () {
            Route::get('/senarai-kenderaan/{kenderaan}', [App\Http\Controllers\KenderaanController::class, 'show'])->name('show-kenderaan');
        });
        Route::middleware('permission:senarai_kenderaan,padam')->group(function () {
            Route::delete('/senarai-kenderaan/{kenderaan}', [App\Http\Controllers\KenderaanController::class, 'destroy'])->name('delete-kenderaan');
        });

        // Selenggara Kenderaan Routes (Permission-based) - Specific routes first, then dynamic routes
        Route::middleware('permission:selenggara_kenderaan,lihat')->group(function () {
            Route::get('/senarai-selenggara', [App\Http\Controllers\SelenggaraKenderaanController::class, 'index'])->name('senarai-selenggara');
        });
        Route::middleware('permission:selenggara_kenderaan,tambah')->group(function () {
            Route::get('/senarai-selenggara/tambah-selenggara', [App\Http\Controllers\SelenggaraKenderaanController::class, 'create'])->name('tambah-selenggara');
            Route::post('/senarai-selenggara/tambah-selenggara', [App\Http\Controllers\SelenggaraKenderaanController::class, 'store'])->name('store-selenggara');
            Route::post('/kategori-kos-selenggara', [App\Http\Controllers\KategoriKosSelenggaraController::class, 'store'])->name('store-kategori-kos');
        });
        Route::middleware('permission:selenggara_kenderaan,kemaskini')->group(function () {
            Route::get('/senarai-selenggara/{selenggara}/edit', [App\Http\Controllers\SelenggaraKenderaanController::class, 'edit'])->name('edit-selenggara');
            Route::put('/senarai-selenggara/{selenggara}', [App\Http\Controllers\SelenggaraKenderaanController::class, 'update'])->name('update-selenggara');
        });
        Route::middleware('permission:selenggara_kenderaan,lihat')->group(function () {
            Route::get('/senarai-selenggara/{selenggara}', [App\Http\Controllers\SelenggaraKenderaanController::class, 'show'])->name('show-selenggara');
        });
        Route::middleware('permission:selenggara_kenderaan,padam')->group(function () {
            Route::delete('/senarai-selenggara/{selenggara}', [App\Http\Controllers\SelenggaraKenderaanController::class, 'destroy'])->name('delete-selenggara');
            Route::delete('/kategori-kos-selenggara/{kategori}', [App\Http\Controllers\KategoriKosSelenggaraController::class, 'destroy'])->name('delete-kategori-kos');
        });

        // Integrasi Routes (Permission-based)
        Route::middleware('permission:integrasi,lihat')->group(function () {
            Route::get('/integrasi', [App\Http\Controllers\IntegrasiController::class, 'index'])->name('integrasi');
        });
        
        // API Configuration (Administrator Only)
        Route::middleware('admin')->group(function () {
            Route::post('/integrasi/generate-api-token', [App\Http\Controllers\IntegrasiController::class, 'generateApiToken'])->name('generate-api-token');
            Route::put('/integrasi/cors', [App\Http\Controllers\IntegrasiController::class, 'updateCors'])->name('update-integrasi-cors');
            
            // Cuti Umum Management
            Route::get('/integrasi/cuti-umum/preview', [App\Http\Controllers\IntegrasiController::class, 'cutiUmumPreview'])->name('integrasi.cuti-umum-preview');
            Route::post('/integrasi/cuti-umum/tambah', [App\Http\Controllers\IntegrasiController::class, 'tambahCutiKhas'])->name('integrasi.tambah-cuti-khas');
            Route::put('/integrasi/cuti-umum/{id}', [App\Http\Controllers\IntegrasiController::class, 'updateCutiKhas'])->name('integrasi.update-cuti-khas');
            Route::delete('/integrasi/cuti-umum/{id}', [App\Http\Controllers\IntegrasiController::class, 'deleteCutiKhas'])->name('integrasi.delete-cuti-khas');
        });
        
        // Weather & Email Configuration (Permission-based for all users based on their organisation - Multi-tenancy)
        Route::middleware('permission:integrasi,kemaskini')->group(function () {
            Route::put('/integrasi/cuaca', [App\Http\Controllers\IntegrasiController::class, 'updateWeather'])->name('update-integrasi-cuaca');
            Route::put('/integrasi/email', [App\Http\Controllers\IntegrasiController::class, 'updateEmail'])->name('update-integrasi-email');
        });

        // Aktiviti Log Routes (Permission-based)
        Route::middleware('permission:aktiviti_log,lihat')->group(function () {
            Route::get('/aktiviti-log', [App\Http\Controllers\AktivitiLogController::class, 'index'])->name('aktiviti-log');
        });

        // Aktiviti Log Keselamatan Routes removed as requested
    });

    // Help Routes
    Route::prefix('help')->name('help.')->group(function () {
        Route::get('/panduan-pengguna', function () {
            return view('help.panduan-pengguna');
        })->name('panduan-pengguna');

        Route::get('/faq', function () {
            return view('help.faq');
        })->name('faq');

        Route::get('/hubungi-sokongan', [App\Http\Controllers\SupportTicketController::class, 'index'])->name('hubungi-sokongan');

        // Support Ticket Routes (Permission-based)
        Route::middleware('permission:sokongan,tambah')->group(function () {
            Route::post('/tickets', [App\Http\Controllers\SupportTicketController::class, 'store'])->name('tickets.store');
        });

        Route::middleware('permission:sokongan,lihat')->group(function () {
            Route::get('/tickets/{id}', [App\Http\Controllers\SupportTicketController::class, 'show'])->name('tickets.show');
            Route::get('/tickets/{id}/export', [App\Http\Controllers\SupportTicketController::class, 'export'])->name('tickets.export');
            Route::post('/tickets/{id}/typing', [App\Http\Controllers\SupportTicketController::class, 'updateTypingStatus'])->name('tickets.typing.update');
            Route::get('/tickets/{id}/typing', [App\Http\Controllers\SupportTicketController::class, 'getTypingStatus'])->name('tickets.typing.get');
        });

        Route::middleware('permission:sokongan,balas')->group(function () {
            Route::post('/tickets/{id}/reply', [App\Http\Controllers\SupportTicketController::class, 'reply'])->name('tickets.reply');
        });

        Route::middleware('permission:sokongan,tugaskan')->group(function () {
            Route::post('/tickets/{id}/assign', [App\Http\Controllers\SupportTicketController::class, 'assignUser'])->name('tickets.assign');
            Route::post('/tickets/{id}/participants', [App\Http\Controllers\SupportTicketController::class, 'addParticipant'])->name('tickets.addParticipant');
            Route::delete('/tickets/{id}/participants/{userId}', [App\Http\Controllers\SupportTicketController::class, 'removeParticipant'])->name('tickets.removeParticipant');
        });

        // Escalate: allow staff to escalate Android tickets to Admin (checked in controller)
        Route::post('/tickets/{id}/escalate', [App\Http\Controllers\SupportTicketController::class, 'escalate'])->name('tickets.escalate');

        Route::middleware('permission:sokongan,tutup')->group(function () {
            Route::post('/tickets/{id}/close', [App\Http\Controllers\SupportTicketController::class, 'close'])->name('tickets.close');
            Route::post('/tickets/{id}/reopen', [App\Http\Controllers\SupportTicketController::class, 'reopen'])->name('tickets.reopen');
        });

        Route::middleware('permission:sokongan,padam')->group(function () {
            Route::delete('/tickets/{id}', [App\Http\Controllers\SupportTicketController::class, 'destroy'])->name('tickets.destroy');
        });

        Route::get('/status-sistem', function () {
            return view('help.status-sistem');
        })->name('status-sistem');

        Route::get('/nota-keluaran', [App\Http\Controllers\NotaKeluaranController::class, 'index'])->name('nota-keluaran');

        Route::get('/api-doc', [App\Http\Controllers\ApiDokumentasiController::class, 'index'])->name('api-dokumentasi');
        Route::get('/api-doc/{module}/{endpoint}', [App\Http\Controllers\ApiDokumentasiController::class, 'show'])->name('api-endpoint-detail');
    });

    // Internal API routes (for AJAX calls)
    Route::prefix('api')->middleware('auth')->group(function () {
        Route::get('/users/list', [App\Http\Controllers\Api\UserController::class, 'list'])->name('api.users.list');

        // Vehicle Usage Report Snapshots
        Route::get('/snapshots/vehicle-usage', [App\Http\Controllers\VehicleUsageReportController::class, 'index'])->name('api.snapshots.vehicle-usage.index');
        Route::get('/snapshots/vehicle-usage/{id}', [App\Http\Controllers\VehicleUsageReportController::class, 'show'])->name('api.snapshots.vehicle-usage.show');
        Route::post('/snapshots/vehicle-usage', [App\Http\Controllers\VehicleUsageReportController::class, 'store'])->name('api.snapshots.vehicle-usage.store');
        Route::delete('/snapshots/vehicle-usage/{id}', [App\Http\Controllers\VehicleUsageReportController::class, 'destroy'])->name('api.snapshots.vehicle-usage.destroy');
        Route::get('/snapshots/vehicle-usage/{id}/pdf', [App\Http\Controllers\VehicleUsageReportController::class, 'pdf'])->name('api.snapshots.vehicle-usage.pdf');
    });
});

require __DIR__.'/auth.php';
