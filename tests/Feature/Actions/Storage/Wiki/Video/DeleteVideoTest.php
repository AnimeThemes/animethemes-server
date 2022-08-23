<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Storage\Wiki\Video;

use App\Actions\Storage\Wiki\Video\DeleteVideoAction;
use App\Constants\Config\VideoConstants;
use App\Enums\Actions\ActionStatus;
use App\Models\Wiki\Video;
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
    public function testDefault(): void
    {
        Config::set(VideoConstants::DISKS_QUALIFIED, []);
        Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

        $video = Video::factory()->createOne();

        $action = new DeleteVideoAction($video);

        $result = $action->handle();

        static::assertTrue($result->hasFailed());
    }

    /**
     * The Delete Video Action shall pass if there are deletions.
     *
     * @return void
     */
    public function testPassed(): void
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

        static::assertTrue(ActionStatus::PASSED()->is($result->getStatus()));
    }

    /**
     * The Delete Video Action shall delete
     *
     * @return void
     */
    public function testDeletedFromDisk(): void
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
     */
    public function testVideoDeleted(): void
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

        static::assertSoftDeleted($video);
    }
}
