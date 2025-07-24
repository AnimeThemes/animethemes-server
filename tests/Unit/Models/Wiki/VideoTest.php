<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Wiki;

use App\Constants\Config\VideoConstants;
use App\Enums\Models\List\PlaylistVisibility;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Events\Wiki\Video\VideoForceDeleting;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Audio;
use App\Models\Wiki\Video;
use App\Models\Wiki\Video\VideoScript;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use CyrildeWit\EloquentViewable\View;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class VideoTest extends TestCase
{
    use WithFaker;

    /**
     * The overlap attribute of a video shall be cast to a VideoOverlap enum instance.
     */
    public function testCastsOverlapToEnum(): void
    {
        $video = Video::factory()->createOne();

        $overlap = $video->overlap;

        static::assertInstanceOf(VideoOverlap::class, $overlap);
    }

    /**
     * The source attribute of a video shall be cast to a VideoSource enum instance.
     */
    public function testCastsSourceToEnum(): void
    {
        $video = Video::factory()->createOne();

        $source = $video->source;

        static::assertInstanceOf(VideoSource::class, $source);
    }

    /**
     * Video shall be a searchable resource.
     */
    public function testSearchableAs(): void
    {
        $video = Video::factory()->createOne();

        static::assertIsString($video->searchableAs());
    }

    /**
     * Video shall be a searchable resource.
     */
    public function testToSearchableArray(): void
    {
        $video = Video::factory()->createOne();

        static::assertIsArray($video->toSearchableArray());
    }

    /**
     * Videos shall be nameable.
     */
    public function testNameable(): void
    {
        $video = Video::factory()->createOne();

        static::assertIsString($video->getName());
    }

    /**
     * Videos shall have subtitle.
     */
    public function testHasSubtitle(): void
    {
        $video = Video::factory()->createOne();

        static::assertIsString($video->getSubtitle());
    }

    /**
     * Videos shall have a one-to-many polymorphic relationship to View.
     */
    public function testViews(): void
    {
        $video = Video::factory()->createOne();

        views($video)->record();

        static::assertInstanceOf(MorphMany::class, $video->views());
        static::assertEquals(1, $video->views()->count());
        static::assertInstanceOf(View::class, $video->views()->first());
    }

    /**
     * Videos shall append a 'tags' attribute.
     */
    public function testAppendsTags(): void
    {
        $video = Video::factory()->createOne();

        static::assertArrayHasKey(Video::ATTRIBUTE_TAGS, $video);
    }

    /**
     * The Tags attribute shall contain 'NC' if the NC attribute is true.
     */
    public function testNcTag(): void
    {
        $video = Video::factory()->createOne([
            Video::ATTRIBUTE_NC => true,
        ]);

        static::assertContains('NC', $video->tags);
    }

    /**
     * The Tags attribute shall not contain 'NC' if the NC attribute is false.
     */
    public function testNoNcTag(): void
    {
        $video = Video::factory()->createOne([
            Video::ATTRIBUTE_NC => false,
        ]);

        static::assertNotContains('NC', $video->tags);
    }

    /**
     * The Tags attribute shall contain 'DVD' if the source is DVD.
     */
    public function testDvdTag(): void
    {
        $source = VideoSource::DVD;

        $video = Video::factory()->createOne([
            Video::ATTRIBUTE_SOURCE => $source->value,
        ]);

        static::assertContains($source->localize(), $video->tags);
    }

    /**
     * The Tags attribute shall contain 'BD' if the source is BD.
     */
    public function testBdTag(): void
    {
        $source = VideoSource::BD;

        $video = Video::factory()->createOne([
            Video::ATTRIBUTE_SOURCE => $source->value,
        ]);

        static::assertContains($source->localize(), $video->tags);
    }

    /**
     * The Tags attribute shall not contain the description of the source if not DVD or BD.
     */
    public function testOtherSourceTag(): void
    {
        $source = null;
        while ($source === null) {
            $sourceCandidate = Arr::random(VideoSource::cases());
            if ($sourceCandidate !== VideoSource::BD && $sourceCandidate !== VideoSource::DVD) {
                $source = $sourceCandidate;
            }
        }

        $video = Video::factory()->createOne([
            Video::ATTRIBUTE_SOURCE => $source->value,
        ]);

        static::assertNotContains($source->localize(), $video->tags);
    }

    /**
     * The Tags attribute shall contain resolution.
     */
    public function testResolutionTag(): void
    {
        $video = Video::factory()->createOne();

        static::assertContains(strval($video->resolution), $video->tags);
    }

    /**
     * The Tags attribute shall exclude 720 resolution from tags.
     */
    public function testNo720ResolutionTag(): void
    {
        $video = Video::factory()->createOne([
            Video::ATTRIBUTE_RESOLUTION => 720,
        ]);

        static::assertNotContains(strval($video->resolution), $video->tags);
    }

    /**
     * The Tags attribute shall contain 'Subbed' if the subbed attribute is true.
     */
    public function testSubbedTag(): void
    {
        $video = Video::factory()->createOne([
            Video::ATTRIBUTE_SUBBED => true,
        ]);

        static::assertContains('Subbed', $video->tags);
        static::assertNotContains('Lyrics', $video->tags);
    }

    /**
     * The Tags attribute shall contain 'Lyrics' if the lyrics attribute is true and subbed attribute is false.
     */
    public function testLyricsTag(): void
    {
        $video = Video::factory()->createOne([
            Video::ATTRIBUTE_SUBBED => false,
            Video::ATTRIBUTE_LYRICS => true,
        ]);

        static::assertNotContains('Subbed', $video->tags);
        static::assertContains('Lyrics', $video->tags);
    }

    /**
     * The video source shall be the primary criterion for scoring.
     *
     * @param  array  $a
     * @param  array  $b
     */
    #[DataProvider('priorityProvider')]
    public function testSourcePriority(array $a, array $b): void
    {
        $first = Video::factory()->createOne($a);

        $second = Video::factory()->createOne($b);

        static::assertGreaterThan($first->getSourcePriority(), $second->getSourcePriority());
    }

    /**
     * Videos shall have a many-to-many relationship with the type Entry.
     */
    public function testEntries(): void
    {
        $entryCount = $this->faker->randomDigitNotNull();

        $video = Video::factory()
            ->has(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory()))->count($entryCount))
            ->createOne();

        static::assertInstanceOf(BelongsToMany::class, $video->animethemeentries());
        static::assertEquals($entryCount, $video->animethemeentries()->count());
        static::assertInstanceOf(AnimeThemeEntry::class, $video->animethemeentries()->first());
        static::assertEquals(AnimeThemeEntryVideo::class, $video->animethemeentries()->getPivotClass());
    }

    /**
     * Video shall belong to an Audio.
     */
    public function testAudio(): void
    {
        $video = Video::factory()
            ->for(Audio::factory())
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $video->audio());
        static::assertInstanceOf(Audio::class, $video->audio()->first());
    }

    /**
     * Video shall have a one-to-many relationship with the type PlaylistTrack, but only if the playlist is public.
     */
    public function testTracksPublic(): void
    {
        $trackCount = $this->faker->randomDigitNotNull();

        $playlist = Playlist::factory()->createOne([Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC]);
        $video = Video::factory()
            ->has(PlaylistTrack::factory()->for($playlist)->count($trackCount), Video::RELATION_TRACKS)
            ->createOne();

        static::assertInstanceOf(HasMany::class, $video->tracks());
        static::assertEquals($trackCount, $video->tracks()->count());
        static::assertInstanceOf(PlaylistTrack::class, $video->tracks()->first());
    }

    /**
     * Video shall have a one-to-many relationship with the type PlaylistTrack, but only if the playlist is public.
     */
    public function testTracksNotPublic(): void
    {
        $trackCount = $this->faker->randomDigitNotNull();

        $visibility = Arr::random([PlaylistVisibility::PRIVATE, PlaylistVisibility::UNLISTED]);
        $playlist = Playlist::factory()->createOne([Playlist::ATTRIBUTE_VISIBILITY => $visibility]);
        $video = Video::factory()
            ->has(PlaylistTrack::factory()->for($playlist)->count($trackCount), Video::RELATION_TRACKS)
            ->createOne();

        static::assertInstanceOf(HasMany::class, $video->tracks());
        static::assertNotEquals($trackCount, $video->tracks()->count());
    }

    /**
     * Video shall have a one-to-one relationship with the type Script.
     */
    public function testScript(): void
    {
        $video = Video::factory()
            ->has(VideoScript::factory(), Video::RELATION_SCRIPT)
            ->createOne();

        static::assertInstanceOf(HasOne::class, $video->videoscript());
        static::assertInstanceOf(VideoScript::class, $video->videoscript()->first());
    }

    /**
     * Provider for source priority testing.
     *
     * @return array
     */
    public static function priorityProvider(): array
    {
        return [
            [
                [
                    Video::ATTRIBUTE_SOURCE => VideoSource::WEB->value,
                ],
                [
                    Video::ATTRIBUTE_SOURCE => VideoSource::BD->value,
                ],
            ],
            [
                [
                    Video::ATTRIBUTE_SOURCE => VideoSource::BD->value,
                    Video::ATTRIBUTE_OVERLAP => VideoOverlap::OVER->value,
                    Video::ATTRIBUTE_LYRICS => false,
                    Video::ATTRIBUTE_SUBBED => false,
                ],
                [
                    Video::ATTRIBUTE_SOURCE => VideoSource::BD->value,
                    Video::ATTRIBUTE_OVERLAP => VideoOverlap::NONE->value,
                    Video::ATTRIBUTE_LYRICS => false,
                    Video::ATTRIBUTE_SUBBED => false,
                ],
            ],
            [
                [
                    Video::ATTRIBUTE_SOURCE => VideoSource::BD->value,
                    Video::ATTRIBUTE_OVERLAP => VideoOverlap::TRANS->value,
                    Video::ATTRIBUTE_LYRICS => false,
                    Video::ATTRIBUTE_SUBBED => false,
                ],
                [
                    Video::ATTRIBUTE_SOURCE => VideoSource::BD->value,
                    Video::ATTRIBUTE_OVERLAP => VideoOverlap::NONE->value,
                    Video::ATTRIBUTE_LYRICS => false,
                    Video::ATTRIBUTE_SUBBED => false,
                ],
            ],
            [
                [
                    Video::ATTRIBUTE_SOURCE => VideoSource::BD->value,
                    Video::ATTRIBUTE_OVERLAP => VideoOverlap::NONE->value,
                    Video::ATTRIBUTE_LYRICS => true,
                    Video::ATTRIBUTE_SUBBED => false,
                ],
                [
                    Video::ATTRIBUTE_SOURCE => VideoSource::BD->value,
                    Video::ATTRIBUTE_OVERLAP => VideoOverlap::NONE->value,
                    Video::ATTRIBUTE_LYRICS => false,
                    Video::ATTRIBUTE_SUBBED => false,
                ],
            ],
            [
                [
                    Video::ATTRIBUTE_SOURCE => VideoSource::BD->value,
                    Video::ATTRIBUTE_OVERLAP => VideoOverlap::NONE->value,
                    Video::ATTRIBUTE_LYRICS => false,
                    Video::ATTRIBUTE_SUBBED => true,
                ],
                [
                    Video::ATTRIBUTE_SOURCE => VideoSource::BD->value,
                    Video::ATTRIBUTE_OVERLAP => VideoOverlap::NONE,
                    Video::ATTRIBUTE_LYRICS => false,
                    Video::ATTRIBUTE_SUBBED => false,
                ],
            ],
        ];
    }

    /**
     * The video shall not be deleted from storage when the Video is deleted.
     */
    public function testVideoStorageDeletion(): void
    {
        $fs = Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
        $file = File::fake()->create($this->faker->word().'.webm', $this->faker->randomDigitNotNull());
        $fsFile = $fs->putFile('', $file);

        $video = Video::factory()->createOne([
            Video::ATTRIBUTE_PATH => $fsFile,
        ]);

        $video->delete();

        static::assertTrue($fs->exists($video->path));
    }

    /**
     * The video shall be deleted from storage when the Video is force deleted.
     */
    public function testVideoStorageForceDeletion(): void
    {
        Event::fakeExcept(VideoForceDeleting::class);

        $fs = Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
        $file = File::fake()->create($this->faker->word().'.webm', $this->faker->randomDigitNotNull());
        $fsFile = $fs->putFile('', $file);

        $video = Video::factory()->createOne([
            Video::ATTRIBUTE_PATH => $fsFile,
        ]);

        $video->forceDelete();

        static::assertFalse($fs->exists($video->path));
    }
}
