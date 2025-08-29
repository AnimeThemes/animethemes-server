<?php

declare(strict_types=1);

namespace App\Console\Commands\Repositories\Storage\Wiki;

use App\Concerns\Repositories\Wiki\ReconcilesVideoRepositories;
use App\Console\Commands\Repositories\Storage\StorageReconcileCommand;
use App\Constants\Config\VideoConstants;
use Illuminate\Support\Facades\Config;

class VideoReconcileCommand extends StorageReconcileCommand
{
    use ReconcilesVideoRepositories;

    protected $signature = 'reconcile:video
                                {--path= : The directory of videos to reconcile. Ex: 2022/Spring/. If unspecified, all directories will be listed.}';

    protected $description = 'Perform set reconciliation between object storage and video database';

    public function disk(): string
    {
        return Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED);
    }
}
