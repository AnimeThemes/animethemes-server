<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Models\Wiki\Video\Audio;

use App\Actions\Models\Wiki\Video\Audio\BackfillVideoAudioAction;
use App\Enums\Actions\ActionStatus;
use App\Enums\Models\Wiki\VideoSource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Audio;
use App\Models\Wiki\Video;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class BackfillVideoAudioTest.
 */
class BackfillVideoAudioTest extends TestCase
{
    use WithFaker;

    /**
     * The Backfill Audio Action shall skip the Anime if the relation already exists.
     *
     * @return void
     */
    public function testSkipped(): void
    {
        Storage::fake(Config::get('video.disk'));
        Storage::fake(Config::get('audio.disk'));

        $video = Video::factory()
            ->for(Audio::factory())
            ->createOne();

        $action = new BackfillVideoAudioAction($video);

        $result = $action->handle();

        static::assertTrue(ActionStatus::SKIPPED()->is($result->getStatus()));
        static::assertDatabaseCount(Audio::class, 1);
        static::assertEmpty(Storage::disk(Config::get('audio.disk'))->allFiles());
    }

    /**
     * The Backfill Audio Action shall fail if the Video is not attached to any Entries.
     *
     * @return void
     */
    public function testFailedWhenNoEntries(): void
    {
        Storage::fake(Config::get('video.disk'));
        Storage::fake(Config::get('audio.disk'));

        $video = Video::factory()->createOne();

        $action = new BackfillVideoAudioAction($video);

        $result = $action->handle();

        static::assertTrue($result->hasFailed());
        static::assertDatabaseCount(Audio::class, 0);
        static::assertEmpty(Storage::disk(Config::get('audio.disk'))->allFiles());
    }

    /**
     * The Backfill Audio Action shall pass if the Video is a source.
     *
     * @return void
     */
    public function testPassesSourceVideo(): void
    {
        Storage::fake(Config::get('video.disk'));
        Storage::fake(Config::get('audio.disk'));

        $video = Video::factory()
            ->has(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->createOne([
                Video::ATTRIBUTE_PATH => $this->faker()->word().'.webm',
            ]);

        Audio::factory()->createOne([
            Audio::ATTRIBUTE_PATH => Str::replace('webm', 'ogg', $video->path),
        ]);

        $action = new BackfillVideoAudioAction($video);

        $result = $action->handle();

        static::assertTrue(ActionStatus::PASSED()->is($result->getStatus()));
        static::assertDatabaseCount(Audio::class, 1);
        static::assertTrue($video->audio()->exists());
        static::assertEmpty(Storage::disk(Config::get('audio.disk'))->allFiles());
    }

    /**
     * The Backfill Audio Action shall pass if the Video has a higher priority source.
     *
     * @return void
     */
    public function testPassesWithHigherPrioritySource(): void
    {
        Storage::fake(Config::get('video.disk'));
        Storage::fake(Config::get('audio.disk'));

        $entry = AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory()));

        Video::factory()
            ->hasAttached($entry, [], Video::RELATION_ANIMETHEMEENTRIES)
            ->for(Audio::factory())
            ->createOne([
                Video::ATTRIBUTE_SOURCE => VideoSource::BD,
            ]);

        $video = Video::factory()
            ->hasAttached($entry, [], Video::RELATION_ANIMETHEMEENTRIES)
            ->createOne([
                Video::ATTRIBUTE_SOURCE => VideoSource::WEB,
            ]);

        $action = new BackfillVideoAudioAction($video);

        $result = $action->handle();

        static::assertTrue(ActionStatus::PASSED()->is($result->getStatus()));
        static::assertDatabaseCount(Audio::class, 1);
        static::assertTrue($video->audio()->exists());
        static::assertEmpty(Storage::disk(Config::get('audio.disk'))->allFiles());
    }

    /**
     * The Backfill Audio Action shall pass if the Video has a primary version source.
     *
     * @return void
     */
    public function testPassesWithPrimaryVersionSource(): void
    {
        Storage::fake(Config::get('video.disk'));
        Storage::fake(Config::get('audio.disk'));

        $theme = AnimeTheme::factory()
            ->for(Anime::factory())
            ->createOne();

        $videoAttributes = Video::factory()->raw();

        $sourceAudio = Audio::factory()->createOne();

        Video::factory()
            ->has(AnimeThemeEntry::factory()->set(AnimeThemeEntry::ATTRIBUTE_VERSION, 1)->for($theme))
            ->for($sourceAudio)
            ->createOne($videoAttributes);

        Video::factory()
            ->has(AnimeThemeEntry::factory()->set(AnimeThemeEntry::ATTRIBUTE_VERSION, 2)->for($theme))
            ->for(Audio::factory())
            ->createOne($videoAttributes);

        $video = Video::factory()
            ->has(AnimeThemeEntry::factory()->set(AnimeThemeEntry::ATTRIBUTE_VERSION, 3)->for($theme))
            ->createOne($videoAttributes);

        $action = new BackfillVideoAudioAction($video);

        $result = $action->handle();

        static::assertTrue(ActionStatus::PASSED()->is($result->getStatus()));
        static::assertDatabaseCount(Audio::class, 2);
        static::assertTrue($video->audio()->is($sourceAudio));
        static::assertEmpty(Storage::disk(Config::get('audio.disk'))->allFiles());
    }
}
