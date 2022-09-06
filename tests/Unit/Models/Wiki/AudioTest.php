<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Wiki;

use App\Models\Wiki\Audio;
use App\Models\Wiki\Video;
use CyrildeWit\EloquentViewable\View;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class AudioTest.
 */
class AudioTest extends TestCase
{
    use WithFaker;

    /**
     * Audios shall be nameable.
     *
     * @return void
     */
    public function testNameable(): void
    {
        $audio = Audio::factory()->createOne();

        static::assertIsString($audio->getName());
    }

    /**
     * Audio shall have a one-to-many relationship with the type Video.
     *
     * @return void
     */
    public function testVideos(): void
    {
        $videoCount = $this->faker->randomDigitNotNull();

        $audio = Audio::factory()
            ->has(Video::factory()->count($videoCount))
            ->createOne();

        static::assertInstanceOf(HasMany::class, $audio->videos());
        static::assertEquals($videoCount, $audio->videos()->count());
        static::assertInstanceOf(Video::class, $audio->videos()->first());
    }

    /**
     * Audios shall have a one-to-many polymorphic relationship to View.
     *
     * @return void
     */
    public function testViews(): void
    {
        $audio = Audio::factory()->createOne();

        views($audio)->record();

        static::assertInstanceOf(MorphMany::class, $audio->views());
        static::assertEquals(1, $audio->views()->count());
        static::assertInstanceOf(View::class, $audio->views()->first());
    }
}
