<?php

declare(strict_types=1);

namespace App\Listeners\Queue;

use Illuminate\Queue\Events\QueueBusy;
use Illuminate\Support\Facades\Log;

/**
 * Class LogQueueBusy.
 */
class LogQueueBusy
{
    /**
     * Handle the event.
     *
     * @param  QueueBusy  $event
     * @return void
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
