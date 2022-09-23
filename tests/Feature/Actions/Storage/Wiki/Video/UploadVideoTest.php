<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Storage\Wiki\Video;

use App\Actions\Storage\Wiki\Video\UploadVideoAction;
use App\Constants\Config\VideoConstants;
use App\Enums\Actions\ActionStatus;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Models\Wiki\Video\VideoScript;
use App\Pivots\AnimeThemeEntryVideo;
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
        Config::set(VideoConstants::DISKS_QUALIFIED, []);
        Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

        $file = File::fake()->create($this->faker->word().'.webm', $this->faker->randomDigitNotNull());

        $action = new UploadVideoAction($file, $this->faker->word());

        $storageResults = $action->handle();

        $result = $storageResults->toActionResult();

        static::assertTrue($result->hasFailed());
    }

    /**
     * The Upload Video Action shall pass if given a valid file.
     *
     * @return void
     */
    public function testPassed(): void
    {
        Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
        Config::set(VideoConstants::DISKS_QUALIFIED, [Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED)]);

        $file = File::fake()->create($this->faker->word().'.webm', $this->faker->randomDigitNotNull());

        $action = new UploadVideoAction($file, $this->faker->word());

        $storageResults = $action->handle();

        $result = $storageResults->toActionResult();

        static::assertTrue(ActionStatus::PASSED()->is($result->getStatus()));
    }

    /**
     * The Upload Video Action shall upload the file to the configured disk.
     *
     * @return void
     */
    public function testUploadedToDisk(): void
    {
        Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
        Config::set(VideoConstants::DISKS_QUALIFIED, [Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED)]);

        $file = File::fake()->create($this->faker->word().'.webm', $this->faker->randomDigitNotNull());

        $action = new UploadVideoAction($file, $this->faker->word());

        $action->handle();

        static::assertCount(1, Storage::disk(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED))->allFiles());
    }

    /**
     * The Upload Video Action shall upload the file to the configured disk.
     *
     * @return void
     */
    public function testCreatedVideo(): void
    {
        Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
        Config::set(VideoConstants::DISKS_QUALIFIED, [Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED)]);

        $file = File::fake()->create($this->faker->word().'.webm', $this->faker->randomDigitNotNull());

        $action = new UploadVideoAction($file, $this->faker->word());

        $result = $action->handle();

        $action->then($result);

        static::assertDatabaseCount(Video::class, 1);
    }

    /**
     * The Upload Video Action shall attach the provided entry.
     *
     * @return void
     */
    public function testAttachesEntry(): void
    {
        Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
        Config::set(VideoConstants::DISKS_QUALIFIED, [Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED)]);

        $file = File::fake()->create($this->faker->word().'.webm', $this->faker->randomDigitNotNull());

        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $action = new UploadVideoAction($file, $this->faker->word(), $entry);

        $result = $action->handle();

        $action->then($result);

        static::assertDatabaseCount(AnimeThemeEntryVideo::class, 1);
    }

    /**
     * The Upload Video Action shall attach the provided script.
     *
     * @return void
     */
    public function testAssociatesScript(): void
    {
        Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
        Config::set(VideoConstants::DISKS_QUALIFIED, [Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED)]);
        Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

        $file = File::fake()->create($this->faker->word().'.webm', $this->faker->randomDigitNotNull());
        $script = File::fake()->create($this->faker->word().'.txt', $this->faker->randomDigitNotNull());

        $action = new UploadVideoAction($file, $this->faker->word(), null, $script);

        $result = $action->handle();

        $action->then($result);

        static::assertDatabaseHas(VideoScript::class, [VideoScript::ATTRIBUTE_VIDEO => 1]);
    }
}
