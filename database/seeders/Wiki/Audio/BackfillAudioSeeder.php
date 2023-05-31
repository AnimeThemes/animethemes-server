<?php

declare(strict_types=1);

namespace Database\Seeders\Wiki\Audio;

use App\Actions\Models\Wiki\Video\Audio\BackfillAudioAction;
use App\Models\Wiki\Video;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

/**
 * Class BackfillAudioSeeder.
 */
class BackfillAudioSeeder extends Seeder
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
        Video::query()
            ->whereDoesntHave(Video::RELATION_AUDIO)
            ->chunkById(100, fn (Collection $videos) => $videos->each(function (Video $video) {
                $action = new BackfillAudioAction($video);

                $action->handle();
            }));
    }
}
