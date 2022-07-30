<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Actions\Wiki\Video\BackfillAudio;
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
        $action = new BackfillAudio();

        Video::query()
            ->whereDoesntHave(Video::RELATION_AUDIO)
            ->chunkById(100, fn (Collection $videos) => $videos->each(fn (Video $video) => $action->backfill($video)));
    }
}
