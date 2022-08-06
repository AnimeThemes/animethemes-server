<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Actions\Models\Wiki\Video\Audio\BackfillVideoAudioAction;
use App\Models\Wiki\Video;
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
     */
    public function run(): void
    {
        Video::query()
            ->whereDoesntHave(Video::RELATION_AUDIO)
            ->chunkById(100, fn (Collection $videos) => $videos->each(function (Video $video) {
                $action = new BackfillVideoAudioAction($video);

                $action->handle();
            }));
    }
}
