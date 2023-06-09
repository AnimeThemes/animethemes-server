<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Storage\Wiki\Audio;

use App\Actions\Storage\Wiki\Audio\MoveAudioAction;
use App\Constants\Config\AudioConstants;
use App\Enums\Actions\ActionStatus;
use App\Models\Wiki\Audio;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Http\Testing\MimeType;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class MoveAudioTest.
 */
class MoveAudioTest extends TestCase
{
    use WithFaker;

    /**
     * The Move Audio Action shall fail if there are no moves.
     *
     * @return void
     */
    public function testDefault(): void
    {
        Config::set(AudioConstants::DISKS_QUALIFIED, []);
        Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

        $audio = Audio::factory()->createOne();

        $action = new MoveAudioAction($audio, $this->faker->word());

        $storageResults = $action->handle();

        $result = $storageResults->toActionResult();

        static::assertTrue($result->hasFailed());
    }

    /**
     * The Move Audio Action shall pass if there are moves.
     *
     * @return void
     */
    public function testPassed(): void
    {
        /** @var FilesystemAdapter $fs */
        $fs = Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));
        Config::set(AudioConstants::DISKS_QUALIFIED, [Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED)]);

        $file = File::fake()->create($this->faker->word().'.ogg', $this->faker->randomDigitNotNull());

        $directory = $this->faker->unique()->word();

        $audio = Audio::factory()->createOne([
            Audio::ATTRIBUTE_BASENAME => FileFacade::basename($file->path()),
            Audio::ATTRIBUTE_FILENAME => FileFacade::name($file->path()),
            Audio::ATTRIBUTE_MIMETYPE => MimeType::from($file->path()),
            Audio::ATTRIBUTE_PATH => $fs->putFileAs($directory, $file, $file->getClientOriginalName()),
        ]);

        $action = new MoveAudioAction($audio, Str::replace($directory, $this->faker->unique()->word(), $audio->path));

        $storageResults = $action->handle();

        $result = $storageResults->toActionResult();

        static::assertTrue(ActionStatus::PASSED === $result->getStatus());
    }

    /**
     * The Move Audio Action shall move the file in the configured disks.
     *
     * @return void
     */
    public function testMovedInDisk(): void
    {
        /** @var FilesystemAdapter $fs */
        $fs = Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));
        Config::set(AudioConstants::DISKS_QUALIFIED, [Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED)]);

        $file = File::fake()->create($this->faker->word().'.ogg', $this->faker->randomDigitNotNull());

        $directory = $this->faker->unique()->word();

        $audio = Audio::factory()->createOne([
            Audio::ATTRIBUTE_BASENAME => FileFacade::basename($file->path()),
            Audio::ATTRIBUTE_FILENAME => FileFacade::name($file->path()),
            Audio::ATTRIBUTE_MIMETYPE => MimeType::from($file->path()),
            Audio::ATTRIBUTE_PATH => $fs->putFileAs($directory, $file, $file->getClientOriginalName()),
        ]);

        $from = $audio->path();
        $to = Str::replace($directory, $this->faker->unique()->word(), $audio->path);

        $action = new MoveAudioAction($audio, $to);

        $action->handle();

        $fs->assertMissing($from);
        $fs->assertExists($to);
    }

    /**
     * The Move Audio Action shall move the audio.
     *
     * @return void
     */
    public function testAudioUpdated(): void
    {
        /** @var FilesystemAdapter $fs */
        $fs = Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));
        Config::set(AudioConstants::DISKS_QUALIFIED, [Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED)]);

        $file = File::fake()->create($this->faker->word().'.ogg', $this->faker->randomDigitNotNull());

        $directory = $this->faker->unique()->word();

        $audio = Audio::factory()->createOne([
            Audio::ATTRIBUTE_BASENAME => FileFacade::basename($file->path()),
            Audio::ATTRIBUTE_FILENAME => FileFacade::name($file->path()),
            Audio::ATTRIBUTE_MIMETYPE => MimeType::from($file->path()),
            Audio::ATTRIBUTE_PATH => $fs->putFileAs($directory, $file, $file->getClientOriginalName()),
        ]);

        $to = Str::replace($directory, $this->faker->unique()->word(), $audio->path);

        $action = new MoveAudioAction($audio, $to);

        $result = $action->handle();

        $action->then($result);

        static::assertDatabaseHas(Audio::class, [Audio::ATTRIBUTE_PATH => $to]);
    }
}
