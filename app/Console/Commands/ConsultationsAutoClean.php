<?php

namespace App\Console\Commands;

use App\Models\ConsultationSystemMessage;
use App\Models\ConsultationThread;
use Illuminate\Console\Command;

class ConsultationsAutoClean extends Command
{
    protected $signature = 'consultations:auto-clean';

    protected $description = 'Auto-close inactive consultation threads and delete old closed threads.';

    public function handle(): int
    {
        $now = now();

        $inactiveCutoff = $now->copy()->subDays(7);
        $closeCount = 0;

        $threadsToClose = ConsultationThread::query()
            ->where('status', '!=', 'closed')
            ->where('updated_at', '<', $inactiveCutoff)
            ->get(['id', 'status']);

        foreach ($threadsToClose as $thread) {
            $thread->update([
                'status' => 'closed',
                'closed_at' => $now,
            ]);

            ConsultationSystemMessage::create([
                'thread_id' => $thread->id,
                'content' => 'Auto-closed due to inactivity',
                'type' => 'system_notification',
                'metadata' => [
                    'rule' => 'inactivity',
                ],
            ]);

            $closeCount++;
        }

        $deleteCutoff = $now->copy()->subMonths(6);

        $deleteCount = ConsultationThread::query()
            ->where('status', 'closed')
            ->whereNotNull('closed_at')
            ->where('closed_at', '<', $deleteCutoff)
            ->delete();

        $this->info("Auto-closed {$closeCount} thread(s). Soft-deleted {$deleteCount} thread(s).");

        return self::SUCCESS;
    }
}
