<?php

declare(strict_types=1);

namespace App\Filament\TableActions\Repositories\Storage\Wiki\Video;

use App\Concerns\Repositories\Wiki\ReconcilesVideoRepositories;
use App\Constants\Config\VideoConstants;
use App\Filament\TableActions\Repositories\Storage\ReconcileStorageTableAction;
use Illuminate\Support\Facades\Config;

/**
 * Class ReconcileVideoTableAction.
 */
class ReconcileVideoTableAction extends ReconcileStorageTableAction
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
