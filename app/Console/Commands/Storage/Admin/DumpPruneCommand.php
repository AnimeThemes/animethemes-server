<?php

declare(strict_types=1);

namespace App\Console\Commands\Storage\Admin;

use App\Actions\Storage\Admin\Dump\PruneDumpAction;
use App\Actions\Storage\Base\PruneAction;
use App\Console\Commands\Storage\Base\PruneCommand;

class DumpPruneCommand extends PruneCommand
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'prune:dump {--H|hours=72 : The number of hours to retain dumps}';

    protected function getAction(): PruneAction
    {
        $hours = $this->option('hours');

        return new PruneDumpAction(intval($hours));
    }
}
