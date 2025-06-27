<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Storage\Wiki\Video;

use App\Actions\Storage\Wiki\Video\DeleteVideoAction;
use App\Constants\Config\VideoConstants;
use App\Enums\Actions\ActionStatus;
use App\Models\Wiki\Video;
use Exception;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Http\Testing\MimeType;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Class DeleteVideoTest.
 */
class DeleteVideoTest extends TestCase
{
    use WithFaker;

    /**
     * The Delete Video Action shall fail if there are no deletions.
     *
     * @return void
     */
    public function test_default(): void
    {
        Config::set(VideoConstants::DISKS_QUALIFIED, []);
        Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

        $video = Video::factory()->createOne();

        $action = new DeleteVideoAction($video);

        $storageResults = $action->handle();

        $result = $storageResults->toActionResult();

        static::assertTrue($result->hasFailed());
    }

    /**
     * The Delete Video Action shall pass if there are deletions.
     *
     * @return void
     */
    public function test_passed(): void
    {
        Config::set(VideoConstants::DISKS_QUALIFIED, [Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED)]);
        Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

        $file = File::fake()->create($this->faker->word().'.webm', $this->faker->randomDigitNotNull());

        $video = Video::factory()->createOne([
            Video::ATTRIBUTE_BASENAME => FileFacade::basename($file->path()),
            Video::ATTRIBUTE_FILENAME => FileFacade::name($file->path()),
            Video::ATTRIBUTE_MIMETYPE => MimeType::from($file->path()),
            Video::ATTRIBUTE_PATH => $file->path(),
        ]);

        $action = new DeleteVideoAction($video);

        $storageResults = $action->handle();

        $result = $storageResults->toActionResult();

        static::assertTrue($result->getStatus() === ActionStatus::PASSED);
    }

    /**
     * The Delete Video Action shall delete the file from the configured disks.
     *
     * @return void
     */
    public function test_deleted_from_disk(): void
    {
        Config::set(VideoConstants::DISKS_QUALIFIED, [Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED)]);
        Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

        $file = File::fake()->create($this->faker->word().'.webm', $this->faker->randomDigitNotNull());

        $video = Video::factory()->createOne([
            Video::ATTRIBUTE_BASENAME => FileFacade::basename($file->path()),
            Video::ATTRIBUTE_FILENAME => FileFacade::name($file->path()),
            Video::ATTRIBUTE_MIMETYPE => MimeType::from($file->path()),
            Video::ATTRIBUTE_PATH => $file->path(),
        ]);

        $action = new DeleteVideoAction($video);

        $action->handle();

        static::assertEmpty(Storage::disk(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED))->allFiles());
    }

    /**
     * The Delete Video Action shall delete the video.
     *
     * @return void
     *
     * @throws Exception
     */
    public function test_video_deleted(): void
    {
        Config::set(VideoConstants::DISKS_QUALIFIED, [Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED)]);
        Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

        $file = File::fake()->create($this->faker->word().'.webm', $this->faker->randomDigitNotNull());

        $video = Video::factory()->createOne([
            Video::ATTRIBUTE_BASENAME => FileFacade::basename($file->path()),
            Video::ATTRIBUTE_FILENAME => FileFacade::name($file->path()),
            Video::ATTRIBUTE_MIMETYPE => MimeType::from($file->path()),
            Video::ATTRIBUTE_PATH => $file->path(),
        ]);

        $action = new DeleteVideoAction($video);

        $result = $action->handle();

        $action->then($result);

        static::assertSoftDeleted($video);
    }
}
