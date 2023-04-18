<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Models\Wiki\Video\Audio;

use App\Actions\Models\Wiki\Video\Audio\BackfillAudioAction;
use App\Constants\Config\AudioConstants;
use App\Constants\Config\VideoConstants;
use App\Enums\Actions\ActionStatus;
use App\Enums\Models\Wiki\VideoSource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Audio;
use App\Models\Wiki\Video;
use Exception;
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
     *
     * @throws Exception
     */
    public function testSkipped(): void
    {
        Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
        Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

        $video = Video::factory()
            ->for(Audio::factory())
            ->createOne();

        $action = new BackfillAudioAction($video);

        $result = $action->handle();

        static::assertTrue(ActionStatus::SKIPPED()->is($result->getStatus()));
        static::assertDatabaseCount(Audio::class, 1);
        static::assertEmpty(Storage::disk(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED))->allFiles());
    }

    /**
     * The Backfill Audio Action shall fail if the Video is not attached to any Entries.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testFailedWhenNoEntries(): void
    {
        Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
        Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

        $video = Video::factory()->createOne();

        $action = new BackfillAudioAction($video);

        $result = $action->handle();

        static::assertTrue($result->hasFailed());
        static::assertDatabaseCount(Audio::class, 0);
        static::assertEmpty(Storage::disk(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED))->allFiles());
    }

    /**
     * The Backfill Audio Action shall pass if the Video is a source.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testPassesSourceVideo(): void
    {
        Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
        Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

        $video = Video::factory()
            ->has(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->createOne([
                Video::ATTRIBUTE_PATH => $this->faker()->word().'.webm',
            ]);

        Audio::factory()->createOne([
            Audio::ATTRIBUTE_PATH => Str::replace('webm', 'ogg', $video->path),
        ]);

        $action = new BackfillAudioAction($video);

        $result = $action->handle();

        static::assertTrue(ActionStatus::PASSED()->is($result->getStatus()));
        static::assertDatabaseCount(Audio::class, 1);
        static::assertTrue($video->audio()->exists());
        static::assertEmpty(Storage::disk(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED))->allFiles());
    }

    /**
     * The Backfill Audio Action shall pass if the Video has a higher priority source.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testPassesWithHigherPrioritySource(): void
    {
        Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
        Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

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

        $action = new BackfillAudioAction($video);

        $result = $action->handle();

        static::assertTrue(ActionStatus::PASSED()->is($result->getStatus()));
        static::assertDatabaseCount(Audio::class, 1);
        static::assertTrue($video->audio()->exists());
        static::assertEmpty(Storage::disk(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED))->allFiles());
    }

    /**
     * The Backfill Audio Action shall pass if the Video has a primary version source.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testPassesWithPrimaryVersionSource(): void
    {
        Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
        Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

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

        $action = new BackfillAudioAction($video);

        $result = $action->handle();

        static::assertTrue(ActionStatus::PASSED()->is($result->getStatus()));
        static::assertDatabaseCount(Audio::class, 2);
        static::assertTrue($video->audio()->is($sourceAudio));
        static::assertEmpty(Storage::disk(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED))->allFiles());
    }
}
