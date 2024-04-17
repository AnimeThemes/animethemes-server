<?php

declare(strict_types=1);

namespace App\Filament\TableActions\Repositories\Storage\Wiki\Video\Script;

use App\Concerns\Repositories\Wiki\Video\ReconcilesScriptRepositories;
use App\Constants\Config\VideoConstants;
use App\Filament\TableActions\Repositories\Storage\ReconcileStorageTableAction;
use Illuminate\Support\Facades\Config;

/**
 * Class ReconcileScriptTableAction.
 */
class ReconcileScriptTableAction extends ReconcileStorageTableAction
{
    use ReconcilesScriptRepositories;

    /**
     * The name of the disk.
     *
     * @return string
     */
    public function disk(): string
    {
        return Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED);
    }
}
