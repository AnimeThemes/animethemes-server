<?php

declare(strict_types=1);

namespace App\Nova\Actions\Wiki\Audio;

use App\Actions\Repositories\ReconcileRepositories;
use App\Actions\Repositories\Wiki\Audio\ReconcileAudioRepositories;
use App\Contracts\Repositories\RepositoryInterface;
use App\Nova\Actions\Wiki\ReconcileStorageAction;
use App\Repositories\Eloquent\Wiki\AudioRepository as AudioDestinationRepository;
use App\Repositories\Storage\Wiki\AudioRepository as AudioSourceRepository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Laravel\Nova\Fields\ActionFields;

/**
 * Class ReconcileAudioAction.
 */
class ReconcileAudioAction extends ReconcileStorageAction
{
    /**
     * Get the displayable name of the action.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function name(): string
    {
        return __('nova.reconcile_audios');
    }

    /**
     * Get the name of the disk that represents the filesystem.
     *
     * @return string
     */
    protected function disk(): string
    {
        return Config::get('audio.disk');
    }

    /**
     * Get source repository for action.
     *
     * @param  ActionFields  $fields
     * @return RepositoryInterface|null
     */
    protected function getSourceRepository(ActionFields $fields): ?RepositoryInterface
    {
        return App::make(AudioSourceRepository::class);
    }

    /**
     * Get destination repository for action.
     *
     * @param  ActionFields  $fields
     * @return RepositoryInterface|null
     */
    protected function getDestinationRepository(ActionFields $fields): ?RepositoryInterface
    {
        return App::make(AudioDestinationRepository::class);
    }

    /**
     * Get the reconciliation action.
     *
     * @return ReconcileRepositories
     */
    protected function getAction(): ReconcileRepositories
    {
        return new ReconcileAudioRepositories();
    }
}
