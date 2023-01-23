<?php

declare(strict_types=1);

namespace Database\Seeders\Wiki\Audio;

use App\Actions\Repositories\Wiki\Audio\ReconcileAudioRepositoriesAction;
use App\Repositories\Eloquent\Wiki\AudioRepository as AudioDestinationRepository;
use App\Repositories\Storage\Wiki\AudioRepository as AudioSourceRepository;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

/**
 * Class AudioSeeder.
 */
class AudioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $sourceRepository = App::make(AudioSourceRepository::class);

        $destinationRepository = App::make(AudioDestinationRepository::class);

        $action = new ReconcileAudioRepositoriesAction();

        $results = $action->reconcileRepositories($sourceRepository, $destinationRepository);

        $results->toLog();
        $results->toConsole($this->command);
    }
}
