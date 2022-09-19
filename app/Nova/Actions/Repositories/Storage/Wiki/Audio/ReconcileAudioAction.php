<?php

declare(strict_types=1);

namespace App\Nova\Actions\Repositories\Storage\Wiki\Audio;

use App\Concerns\Repositories\Wiki\ReconcilesAudioRepositories;
use App\Constants\Config\AudioConstants;
use App\Nova\Actions\Repositories\Storage\ReconcileStorageAction;
use Illuminate\Support\Facades\Config;

/**
 * Class ReconcileAudioAction.
 */
class ReconcileAudioAction extends ReconcileStorageAction
{
    use ReconcilesAudioRepositories;

    /**
     * Get the displayable name of the action.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function name(): string
    {
        return __('nova.actions.repositories.name', ['label' => __('nova.resources.label.audios')]);
    }

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
