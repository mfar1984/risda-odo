<?php

namespace App\Console\Commands;

use App\Models\Program;
use App\Models\User;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Kreait\Firebase\Messaging\CloudMessage;

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

            // Send notification to ADMIN (backend bell)
            Notification::create([
                'user_id' => null, // Global for all admins
                'type' => 'program_auto_closed',
                'title' => 'Program Auto-Ditutup',
                'message' => "Program '{$program->nama_program}' telah ditutup secara automatik kerana tarikh selesai telah tamat.",
                'data' => [
                    'program_id' => $program->id,
                    'program_name' => $program->nama_program,
                    'closed_at' => $sebenarSelesai,
                ],
                'action_url' => "/program/{$program->id}",
            ]);

            // Send notification to DRIVER (mobile app)
            $driver = User::where('staf_id', $program->pemandu_id)->first();
            if ($driver) {
                // Database notification
                Notification::create([
                    'user_id' => $driver->id,
                    'type' => 'program_auto_closed',
                    'title' => 'Program Selesai',
                    'message' => "Program '{$program->nama_program}' anda telah selesai. Terima kasih atas perkhidmatan anda!",
                    'data' => [
                        'program_id' => $program->id,
                        'program_name' => $program->nama_program,
                    ],
                    'action_url' => "/program/{$program->id}",
                ]);

                // FCM push notification
                $this->sendFcmNotification(
                    $driver->id,
                    'Program Selesai 🏁',
                    "Program '{$program->nama_program}' telah selesai. Terima kasih!",
                    ['program_id' => $program->id, 'type' => 'program_auto_closed']
                );
            }

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

            // Send notification to ADMIN (backend bell)
            Notification::create([
                'user_id' => null, // Global for all admins
                'type' => 'program_tertunda',
                'title' => 'Program Tertunda',
                'message' => "Program '{$program->nama_program}' ditandakan TERTUNDA kerana tiada perjalanan dimulakan.",
                'data' => [
                    'program_id' => $program->id,
                    'program_name' => $program->nama_program,
                    'scheduled_start' => $program->tarikh_mula,
                ],
                'action_url' => "/program/{$program->id}",
            ]);

            // Send notification to DRIVER (mobile app)
            $driver = User::where('staf_id', $program->pemandu_id)->first();
            if ($driver) {
                // Database notification
                Notification::create([
                    'user_id' => $driver->id,
                    'type' => 'program_tertunda',
                    'title' => 'Program Tertunda',
                    'message' => "Program '{$program->nama_program}' telah ditandakan tertunda. Sila hubungi pentadbir.",
                    'data' => [
                        'program_id' => $program->id,
                        'program_name' => $program->nama_program,
                    ],
                    'action_url' => "/program/{$program->id}",
                ]);

                // FCM push notification
                $this->sendFcmNotification(
                    $driver->id,
                    'Program Tertunda ⚠️',
                    "Program '{$program->nama_program}' telah ditandakan tertunda.",
                    ['program_id' => $program->id, 'type' => 'program_tertunda']
                );
            }

            $updated++;
            $this->info("Program #{$program->id} - {$program->nama_program}: LULUS → TERTUNDA (no journey started)");
        }

        $this->info("✅ Total programs updated: {$updated}");

        return Command::SUCCESS;
    }

    /**
     * Send FCM push notification to user
     */
    private function sendFcmNotification(int $userId, string $title, string $body, array $data = [])
    {
        try {
            $tokens = \App\Models\FcmToken::where('user_id', $userId)->pluck('token')->toArray();

            if (empty($tokens)) {
                return;
            }

            $firebase = app('firebase.messaging');

            foreach ($tokens as $token) {
                try {
                    $message = CloudMessage::withTarget('token', $token)
                        ->withNotification([
                            'title' => $title,
                            'body' => $body,
                        ])
                        ->withData($data);

                    $firebase->send($message);
                } catch (\Exception $e) {
                    // Token might be invalid, continue with next token
                    continue;
                }
            }
        } catch (\Exception $e) {
            $this->error("FCM Error: " . $e->getMessage());
        }
    }
}
