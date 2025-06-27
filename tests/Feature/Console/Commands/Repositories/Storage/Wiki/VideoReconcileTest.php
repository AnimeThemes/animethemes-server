<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands\Repositories\Storage\Wiki;

use App\Console\Commands\Repositories\Storage\Wiki\VideoReconcileCommand;
use App\Constants\Config\VideoConstants;
use App\Models\Wiki\Video;
use App\Repositories\Storage\Wiki\VideoRepository;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * Class VideoReconcileTest.
 */
class VideoReconcileTest extends TestCase
{
    use WithFaker;

    /**
     * If no changes are needed, the Reconcile Video Command shall output 'No Videos created or deleted or updated'.
     *
     * @return void
     */
    public function testNoResults(): void
    {
        Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

        $this->mock(VideoRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('get')->once()->andReturn(Collection::make());
        });

        $this->artisan(VideoReconcileCommand::class)
            ->assertSuccessful()
            ->expectsOutput('No Videos created or deleted or updated');
    }

    /**
     * If videos are created, the Reconcile Video Command shall output '{Created Count} Videos created, 0 Videos deleted, 0 Videos updated'.
     *
     * @return void
     */
    public function testCreated(): void
    {
        Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

        $createdVideoCount = $this->faker->numberBetween(2, 9);

        $videos = Video::factory()->count($createdVideoCount)->make();

        $this->mock(VideoRepository::class, function (MockInterface $mock) use ($videos) {
            $mock->shouldReceive('get')->once()->andReturn($videos);
        });

        $this->artisan(VideoReconcileCommand::class)
            ->assertSuccessful()
            ->expectsOutput("$createdVideoCount Videos created, 0 Videos deleted, 0 Videos updated");
    }

    /**
     * If videos are deleted, the Reconcile Video Command shall output '0 Videos created, {Deleted Count} Videos deleted, 0 Videos updated'.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

        $deletedVideoCount = $this->faker->numberBetween(2, 9);

        Video::factory()->count($deletedVideoCount)->create();

        $this->mock(VideoRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('get')->once()->andReturn(Collection::make());
        });

        $this->artisan(VideoReconcileCommand::class)
            ->assertSuccessful()
            ->expectsOutput("0 Videos created, $deletedVideoCount Videos deleted, 0 Videos updated");
    }

    /**
     * If videos are updated, the Reconcile Video Command shall output '0 Videos created, 0 Videos deleted, {Updated Count} Videos updated'.
     *
     * @return void
     */
    public function testUpdated(): void
    {
        Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

        $updatedVideoCount = $this->faker->numberBetween(2, 9);

        $basenames = collect($this->faker->words($updatedVideoCount));

        Video::factory()
            ->count($updatedVideoCount)
            ->sequence(fn ($sequence) => [Video::ATTRIBUTE_BASENAME => $basenames->get($sequence->index)])
            ->create();

        $sourceVideos = Video::factory()
            ->count($updatedVideoCount)
            ->sequence(fn ($sequence) => [Video::ATTRIBUTE_BASENAME => $basenames->get($sequence->index)])
            ->create();

        $this->mock(VideoRepository::class, function (MockInterface $mock) use ($sourceVideos) {
            $mock->shouldReceive('get')->once()->andReturn($sourceVideos);
        });

        $this->artisan(VideoReconcileCommand::class)
            ->assertSuccessful()
            ->expectsOutput("0 Videos created, 0 Videos deleted, $updatedVideoCount Videos updated");
    }
}
