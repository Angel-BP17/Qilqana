<?php

namespace App\Jobs;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LogActivityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected array $payload
    )
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $userId = $this->payload['user_id'] ?? null;
        if ($userId && !User::whereKey($userId)->exists()) {
            $userId = null;
        }

        ActivityLog::create([
            'user_id' => $userId,
            'action' => $this->payload['action'],
            'model' => $this->payload['model'],
            'before' => $this->payload['before'] ?? null,
            'after' => $this->payload['after'] ?? null,
            'reason' => $this->payload['reason'] ?? null,
        ]);
    }
}
