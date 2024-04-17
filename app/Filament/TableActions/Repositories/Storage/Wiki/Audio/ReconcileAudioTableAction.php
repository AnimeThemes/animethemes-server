<?php

declare(strict_types=1);

namespace App\Filament\TableActions\Repositories\Storage\Wiki\Audio;

use App\Concerns\Repositories\Wiki\ReconcilesAudioRepositories;
use App\Constants\Config\AudioConstants;
use App\Filament\TableActions\Repositories\Storage\ReconcileStorageTableAction;
use Illuminate\Support\Facades\Config;

/**
 * Class ReconcileAudioTableAction.
 */
class ReconcileAudioTableAction extends ReconcileStorageTableAction
{
    use ReconcilesAudioRepositories;

    /**
     * The name of the disk.
     *
     * @return string
     */
    public function disk(): string
    {
        return Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED);
    }
}
