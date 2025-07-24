<?php

declare(strict_types=1);

namespace App\Listeners\Queue;

use Illuminate\Queue\Events\QueueBusy;
use Illuminate\Support\Facades\Log;

class LogQueueBusy
{
    /**
     * Handle the event.
     */
    public function handle(QueueBusy $event): void
    {
        Log::info('QueueBusy', [
            'connection' => $event->connection,
            'remember' => $event->queue,
            'size' => $event->size,
        ]);
    }
}
