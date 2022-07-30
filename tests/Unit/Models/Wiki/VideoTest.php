<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Wiki;

use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Audio;
use App\Models\Wiki\Video;
use App\Pivots\AnimeThemeEntryVideo;
use CyrildeWit\EloquentViewable\View;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class VideoTest.
 */
class VideoTest extends TestCase
{
    use WithFaker;

    /**
     * The overlap attribute of a video shall be cast to a VideoOverlap enum instance.
     *
     * @return void
     */
    public function testCastsOverlapToEnum(): void
    {
        $video = Video::factory()->createOne();

        $overlap = $video->overlap;

        static::assertInstanceOf(VideoOverlap::class, $overlap);
    }

    /**
     * The source attribute of a video shall be cast to a VideoSource enum instance.
     *
     * @return void
     */
    public function testCastsSourceToEnum(): void
    {
        $video = Video::factory()->createOne();

        $source = $video->source;

        static::assertInstanceOf(VideoSource::class, $source);
    }

    /**
     * Video shall be a searchable resource.
     *
     * @return void
     */
    public function testSearchableAs(): void
    {
        $video = Video::factory()->createOne();

        static::assertIsString($video->searchableAs());
    }

    /**
     * Video shall be a searchable resource.
     *
     * @return void
     */
    public function testToSearchableArray(): void
    {
        $video = Video::factory()->createOne();

        static::assertIsArray($video->toSearchableArray());
    }

    /**
     * Videos shall be auditable.
     *
     * @return void
     */
    public function testAuditable(): void
    {
        Config::set('audit.console', true);

        $video = Video::factory()->createOne();

        static::assertEquals(1, $video->audits()->count());
    }

    /**
     * Videos shall be nameable.
     *
     * @return void
     */
    public function testNameable(): void
    {
        $video = Video::factory()->createOne();

        static::assertIsString($video->getName());
    }

    /**
     * Videos shall have a one-to-many polymorphic relationship to View.
     *
     * @return void
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
     *
     * @return void
     */
    public function testAppendsTags(): void
    {
        $video = Video::factory()->createOne();

        static::assertArrayHasKey(Video::ATTRIBUTE_TAGS, $video);
    }

    /**
     * The Tags attribute shall contain 'NC' if the NC attribute is true.
     *
     * @return void
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
     *
     * @return void
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
     *
     * @return void
     */
    public function testDvdTag(): void
    {
        $source = VideoSource::DVD;

        $video = Video::factory()->createOne([
            Video::ATTRIBUTE_SOURCE => $source,
        ]);

        static::assertContains(VideoSource::getDescription($source), $video->tags);
    }

    /**
     * The Tags attribute shall contain 'BD' if the source is BD.
     *
     * @return void
     */
    public function testBdTag(): void
    {
        $source = VideoSource::BD;

        $video = Video::factory()->createOne([
            Video::ATTRIBUTE_SOURCE => $source,
        ]);

        static::assertContains(VideoSource::getDescription($source), $video->tags);
    }

    /**
     * The Tags attribute shall not contain the description of the source if not DVD or BD.
     *
     * @return void
     */
    public function testOtherSourceTag(): void
    {
        $source = null;
        while ($source === null) {
            $sourceCandidate = VideoSource::getRandomInstance();
            if (! $sourceCandidate->is(VideoSource::BD) && ! $sourceCandidate->is(VideoSource::DVD)) {
                $source = $sourceCandidate->value;
            }
        }

        $video = Video::factory()->createOne([
            Video::ATTRIBUTE_SOURCE => $source,
        ]);

        static::assertNotContains(VideoSource::getDescription($source), $video->tags);
    }

    /**
     * The Tags attribute shall contain resolution.
     *
     * @return void
     */
    public function testResolutionTag(): void
    {
        $video = Video::factory()->createOne();

        static::assertContains(strval($video->resolution), $video->tags);
    }

    /**
     * The Tags attribute shall exclude 720 resolution from tags.
     *
     * @return void
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
     *
     * @return void
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
     *
     * @return void
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
     * @return void
     *
     * @dataProvider priorityProvider
     */
    public function testSourcePriority(array $a, array $b): void
    {
        $first = Video::factory()->createOne($a);

        $second = Video::factory()->createOne($b);

        static::assertGreaterThan($first->getSourcePriority(), $second->getSourcePriority());
    }

    /**
     * Videos shall have a many-to-many relationship with the type Entry.
     *
     * @return void
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
     *
     * @return void
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
     * Provider for source priority testing
     *
     * @return array
     */
    public function priorityProvider(): array
    {
        return [
            [
                [
                    Video::ATTRIBUTE_SOURCE => VideoSource::WEB,
                ],
                [
                    Video::ATTRIBUTE_SOURCE => VideoSource::BD,
                ],
            ],
            [
                [
                    Video::ATTRIBUTE_SOURCE => VideoSource::BD,
                    Video::ATTRIBUTE_OVERLAP => VideoOverlap::OVER,
                    Video::ATTRIBUTE_LYRICS => false,
                    Video::ATTRIBUTE_SUBBED => false,
                ],
                [
                    Video::ATTRIBUTE_SOURCE => VideoSource::BD,
                    Video::ATTRIBUTE_OVERLAP => VideoOverlap::NONE,
                    Video::ATTRIBUTE_LYRICS => false,
                    Video::ATTRIBUTE_SUBBED => false,
                ],
            ],
            [
                [
                    Video::ATTRIBUTE_SOURCE => VideoSource::BD,
                    Video::ATTRIBUTE_OVERLAP => VideoOverlap::TRANS,
                    Video::ATTRIBUTE_LYRICS => false,
                    Video::ATTRIBUTE_SUBBED => false,
                ],
                [
                    Video::ATTRIBUTE_SOURCE => VideoSource::BD,
                    Video::ATTRIBUTE_OVERLAP => VideoOverlap::NONE,
                    Video::ATTRIBUTE_LYRICS => false,
                    Video::ATTRIBUTE_SUBBED => false,
                ],
            ],
            [
                [
                    Video::ATTRIBUTE_SOURCE => VideoSource::BD,
                    Video::ATTRIBUTE_OVERLAP => VideoOverlap::NONE,
                    Video::ATTRIBUTE_LYRICS => true,
                    Video::ATTRIBUTE_SUBBED => false,
                ],
                [
                    Video::ATTRIBUTE_SOURCE => VideoSource::BD,
                    Video::ATTRIBUTE_OVERLAP => VideoOverlap::NONE,
                    Video::ATTRIBUTE_LYRICS => false,
                    Video::ATTRIBUTE_SUBBED => false,
                ],
            ],
            [
                [
                    Video::ATTRIBUTE_SOURCE => VideoSource::BD,
                    Video::ATTRIBUTE_OVERLAP => VideoOverlap::NONE,
                    Video::ATTRIBUTE_LYRICS => false,
                    Video::ATTRIBUTE_SUBBED => true,
                ],
                [
                    Video::ATTRIBUTE_SOURCE => VideoSource::BD,
                    Video::ATTRIBUTE_OVERLAP => VideoOverlap::NONE,
                    Video::ATTRIBUTE_LYRICS => false,
                    Video::ATTRIBUTE_SUBBED => false,
                ],
            ],
        ];
    }
}
