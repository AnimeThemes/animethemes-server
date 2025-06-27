<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Wiki;

use App\Constants\Config\AudioConstants;
use App\Events\Wiki\Audio\AudioForceDeleting;
use App\Models\Wiki\Audio;
use App\Models\Wiki\Video;
use CyrildeWit\EloquentViewable\View;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
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
    public function test_nameable(): void
    {
        $audio = Audio::factory()->createOne();

        static::assertIsString($audio->getName());
    }

    /**
     * Audios shall have subtitle.
     *
     * @return void
     */
    public function test_has_subtitle(): void
    {
        $audio = Audio::factory()->createOne();

        static::assertIsString($audio->getSubtitle());
    }

    /**
     * Audio shall have a one-to-many relationship with the type Video.
     *
     * @return void
     */
    public function test_videos(): void
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
    public function test_views(): void
    {
        $audio = Audio::factory()->createOne();

        views($audio)->record();

        static::assertInstanceOf(MorphMany::class, $audio->views());
        static::assertEquals(1, $audio->views()->count());
        static::assertInstanceOf(View::class, $audio->views()->first());
    }

    /**
     * The audio shall not be deleted from storage when the Audio is deleted.
     *
     * @return void
     */
    public function test_audio_storage_deletion(): void
    {
        $fs = Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));
        $file = File::fake()->create($this->faker->word().'.ogg', $this->faker->randomDigitNotNull());
        $fsFile = $fs->putFile('', $file);

        $audio = Audio::factory()->createOne([
            Audio::ATTRIBUTE_PATH => $fsFile,
        ]);

        $audio->delete();

        static::assertTrue($fs->exists($audio->path));
    }

    /**
     * The audio shall be deleted from storage when the Audio is force deleted.
     *
     * @return void
     */
    public function test_audio_storage_force_deletion(): void
    {
        Event::fakeExcept(AudioForceDeleting::class);

        $fs = Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));
        $file = File::fake()->create($this->faker->word().'.ogg', $this->faker->randomDigitNotNull());
        $fsFile = $fs->putFile('', $file);

        $audio = Audio::factory()->createOne([
            Audio::ATTRIBUTE_PATH => $fsFile,
        ]);

        $audio->forceDelete();

        static::assertFalse($fs->exists($audio->path));
    }
}
