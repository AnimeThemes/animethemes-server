<?php

declare(strict_types=1);

namespace App\Actions\Storage\Wiki\Audio;

use App\Actions\Repositories\ReconcileRepositories;
use App\Actions\Repositories\Wiki\Audio\ReconcileAudioRepositories;
use App\Actions\Storage\Base\UploadAction;
use App\Constants\Config\AudioConstants;
use App\Contracts\Repositories\RepositoryInterface;
use App\Repositories\Eloquent\Wiki\AudioRepository as AudioDestinationRepository;
use App\Repositories\Storage\Wiki\AudioRepository as AudioSourceRepository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

/**
 * Class UploadAudioAction.
 */
class UploadAudioAction extends UploadAction
{
    /**
     * Get the disks to update.
     *
     * @return array
     */
    protected function disks(): array
    {
        return Config::get(AudioConstants::DISKS_QUALIFIED);
    }

    /**
     * Get source repository for action.
     *
     * @return RepositoryInterface
     */
    protected function getSourceRepository(): RepositoryInterface
    {
        return App::make(AudioSourceRepository::class);
    }

    /**
     * Get destination repository for action.
     *
     * @return RepositoryInterface
     */
    protected function getDestinationRepository(): RepositoryInterface
    {
        return App::make(AudioDestinationRepository::class);
    }

    /**
     * Get the reconcile action.
     *
     * @return ReconcileRepositories
     */
    protected function action(): ReconcileRepositories
    {
        return new ReconcileAudioRepositories();
    }
}
