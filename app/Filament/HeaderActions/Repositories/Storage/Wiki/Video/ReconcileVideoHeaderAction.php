<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Repositories\Storage\Wiki\Video;

use App\Concerns\Repositories\Wiki\ReconcilesVideoRepositories;
use App\Constants\Config\VideoConstants;
use App\Filament\HeaderActions\Repositories\Storage\ReconcileStorageHeaderAction;
use Illuminate\Support\Facades\Config;

/**
 * Class ReconcileVideoHeaderAction.
 */
class ReconcileVideoHeaderAction extends ReconcileStorageHeaderAction
{
    use ReconcilesVideoRepositories;

    /**
     * The name of the disk.
     *
     * @return string
     */
    public function disk(): string
    {
        return Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED);
    }
}
