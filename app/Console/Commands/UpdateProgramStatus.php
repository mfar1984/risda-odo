<?php

namespace App\Console\Commands;

use App\Models\Program;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateProgramStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'program:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-update program status based on dates (AKTIF → SELESAI, LULUS → TERTUNDA)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $updated = 0;

        // 1. AKTIF → SELESAI (if tarikh_selesai has passed)
        $completedPrograms = Program::where('status', 'aktif')
            ->where('tarikh_selesai', '<', $now)
            ->get();

        foreach ($completedPrograms as $program) {
            // Use last end journey time or scheduled end time
            $latestEndJourney = \App\Models\LogPemandu::where('program_id', $program->id)
                ->where('status', 'selesai')
                ->orderBy('masa_masuk', 'desc')
                ->first();
            
            $sebenarSelesai = $latestEndJourney ? $latestEndJourney->masa_masuk : $program->tarikh_selesai;
            
            $program->update([
                'status' => 'selesai',
                'tarikh_sebenar_selesai' => $sebenarSelesai,
            ]);
            $updated++;
            $this->info("Program #{$program->id} - {$program->nama_program}: AKTIF → SELESAI (auto-closed at {$sebenarSelesai})");
        }

        // 2. LULUS → TERTUNDA (if tarikh_mula has passed and no journey started)
        $pendingPrograms = Program::where('status', 'lulus')
            ->where('tarikh_mula', '<', $now)
            ->whereDoesntHave('logPemandu') // No log pemandu created
            ->get();

        foreach ($pendingPrograms as $program) {
            $program->update(['status' => 'tertunda']);
            $updated++;
            $this->info("Program #{$program->id} - {$program->nama_program}: LULUS → TERTUNDA (no journey started)");
        }

        $this->info("✅ Total programs updated: {$updated}");

        return Command::SUCCESS;
    }
}
