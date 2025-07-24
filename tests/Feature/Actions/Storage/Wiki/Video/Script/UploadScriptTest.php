<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Storage\Wiki\Video\Script;

use App\Actions\Storage\Wiki\Video\Script\UploadScriptAction;
use App\Constants\Config\VideoConstants;
use App\Enums\Actions\ActionStatus;
use App\Models\Wiki\Video;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UploadScriptTest extends TestCase
{
    use WithFaker;

    /**
     * The Upload Script Action shall fail if there are no uploads.
     */
    public function testDefault(): void
    {
        Config::set(VideoConstants::SCRIPT_DISK_QUALIFIED, []);
        Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

        $file = File::fake()->create($this->faker->word().'.txt', $this->faker->randomDigitNotNull());

        $action = new UploadScriptAction($file, $this->faker->word());

        $storageResults = $action->handle();

        $result = $storageResults->toActionResult();

        static::assertTrue($result->hasFailed());
    }

    /**
     * The Upload Script Action shall pass if given a valid file.
     */
    public function testPassed(): void
    {
        Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

        $file = File::fake()->create($this->faker->word().'.txt', $this->faker->randomDigitNotNull());

        $action = new UploadScriptAction($file, $this->faker->word());

        $storageResults = $action->handle();

        $result = $storageResults->toActionResult();

        static::assertTrue($result->getStatus() === ActionStatus::PASSED);
    }

    /**
     * The Upload Script Action shall upload the file to the configured disk.
     */
    public function testUploadedToDisk(): void
    {
        $fs = Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

        $file = File::fake()->create($this->faker->word().'.txt', $this->faker->randomDigitNotNull());

        $action = new UploadScriptAction($file, $this->faker->word());

        $action->handle();

        static::assertCount(1, $fs->allFiles());
    }

    /**
     * The Upload Video Action shall upload the file to the configured disk.
     */
    public function testCreatedVideo(): void
    {
        Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

        $file = File::fake()->create($this->faker->word().'.txt', $this->faker->randomDigitNotNull());

        $action = new UploadScriptAction($file, $this->faker->word());

        $result = $action->handle();

        $action->then($result);

        static::assertDatabaseCount(VideoScript::class, 1);
    }

    /**
     * The Upload Script Action shall attach the provided video.
     */
    public function testAttachesVideo(): void
    {
        Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

        $file = File::fake()->create($this->faker->word().'.txt', $this->faker->randomDigitNotNull());

        $video = Video::factory()->createOne();

        $action = new UploadScriptAction($file, $this->faker->word(), $video);

        $result = $action->handle();

        $action->then($result);

        static::assertTrue($video->videoscript()->exists());
    }
}
