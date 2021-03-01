<?php

namespace Tests\Feature\Listeners;

use App\Enums\VideoSource;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class InitializeVideoTagsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * When a video is created, if the filename does not contain tags, the tag attributes shall retain their default values.
     *
     * @return void
     */
    public function testNoTags()
    {
        $filename = Str::of(Str::random($this->faker->randomDigitNotNull))
            ->append('-')
            ->append($this->faker->boolean() ? 'OP' : 'ED')
            ->__toString();

        $video = Video::factory()->create([
            'filename' => $filename,
            'nc' => false,
            'subbed' => false,
            'lyrics' => false,
            'resolution' => null,
            'source' => null,
        ]);

        $this->assertFalse($video->nc);
        $this->assertFalse($video->subbed);
        $this->assertFalse($video->lyrics);
        $this->assertNull($video->resolution);
        $this->assertNull($video->source);
    }

    /**
     * When a video is created, set the NC tag attribute if contained in the filename.
     *
     * @return void
     */
    public function testNcTag()
    {
        $filename = Str::of(Str::random($this->faker->randomDigitNotNull))
            ->append('-')
            ->append($this->faker->boolean() ? 'OP' : 'ED')
            ->append('-')
            ->append('NC')
            ->__toString();

        $video = Video::factory()->create([
            'filename' => $filename,
            'nc' => false,
            'subbed' => false,
            'lyrics' => false,
            'resolution' => null,
            'source' => null,
        ]);

        $this->assertTrue($video->nc);
    }

    /**
     * When a video is created, set the Subbed tag attribute if contained in the filename.
     *
     * @return void
     */
    public function testSubbedTag()
    {
        $filename = Str::of(Str::random($this->faker->randomDigitNotNull))
            ->append('-')
            ->append($this->faker->boolean() ? 'OP' : 'ED')
            ->append('-')
            ->append('Subbed')
            ->__toString();

        $video = Video::factory()->create([
            'filename' => $filename,
            'nc' => false,
            'subbed' => false,
            'lyrics' => false,
            'resolution' => null,
            'source' => null,
        ]);

        $this->assertTrue($video->subbed);
    }

    /**
     * When a video is created, set the Lyrics tag attribute if contained in the filename.
     *
     * @return void
     */
    public function testLyricsTag()
    {
        $filename = Str::of(Str::random($this->faker->randomDigitNotNull))
            ->append('-')
            ->append($this->faker->boolean() ? 'OP' : 'ED')
            ->append('-')
            ->append('Lyrics')
            ->__toString();

        $video = Video::factory()->create([
            'filename' => $filename,
            'nc' => false,
            'subbed' => false,
            'lyrics' => false,
            'resolution' => null,
            'source' => null,
        ]);

        $this->assertTrue($video->lyrics);
    }

    /**
     * When a video is created, set the Resolution tag attribute if contained in the filename.
     *
     * @return void
     */
    public function testResolutionTag()
    {
        $resolution = $this->faker->randomNumber();

        $filename = Str::of(Str::random($this->faker->randomDigitNotNull))
            ->append('-')
            ->append($this->faker->boolean() ? 'OP' : 'ED')
            ->append('-')
            ->append($resolution)
            ->__toString();

        $video = Video::factory()->create([
            'filename' => $filename,
            'nc' => false,
            'subbed' => false,
            'lyrics' => false,
            'resolution' => null,
            'source' => null,
        ]);

        $this->assertEquals($resolution, $video->resolution);
    }

    /**
     * When a video is created, set the Resolution tag attribute if implicitly contained in the filename.
     *
     * @return void
     */
    public function testImplicitResolutionTag()
    {
        $filename = Str::of(Str::random($this->faker->randomDigitNotNull))
            ->append('-')
            ->append($this->faker->boolean() ? 'OP' : 'ED')
            ->append('-')
            ->append($this->faker->boolean() ? 'NCBD' : 'NCBDLyrics')
            ->__toString();

        $video = Video::factory()->create([
            'filename' => $filename,
            'nc' => false,
            'subbed' => false,
            'lyrics' => false,
            'resolution' => null,
            'source' => null,
        ]);

        $this->assertEquals(720, $video->resolution);
    }

    /**
     * When a video is created, set the Source tag attribute if contained in the filename.
     *
     * @return void
     */
    public function testSourceTag()
    {
        $source = VideoSource::getRandomInstance();

        $filename = Str::of(Str::random($this->faker->randomDigitNotNull))
            ->append('-')
            ->append($this->faker->boolean() ? 'OP' : 'ED')
            ->append('-')
            ->append($source->key)
            ->__toString();

        $video = Video::factory()->create([
            'filename' => $filename,
            'nc' => false,
            'subbed' => false,
            'lyrics' => false,
            'resolution' => null,
            'source' => null,
        ]);

        $this->assertEquals($source, $video->source);
    }
}
