<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Actions\Repositories\Wiki\Video\ReconcileVideoRepositories;
use App\Repositories\Eloquent\Wiki\VideoRepository as VideoDestinationRepository;
use App\Repositories\Storage\Wiki\VideoRepository as VideoSourceRepository;
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
     */
    public function run(): void
    {
        $sourceRepository = App::make(VideoSourceRepository::class);

        $destinationRepository = App::make(VideoDestinationRepository::class);

        $action = new ReconcileVideoRepositories();

        $results = $action->reconcileRepositories($sourceRepository, $destinationRepository);

        $results->toLog();
        $results->toConsole($this->command);
    }
}
