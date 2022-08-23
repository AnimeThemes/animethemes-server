<?php

declare(strict_types=1);

namespace App\Console\Commands\Wiki\Audio;

use App\Actions\Repositories\ReconcileRepositories;
use App\Actions\Repositories\Wiki\Audio\ReconcileAudioRepositories;
use App\Console\Commands\Wiki\StorageReconcileCommand;
use App\Constants\Config\AudioConstants;
use App\Contracts\Repositories\RepositoryInterface;
use App\Repositories\Eloquent\Wiki\AudioRepository as AudioDestinationRepository;
use App\Repositories\Storage\Wiki\AudioRepository as AudioSourceRepository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

/**
 * Class AudioReconcileCommand.
 */
class AudioReconcileCommand extends StorageReconcileCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reconcile:audio
                                {--path= : The directory of audios to reconcile. Ex: 2022/Spring/. If unspecified, all directories will be listed.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform set reconciliation between object storage and audios database';

    /**
     * Get the name of the disk that represents the filesystem.
     *
     * @return string
     */
    protected function disk(): string
    {
        return Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED);
    }

    /**
     * Get source repository for action.
     *
     * @param  array  $validated
     * @return RepositoryInterface|null
     */
    protected function getSourceRepository(array $validated): ?RepositoryInterface
    {
        return App::make(AudioSourceRepository::class);
    }

    /**
     * Get destination repository for action.
     *
     * @param  array  $validated
     * @return RepositoryInterface|null
     */
    protected function getDestinationRepository(array $validated): ?RepositoryInterface
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
