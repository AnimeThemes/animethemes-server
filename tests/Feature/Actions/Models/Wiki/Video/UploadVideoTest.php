<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Models\Wiki\Video;

use App\Actions\Models\Wiki\Video\UploadVideoAction;
use App\Enums\Actions\ActionStatus;
use App\Models\Wiki\Video;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Class UploadVideoTest.
 */
class UploadVideoTest extends TestCase
{
    use WithFaker;

    /**
     * The Upload Video Action shall fail if there are no uploads.
     *
     * @return void
     */
    public function testDefault(): void
    {
        Config::set('video.upload_disks', []);
        Storage::fake(Config::get('video.disk'));

        $file = File::fake()->create($this->faker->word().'.webm', $this->faker->randomDigitNotNull());

        $action = new UploadVideoAction($file, $this->faker->word());

        $result = $action->handle();

        static::assertTrue($result->hasFailed());
    }

    /**
     * The Upload Video Action shall pass if given a valid file.
     *
     * @return void
     */
    public function testPassed(): void
    {
        Storage::fake(Config::get('video.disk'));
        Config::set('video.upload_disks', [Config::get('video.disk')]);

        $file = File::fake()->create($this->faker->word().'.webm', $this->faker->randomDigitNotNull());

        $action = new UploadVideoAction($file, $this->faker->word());

        $result = $action->handle();

        static::assertTrue(ActionStatus::PASSED()->is($result->getStatus()));
    }

    /**
     * The Upload Video Action shall upload the file to the configured disk.
     *
     * @return void
     */
    public function testUploadedToDisk(): void
    {
        Storage::fake(Config::get('video.disk'));
        Config::set('video.upload_disks', [Config::get('video.disk')]);

        $file = File::fake()->create($this->faker->word().'.webm', $this->faker->randomDigitNotNull());

        $action = new UploadVideoAction($file, $this->faker->word());

        $action->handle();

        static::assertCount(1, Storage::disk(Config::get('video.disk'))->allFiles());
    }

    /**
     * The Upload Video Action shall upload the file to the configured disk.
     *
     * @return void
     */
    public function testCreatedVideo(): void
    {
        Storage::fake(Config::get('video.disk'));
        Config::set('video.upload_disks', [Config::get('video.disk')]);

        $file = File::fake()->create($this->faker->word().'.webm', $this->faker->randomDigitNotNull());

        $action = new UploadVideoAction($file, $this->faker->word());

        $action->handle();

        static::assertDatabaseCount(Video::class, 1);
    }
}
