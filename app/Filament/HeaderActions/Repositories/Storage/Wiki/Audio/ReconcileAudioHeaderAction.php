<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Repositories\Storage\Wiki\Audio;

use App\Concerns\Repositories\Wiki\ReconcilesAudioRepositories;
use App\Constants\Config\AudioConstants;
use App\Filament\HeaderActions\Repositories\Storage\ReconcileStorageHeaderAction;
use Illuminate\Support\Facades\Config;

/**
 * Class ReconcileAudioHeaderAction.
 */
class ReconcileAudioHeaderAction extends ReconcileStorageHeaderAction
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
