<?php

declare(strict_types=1);

namespace App\Listeners\Database;

use Illuminate\Database\Events\DatabaseBusy;
use Illuminate\Support\Facades\Log;

/**
 * Class LogDatabaseBusy.
 */
class LogDatabaseBusy
{
    /**
     * Handle the event.
     *
     * @param  DatabaseBusy  $event
     * @return void
     */
    public function handle(DatabaseBusy $event): void
    {
        Log::info('DatabaseBusy', [
            'connectionName' => $event->connectionName,
            'connections' => $event->connections,
        ]);
    }
}
