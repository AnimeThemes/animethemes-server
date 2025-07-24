<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Storage\Wiki\Audio;

use App\Actions\Storage\Wiki\Audio\DeleteAudioAction;
use App\Constants\Config\AudioConstants;
use App\Enums\Actions\ActionStatus;
use App\Models\Wiki\Audio;
use Exception;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Http\Testing\MimeType;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DeleteAudioTest extends TestCase
{
    use WithFaker;

    /**
     * The Delete Audio Action shall fail if there are no deletions.
     */
    public function testDefault(): void
    {
        Config::set(AudioConstants::DISKS_QUALIFIED, []);
        Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

        $audio = Audio::factory()->createOne();

        $action = new DeleteAudioAction($audio);

        $storageResults = $action->handle();

        $result = $storageResults->toActionResult();

        static::assertTrue($result->hasFailed());
    }

    /**
     * The Delete Audio Action shall pass if there are deletions.
     */
    public function testPassed(): void
    {
        Config::set(AudioConstants::DISKS_QUALIFIED, [Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED)]);
        Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

        $file = File::fake()->create($this->faker->word().'.ogg', $this->faker->randomDigitNotNull());

        $audio = Audio::factory()->createOne([
            Audio::ATTRIBUTE_BASENAME => FileFacade::basename($file->path()),
            Audio::ATTRIBUTE_FILENAME => FileFacade::name($file->path()),
            Audio::ATTRIBUTE_MIMETYPE => MimeType::from($file->path()),
            Audio::ATTRIBUTE_PATH => $file->path(),
        ]);

        $action = new DeleteAudioAction($audio);

        $storageResults = $action->handle();

        $result = $storageResults->toActionResult();

        static::assertTrue($result->getStatus() === ActionStatus::PASSED);
    }

    /**
     * The Delete Audio Action shall delete the file from the configured disks.
     */
    public function testDeletedFromDisk(): void
    {
        Config::set(AudioConstants::DISKS_QUALIFIED, [Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED)]);
        Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

        $file = File::fake()->create($this->faker->word().'.ogg', $this->faker->randomDigitNotNull());

        $audio = Audio::factory()->createOne([
            Audio::ATTRIBUTE_BASENAME => FileFacade::basename($file->path()),
            Audio::ATTRIBUTE_FILENAME => FileFacade::name($file->path()),
            Audio::ATTRIBUTE_MIMETYPE => MimeType::from($file->path()),
            Audio::ATTRIBUTE_PATH => $file->path(),
        ]);

        $action = new DeleteAudioAction($audio);

        $action->handle();

        static::assertEmpty(Storage::disk(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED))->allFiles());
    }

    /**
     * The Delete Audio Action shall delete the audio.
     *
     * @throws Exception
     */
    public function testAudioDeleted(): void
    {
        Config::set(AudioConstants::DISKS_QUALIFIED, [Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED)]);
        Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

        $file = File::fake()->create($this->faker->word().'.ogg', $this->faker->randomDigitNotNull());

        $audio = Audio::factory()->createOne([
            Audio::ATTRIBUTE_BASENAME => FileFacade::basename($file->path()),
            Audio::ATTRIBUTE_FILENAME => FileFacade::name($file->path()),
            Audio::ATTRIBUTE_MIMETYPE => MimeType::from($file->path()),
            Audio::ATTRIBUTE_PATH => $file->path(),
        ]);

        $action = new DeleteAudioAction($audio);

        $result = $action->handle();

        $action->then($result);

        static::assertSoftDeleted($audio);
    }
}
