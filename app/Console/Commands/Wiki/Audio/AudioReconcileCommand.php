<?php

declare(strict_types=1);

namespace App\Console\Commands\Wiki\Audio;

use App\Actions\Repositories\Wiki\Audio\ReconcileAudioRepositories;
use App\Repositories\Eloquent\Wiki\AudioRepository as AudioDestinationRepository;
use App\Repositories\Storage\Wiki\AudioRepository as AudioSourceRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

/**
 * Class AudioReconcileCommand.
 */
class AudioReconcileCommand extends Command
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
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $sourceRepository = App::make(AudioSourceRepository::class);

        $destinationRepository = App::make(AudioDestinationRepository::class);

        $path = $this->option('path');
        if ($path !== null) {
            if (! $sourceRepository->validateFilter('path', $path) || ! $destinationRepository->validateFilter('path', $path)) {
                $this->error("Invalid path '$path'");

                return 1;
            }

            $sourceRepository->handleFilter('path', $path);
            $destinationRepository->handleFilter('path', $path);
        }

        $action = new ReconcileAudioRepositories();

        $results = $action->reconcileRepositories($sourceRepository, $destinationRepository);

        $results->toLog();
        $results->toConsole($this);

        return 0;
    }
}
