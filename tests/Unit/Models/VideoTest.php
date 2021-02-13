<?php

namespace Tests\Unit\Models;

use App\Enums\VideoOverlap;
use App\Enums\VideoSource;
use App\Models\Anime;
use App\Models\Entry;
use App\Models\Theme;
use App\Models\Video;
use App\Pivots\VideoEntry;
use CyrildeWit\EloquentViewable\View;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class VideoTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * The overlap attribute of a video shall be cast to a VideoOverlap enum instance.
     *
     * @return void
     */
    public function testCastsOverlapToEnum()
    {
        $video = Video::factory()->create();

        $overlap = $video->overlap;

        $this->assertInstanceOf(VideoOverlap::class, $overlap);
    }

    /**
     * The source attribute of a video shall be cast to a VideoSource enum instance.
     *
     * @return void
     */
    public function testCastsSourceToEnum()
    {
        $video = Video::factory()->create();

        $source = $video->source;

        $this->assertInstanceOf(VideoSource::class, $source);
    }

    /**
     * Video shall be a searchable resource.
     *
     * @return void
     */
    public function testSearchableAs()
    {
        $video = Video::factory()->create();

        $this->assertIsString($video->searchableAs());
    }

    /**
     * Video shall be a searchable resource.
     *
     * @return void
     */
    public function testToSearchableArray()
    {
        $video = Video::factory()->create();

        $this->assertIsArray($video->toSearchableArray());
    }

    /**
     * Videos shall be auditable.
     *
     * @return void
     */
    public function testAuditable()
    {
        Config::set('audit.console', true);

        $video = Video::factory()->create();

        $this->assertEquals(1, $video->audits->count());
    }

    /**
     * Videos shall be nameable.
     *
     * @return void
     */
    public function testNameable()
    {
        $video = Video::factory()->create();

        $this->assertIsString($video->getName());
    }

    /**
     * Videos shall have a one-to-many polymorphic relationship to View.
     *
     * @return void
     */
    public function testViews()
    {
        $video = Video::factory()->create();

        views($video)->record();

        $this->assertInstanceOf(MorphMany::class, $video->views());
        $this->assertEquals(1, $video->views()->count());
        $this->assertInstanceOf(View::class, $video->views()->first());
    }

    /**
     * Videos shall append a 'tags' attribute.
     *
     * @return void
     */
    public function testAppendsTags()
    {
        $video = Video::factory()->create();

        $this->assertArrayHasKey('tags', $video);
    }

    /**
     * The Tags attribute shall contain 'NC' if the NC attribute is true.
     *
     * @return void
     */
    public function testNcTag()
    {
        $video = Video::factory()->create([
            'nc' => true,
        ]);

        $this->assertContains('NC', $video->tags);
    }

    /**
     * The Tags attribute shall not contain 'NC' if the NC attribute is false.
     *
     * @return void
     */
    public function testNoNcTag()
    {
        $video = Video::factory()->create([
            'nc' => false,
        ]);

        $this->assertNotContains('NC', $video->tags);
    }

    /**
     * The Tags attribute shall contain 'DVD' if the source is DVD.
     *
     * @return void
     */
    public function testDvdTag()
    {
        $source = VideoSource::DVD;

        $video = Video::factory()->create([
            'source' => $source,
        ]);

        $this->assertContains(VideoSource::getDescription($source), $video->tags);
    }

    /**
     * The Tags attribute shall contain 'BD' if the source is BD.
     *
     * @return void
     */
    public function testBdTag()
    {
        $source = VideoSource::BD;

        $video = Video::factory()->create([
            'source' => $source,
        ]);

        $this->assertContains(VideoSource::getDescription($source), $video->tags);
    }

    /**
     * The Tags attribute shall not contain the description of the source if not DVD or BD.
     *
     * @return void
     */
    public function testOtherSourceTag()
    {
        $source = null;
        while ($source == null) {
            $source_candidate = VideoSource::getRandomInstance();
            if (! $source_candidate->is(VideoSource::BD) && ! $source_candidate->is(VideoSource::DVD)) {
                $source = $source_candidate->value;
            }
        }

        $video = Video::factory()->create([
            'source' => $source,
        ]);

        $this->assertNotContains(VideoSource::getDescription($source), $video->tags);
    }

    /**
     * The Tags attribute shall contain resolution.
     *
     * @return void
     */
    public function testResolutionTag()
    {
        $video = Video::factory()->create();

        $this->assertContains(strval($video->resolution), $video->tags);
    }

    /**
     * The Tags attribute shall contain 'Subbed' if the subbed attribute is true.
     *
     * @return void
     */
    public function testSubbedTag()
    {
        $video = Video::factory()->create([
            'subbed' => true,
        ]);

        $this->assertContains('Subbed', $video->tags);
        $this->assertNotContains('Lyrics', $video->tags);
    }

    /**
     * The Tags attribute shall contain 'Lyrics' if the lyrics attribute is true and subbed attribute is false.
     *
     * @return void
     */
    public function testLyricsTag()
    {
        $video = Video::factory()->create([
            'subbed' => false,
            'lyrics' => true,
        ]);

        $this->assertNotContains('Subbed', $video->tags);
        $this->assertContains('Lyrics', $video->tags);
    }

    /**
     * Videos shall have a many-to-many relationship with the type Entry.
     *
     * @return void
     */
    function testEntries()
    {
        $entry_count = $this->faker->randomDigitNotNull;

        $video = Video::factory()
            ->has(Entry::factory()->for(Theme::factory()->for(Anime::factory()))->count($entry_count))
            ->create();

        $this->assertInstanceOf(BelongsToMany::class, $video->entries());
        $this->assertEquals($entry_count, $video->entries()->count());
        $this->assertInstanceOf(Entry::class, $video->entries()->first());
        $this->assertEquals(VideoEntry::class, $video->entries()->getPivotClass());
    }
}
