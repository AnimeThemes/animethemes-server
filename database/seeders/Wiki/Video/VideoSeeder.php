<?php

declare(strict_types=1);

namespace Database\Seeders\Wiki\Video;

use App\Actions\Repositories\Wiki\Video\ReconcileVideoRepositoriesAction;
use App\Repositories\Eloquent\Wiki\VideoRepository as VideoDestinationRepository;
use App\Repositories\Storage\Wiki\VideoRepository as VideoSourceRepository;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

/**
 * Class VideoSeeder.
 */
class VideoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     *
     * @throws Exception
     */
    public function run(): void
    {
        $sourceRepository = App::make(VideoSourceRepository::class);

        $destinationRepository = App::make(VideoDestinationRepository::class);

        $action = new ReconcileVideoRepositoriesAction();

        $results = $action->reconcileRepositories($sourceRepository, $destinationRepository);

        $results->toLog();
        $results->toConsole($this->command);
    }
}
