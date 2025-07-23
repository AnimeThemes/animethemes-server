<?php

declare(strict_types=1);

namespace App\Console\Commands\Repositories\Storage\Admin;

use App\Concerns\Repositories\Admin\ReconcilesDumpRepositories;
use App\Console\Commands\Repositories\Storage\StorageReconcileCommand;
use App\Constants\Config\DumpConstants;
use Illuminate\Support\Facades\Config;

class DumpReconcileCommand extends StorageReconcileCommand
{
    use ReconcilesDumpRepositories;

    /**
     * The name and signature of the console command.
     */
    protected $signature = 'reconcile:dump';

    /**
     * The console command description.
     */
    protected $description = 'Perform set reconciliation between object storage and dump database';

    /**
     * The name of the disk.
     */
    public function disk(): string
    {
        return Config::get(DumpConstants::DISK_QUALIFIED);
    }
}
