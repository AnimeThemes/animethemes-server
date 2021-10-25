<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Wiki;

use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Pivots\AnimeThemeEntryVideo;
use CyrildeWit\EloquentViewable\View;
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
    public function testCastsOverlapToEnum()
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
    public function testCastsSourceToEnum()
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
    public function testSearchableAs()
    {
        $video = Video::factory()->createOne();

        static::assertIsString($video->searchableAs());
    }

    /**
     * Video shall be a searchable resource.
     *
     * @return void
     */
    public function testToSearchableArray()
    {
        $video = Video::factory()->createOne();

        static::assertIsArray($video->toSearchableArray());
    }

    /**
     * Videos shall be auditable.
     *
     * @return void
     */
    public function testAuditable()
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
    public function testNameable()
    {
        $video = Video::factory()->createOne();

        static::assertIsString($video->getName());
    }

    /**
     * Videos shall have a one-to-many polymorphic relationship to View.
     *
     * @return void
     */
    public function testViews()
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
    public function testAppendsTags()
    {
        $video = Video::factory()->createOne();

        static::assertArrayHasKey(Video::ATTRIBUTE_TAGS, $video);
    }

    /**
     * The Tags attribute shall contain 'NC' if the NC attribute is true.
     *
     * @return void
     */
    public function testNcTag()
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
    public function testNoNcTag()
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
    public function testDvdTag()
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
    public function testBdTag()
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
    public function testOtherSourceTag()
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
    public function testResolutionTag()
    {
        $video = Video::factory()->createOne();

        static::assertContains(strval($video->resolution), $video->tags);
    }

    /**
     * The Tags attribute shall exclude 720 resolution from tags.
     *
     * @return void
     */
    public function testNo720ResolutionTag()
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
    public function testSubbedTag()
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
    public function testLyricsTag()
    {
        $video = Video::factory()->createOne([
            Video::ATTRIBUTE_SUBBED => false,
            Video::ATTRIBUTE_LYRICS => true,
        ]);

        static::assertNotContains('Subbed', $video->tags);
        static::assertContains('Lyrics', $video->tags);
    }

    /**
     * Videos shall have a many-to-many relationship with the type Entry.
     *
     * @return void
     */
    public function testEntries()
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
}
