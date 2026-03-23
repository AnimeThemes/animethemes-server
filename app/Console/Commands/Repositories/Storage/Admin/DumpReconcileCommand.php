<?php

declare(strict_types=1);

namespace App\Console\Commands\Repositories\Storage\Admin;

use App\Concerns\Repositories\Admin\ReconcilesDumpRepositories;
use App\Console\Commands\Repositories\Storage\StorageReconcileCommand;
use App\Constants\Config\DumpConstants;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Support\Facades\Config;

#[Signature('reconcile:dump')]
#[Description('Perform set reconciliation between object storage and dump database')]
class DumpReconcileCommand extends StorageReconcileCommand
{
    use ReconcilesDumpRepositories;

    public function disk(): string
    {
        return Config::get(DumpConstants::DISK_QUALIFIED);
    }
}
