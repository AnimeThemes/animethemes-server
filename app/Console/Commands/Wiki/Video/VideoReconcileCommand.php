<?php

declare(strict_types=1);

namespace App\Console\Commands\Wiki\Video;

use App\Actions\Repositories\Wiki\Video\ReconcileVideoRepositories;
use App\Repositories\Eloquent\Wiki\VideoRepository as VideoDestinationRepository;
use App\Repositories\Storage\Wiki\VideoRepository as VideoSourceRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

/**
 * Class VideoReconcileCommand.
 */
class VideoReconcileCommand extends Command
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
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $sourceRepository = App::make(VideoSourceRepository::class);

        $destinationRepository = App::make(VideoDestinationRepository::class);

        $path = $this->option('path');
        if ($path !== null) {
            if (! $sourceRepository->validateFilter('path', $path) || ! $destinationRepository->validateFilter('path', $path)) {
                $this->error("Invalid path '$path'");

                return 1;
            }

            $sourceRepository->handleFilter('path', $path);
            $destinationRepository->handleFilter('path', $path);
        }

        $action = new ReconcileVideoRepositories();

        $results = $action->reconcileRepositories($sourceRepository, $destinationRepository);

        $results->toLog();
        $results->toConsole($this);

        return 0;
    }
}
