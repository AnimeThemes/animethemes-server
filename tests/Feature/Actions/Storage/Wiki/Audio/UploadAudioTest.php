<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Storage\Wiki\Audio;

use App\Actions\Storage\Wiki\Audio\UploadAudioAction;
use App\Constants\Config\AudioConstants;
use App\Enums\Actions\ActionStatus;
use App\Models\Wiki\Audio;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Class UploadAudioTest.
 */
class UploadAudioTest extends TestCase
{
    use WithFaker;

    /**
     * The Upload Audio Action shall fail if there are no uploads.
     *
     * @return void
     */
    public function testDefault(): void
    {
        Config::set(AudioConstants::DISKS_QUALIFIED, []);
        Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

        $file = File::fake()->create($this->faker->word().'.ogg', $this->faker->randomDigitNotNull());

        $action = new UploadAudioAction($file, $this->faker->word());

        $storageResults = $action->handle();

        $result = $storageResults->toActionResult();

        static::assertTrue($result->hasFailed());
    }

    /**
     * The Upload Audio Action shall pass if given a valid file.
     *
     * @return void
     */
    public function testPassed(): void
    {
        Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));
        Config::set(AudioConstants::DISKS_QUALIFIED, [Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED)]);

        $file = File::fake()->create($this->faker->word().'.ogg', $this->faker->randomDigitNotNull());

        $action = new UploadAudioAction($file, $this->faker->word());

        $storageResults = $action->handle();

        $result = $storageResults->toActionResult();

        static::assertTrue(ActionStatus::PASSED()->is($result->getStatus()));
    }

    /**
     * The Upload Audio Action shall upload the file to the configured disk.
     *
     * @return void
     */
    public function testUploadedToDisk(): void
    {
        Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));
        Config::set(AudioConstants::DISKS_QUALIFIED, [Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED)]);

        $file = File::fake()->create($this->faker->word().'.ogg', $this->faker->randomDigitNotNull());

        $action = new UploadAudioAction($file, $this->faker->word());

        $action->handle();

        static::assertCount(1, Storage::disk(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED))->allFiles());
    }

    /**
     * The Upload Audio Action shall upload the file to the configured disk.
     *
     * @return void
     */
    public function testCreatedAudio(): void
    {
        Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));
        Config::set(AudioConstants::DISKS_QUALIFIED, [Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED)]);

        $file = File::fake()->create($this->faker->word().'.ogg', $this->faker->randomDigitNotNull());

        $action = new UploadAudioAction($file, $this->faker->word());

        $result = $action->handle();

        $action->then($result);

        static::assertDatabaseCount(Audio::class, 1);
    }
}
