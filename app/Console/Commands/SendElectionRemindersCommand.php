<?php

namespace App\Console\Commands;

use App\Services\ElectionReminderService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendElectionRemindersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pemira:send-election-reminders {--now= : Waktu acuan pengiriman reminder (Y-m-d H:i:s)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim email pengingat pembukaan dan penutupan voting PEMIRA';

    public function handle(ElectionReminderService $service): int
    {
        $nowOption = $this->option('now');
        $reference = $nowOption ? Carbon::parse($nowOption) : Carbon::now();

        $this->info("Memeriksa jadwal reminder PEMIRA untuk waktu acuan: {$reference->toDateTimeString()}");

        $results = $service->sendReminders($reference);

        if (empty($results)) {
            $this->info('Tidak ada reminder yang perlu dikirimkan saat ini.');

            return self::SUCCESS;
        }

        $rows = [];
        foreach ($results as $result) {
            $rows[] = [
                $result['election_id'],
                $result['election_name'],
                $result['start_reminders_sent'],
                $result['end_reminders_sent'],
            ];
        }

        $this->table(['Election ID', 'Nama PEMIRA', 'Start Reminders', 'End Reminders'], $rows);
        $this->info('Proses pengiriman reminder selesai.');

        return self::SUCCESS;
    }
}
