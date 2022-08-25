<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Storage\Wiki\Video;

use App\Actions\Storage\Wiki\Video\MoveVideoAction;
use App\Constants\Config\VideoConstants;
use App\Enums\Actions\ActionStatus;
use App\Models\Wiki\Video;
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
 * Class MoveVideoTest.
 */
class MoveVideoTest extends TestCase
{
    use WithFaker;

    /**
     * The Move Video Action shall fail if there are no moves.
     *
     * @return void
     */
    public function testDefault(): void
    {
        Config::set(VideoConstants::DISKS_QUALIFIED, []);
        Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

        $video = Video::factory()->createOne();

        $action = new MoveVideoAction($video, $this->faker->word());

        $storageResults = $action->handle();

        $result = $storageResults->toActionResult();

        static::assertTrue($result->hasFailed());
    }

    /**
     * The Move Video Action shall pass if there are moves.
     *
     * @return void
     */
    public function testPassed(): void
    {
        /** @var FilesystemAdapter $fs */
        $fs = Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
        Config::set(VideoConstants::DISKS_QUALIFIED, [Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED)]);

        $file = File::fake()->create($this->faker->word().'.webm', $this->faker->randomDigitNotNull());

        $directory = $this->faker->word();

        $video = Video::factory()->createOne([
            Video::ATTRIBUTE_BASENAME => FileFacade::basename($file->path()),
            Video::ATTRIBUTE_FILENAME => FileFacade::name($file->path()),
            Video::ATTRIBUTE_MIMETYPE => MimeType::from($file->path()),
            Video::ATTRIBUTE_PATH => $fs->putFileAs($directory, $file, $file->getClientOriginalName()),
        ]);

        $action = new MoveVideoAction($video, Str::replace($directory, $this->faker->word(), $video->path));

        $storageResults = $action->handle();

        $result = $storageResults->toActionResult();

        static::assertTrue(ActionStatus::PASSED()->is($result->getStatus()));
    }

    /**
     * The Move Video Action shall move the file in the configured disks.
     *
     * @return void
     */
    public function testMovedInDisk(): void
    {
        /** @var FilesystemAdapter $fs */
        $fs = Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
        Config::set(VideoConstants::DISKS_QUALIFIED, [Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED)]);

        $file = File::fake()->create($this->faker->word().'.webm', $this->faker->randomDigitNotNull());

        $directory = $this->faker->word();

        $video = Video::factory()->createOne([
            Video::ATTRIBUTE_BASENAME => FileFacade::basename($file->path()),
            Video::ATTRIBUTE_FILENAME => FileFacade::name($file->path()),
            Video::ATTRIBUTE_MIMETYPE => MimeType::from($file->path()),
            Video::ATTRIBUTE_PATH => $fs->putFileAs($directory, $file, $file->getClientOriginalName()),
        ]);

        $from = $video->path();
        $to = Str::replace($directory, $this->faker->word(), $video->path);

        $action = new MoveVideoAction($video, $to);

        $action->handle();

        $fs->assertMissing($from);
        $fs->assertExists($to);
    }

    /**
     * The Move Video Action shall move the video.
     *
     * @return void
     */
    public function testVideoDeleted(): void
    {
        /** @var FilesystemAdapter $fs */
        $fs = Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
        Config::set(VideoConstants::DISKS_QUALIFIED, [Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED)]);

        $file = File::fake()->create($this->faker->word().'.webm', $this->faker->randomDigitNotNull());

        $directory = $this->faker->word();

        $video = Video::factory()->createOne([
            Video::ATTRIBUTE_BASENAME => FileFacade::basename($file->path()),
            Video::ATTRIBUTE_FILENAME => FileFacade::name($file->path()),
            Video::ATTRIBUTE_MIMETYPE => MimeType::from($file->path()),
            Video::ATTRIBUTE_PATH => $fs->putFileAs($directory, $file, $file->getClientOriginalName()),
        ]);

        $to = Str::replace($directory, $this->faker->word(), $video->path);

        $action = new MoveVideoAction($video, $to);

        $action->handle();

        static::assertDatabaseHas(Video::class, [Video::ATTRIBUTE_PATH => $to]);
    }
}
