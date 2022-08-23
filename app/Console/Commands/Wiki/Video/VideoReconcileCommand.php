<?php

declare(strict_types=1);

namespace App\Console\Commands\Wiki\Video;

use App\Actions\Repositories\ReconcileRepositories;
use App\Actions\Repositories\Wiki\Video\ReconcileVideoRepositories;
use App\Console\Commands\Wiki\StorageReconcileCommand;
use App\Constants\Config\VideoConstants;
use App\Contracts\Repositories\RepositoryInterface;
use App\Repositories\Eloquent\Wiki\VideoRepository as VideoDestinationRepository;
use App\Repositories\Storage\Wiki\VideoRepository as VideoSourceRepository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

/**
 * Class VideoReconcileCommand.
 */
class VideoReconcileCommand extends StorageReconcileCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reconcile:video
                                {--path= : The directory of videos to reconcile. Ex: 2022/Spring/. If unspecified, all directories will be listed.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform set reconciliation between object storage and video database';

    /**
     * Get the name of the disk that represents the filesystem.
     *
     * @return string
     */
    protected function disk(): string
    {
        return Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED);
    }

    /**
     * Get source repository for action.
     *
     * @param  array  $validated
     * @return RepositoryInterface|null
     */
    protected function getSourceRepository(array $validated): ?RepositoryInterface
    {
        return App::make(VideoSourceRepository::class);
    }

    /**
     * Get destination repository for action.
     *
     * @param  array  $validated
     * @return RepositoryInterface|null
     */
    protected function getDestinationRepository(array $validated): ?RepositoryInterface
    {
        return App::make(VideoDestinationRepository::class);
    }

    /**
     * Get the reconciliation action.
     *
     * @return ReconcileRepositories
     */
    protected function getAction(): ReconcileRepositories
    {
        return new ReconcileVideoRepositories();
    }
}
