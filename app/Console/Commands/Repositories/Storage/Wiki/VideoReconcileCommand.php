<?php

declare(strict_types=1);

namespace App\Console\Commands\Repositories\Storage\Wiki;

use App\Concerns\Repositories\Wiki\ReconcilesVideoRepositories;
use App\Console\Commands\Repositories\Storage\StorageReconcileCommand;
use App\Constants\Config\VideoConstants;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Support\Facades\Config;

#[Signature(
    'reconcile:video
    {--path= : The directory of videos to reconcile. Ex: 2022/Spring/. If unspecified, all directories will be listed.}'
)]
#[Description('Perform set reconciliation between object storage and video database')]
class VideoReconcileCommand extends StorageReconcileCommand
{
    use ReconcilesVideoRepositories;

    public function disk(): string
    {
        return Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED);
    }
}
