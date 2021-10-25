<?php

declare(strict_types=1);

namespace Tests\Feature\Listeners\Wiki;

use App\Enums\Models\Wiki\VideoSource;
use App\Models\Wiki\Video;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class InitializeVideoTagsTest.
 */
class InitializeVideoTagsTest extends TestCase
{
    use WithFaker;

    /**
     * When a video is created, if the filename does not contain tags,
     * the tag attributes shall retain their default values.
     *
     * @return void
     */
    public function testNoTags()
    {
        $filename = Str::of(Str::random($this->faker->randomDigitNotNull()))
            ->append('-')
            ->append($this->faker->boolean() ? 'OP' : 'ED')
            ->__toString();

        $video = Video::factory()->createOne([
            Video::ATTRIBUTE_FILENAME => $filename,
            Video::ATTRIBUTE_LYRICS => false,
            Video::ATTRIBUTE_NC => false,
            Video::ATTRIBUTE_RESOLUTION => null,
            Video::ATTRIBUTE_SOURCE => null,
            Video::ATTRIBUTE_SUBBED => false,
        ]);

        static::assertFalse($video->nc);
        static::assertFalse($video->subbed);
        static::assertFalse($video->lyrics);
        static::assertNull($video->resolution);
        static::assertNull($video->source);
    }

    /**
     * When a video is created, set the NC tag attribute if contained in the filename.
     *
     * @return void
     */
    public function testNcTag()
    {
        $filename = Str::of(Str::random($this->faker->randomDigitNotNull()))
            ->append('-')
            ->append($this->faker->boolean() ? 'OP' : 'ED')
            ->append('-')
            ->append('NC')
            ->__toString();

        $video = Video::factory()->createOne([
            Video::ATTRIBUTE_FILENAME => $filename,
            Video::ATTRIBUTE_LYRICS => false,
            Video::ATTRIBUTE_NC => false,
            Video::ATTRIBUTE_RESOLUTION => null,
            Video::ATTRIBUTE_SOURCE => null,
            Video::ATTRIBUTE_SUBBED => false,
        ]);

        static::assertTrue($video->nc);
    }

    /**
     * When a video is created, set the Subbed tag attribute if contained in the filename.
     *
     * @return void
     */
    public function testSubbedTag()
    {
        $filename = Str::of(Str::random($this->faker->randomDigitNotNull()))
            ->append('-')
            ->append($this->faker->boolean() ? 'OP' : 'ED')
            ->append('-')
            ->append('Subbed')
            ->__toString();

        $video = Video::factory()->createOne([
            Video::ATTRIBUTE_FILENAME => $filename,
            Video::ATTRIBUTE_LYRICS => false,
            Video::ATTRIBUTE_NC => false,
            Video::ATTRIBUTE_RESOLUTION => null,
            Video::ATTRIBUTE_SOURCE => null,
            Video::ATTRIBUTE_SUBBED => false,
        ]);

        static::assertTrue($video->subbed);
    }

    /**
     * When a video is created, set the Lyrics tag attribute if contained in the filename.
     *
     * @return void
     */
    public function testLyricsTag()
    {
        $filename = Str::of(Str::random($this->faker->randomDigitNotNull()))
            ->append('-')
            ->append($this->faker->boolean() ? 'OP' : 'ED')
            ->append('-')
            ->append('Lyrics')
            ->__toString();

        $video = Video::factory()->createOne([
            Video::ATTRIBUTE_FILENAME => $filename,
            Video::ATTRIBUTE_LYRICS => false,
            Video::ATTRIBUTE_NC => false,
            Video::ATTRIBUTE_RESOLUTION => null,
            Video::ATTRIBUTE_SOURCE => null,
            Video::ATTRIBUTE_SUBBED => false,
        ]);

        static::assertTrue($video->lyrics);
    }

    /**
     * When a video is created, set the Resolution tag attribute if contained in the filename.
     *
     * @return void
     */
    public function testResolutionTag()
    {
        $resolution = $this->faker->randomNumber();

        $filename = Str::of(Str::random($this->faker->randomDigitNotNull()))
            ->append('-')
            ->append($this->faker->boolean() ? 'OP' : 'ED')
            ->append('-')
            ->append($resolution)
            ->__toString();

        $video = Video::factory()->createOne([
            Video::ATTRIBUTE_FILENAME => $filename,
            Video::ATTRIBUTE_LYRICS => false,
            Video::ATTRIBUTE_NC => false,
            Video::ATTRIBUTE_RESOLUTION => null,
            Video::ATTRIBUTE_SOURCE => null,
            Video::ATTRIBUTE_SUBBED => false,
        ]);

        static::assertEquals($resolution, $video->resolution);
    }

    /**
     * When a video is created, set the Resolution tag attribute if implicitly contained in the filename.
     *
     * @return void
     */
    public function testImplicitResolutionTag()
    {
        $filename = Str::of(Str::random($this->faker->randomDigitNotNull()))
            ->append('-')
            ->append($this->faker->boolean() ? 'OP' : 'ED')
            ->append('-')
            ->append($this->faker->boolean() ? 'NCBD' : 'NCBDLyrics')
            ->__toString();

        $video = Video::factory()->createOne([
            Video::ATTRIBUTE_FILENAME => $filename,
            Video::ATTRIBUTE_LYRICS => false,
            Video::ATTRIBUTE_NC => false,
            Video::ATTRIBUTE_RESOLUTION => null,
            Video::ATTRIBUTE_SOURCE => null,
            Video::ATTRIBUTE_SUBBED => false,
        ]);

        static::assertEquals(720, $video->resolution);
    }

    /**
     * When a video is created, set the Source tag attribute if contained in the filename.
     *
     * @return void
     */
    public function testSourceTag()
    {
        $source = VideoSource::getRandomInstance();

        $filename = Str::of(Str::random($this->faker->randomDigitNotNull()))
            ->append('-')
            ->append($this->faker->boolean() ? 'OP' : 'ED')
            ->append('-')
            ->append($source->key)
            ->__toString();

        $video = Video::factory()->createOne([
            Video::ATTRIBUTE_FILENAME => $filename,
            Video::ATTRIBUTE_LYRICS => false,
            Video::ATTRIBUTE_NC => false,
            Video::ATTRIBUTE_RESOLUTION => null,
            Video::ATTRIBUTE_SOURCE => null,
            Video::ATTRIBUTE_SUBBED => false,
        ]);

        static::assertEquals($source, $video->source);
    }
}
