<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Repositories\Wiki\Video;

use App\Actions\Repositories\Wiki\Video\ReconcileVideoRepositoriesAction;
use App\Enums\Actions\ActionStatus;
use App\Models\Wiki\Video;
use App\Repositories\Eloquent\Wiki\VideoRepository as VideoDestinationRepository;
use App\Repositories\Storage\Wiki\VideoRepository;
use App\Repositories\Storage\Wiki\VideoRepository as VideoSourceRepository;
use Exception;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * Class ReconcileVideoRepositoriesTest.
 */
class ReconcileVideoRepositoriesTest extends TestCase
{
    use WithFaker;

    /**
     * If no changes are needed, the Reconcile Video Repository Action shall indicate no changes were made.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testNoResults(): void
    {
        $this->mock(VideoSourceRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('get')->once()->andReturn(Collection::make());
        });

        $action = new ReconcileVideoRepositoriesAction();

        $source = App::make(VideoSourceRepository::class);
        $destination = App::make(VideoDestinationRepository::class);

        $result = $action->reconcileRepositories($source, $destination);

        static::assertTrue(ActionStatus::PASSED === $result->getStatus());
        static::assertFalse($result->hasChanges());
        static::assertDatabaseCount(Video::class, 0);
    }

    /**
     * If videos are created, the Reconcile Video Repository Action shall return created videos.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testCreated(): void
    {
        $createdVideoCount = $this->faker->numberBetween(2, 9);

        $videos = Video::factory()->count($createdVideoCount)->make();

        $this->mock(VideoSourceRepository::class, function (MockInterface $mock) use ($videos) {
            $mock->shouldReceive('get')->once()->andReturn($videos);
        });

        $action = new ReconcileVideoRepositoriesAction();

        $source = App::make(VideoSourceRepository::class);
        $destination = App::make(VideoDestinationRepository::class);

        $result = $action->reconcileRepositories($source, $destination);

        static::assertTrue(ActionStatus::PASSED === $result->getStatus());
        static::assertTrue($result->hasChanges());
        static::assertCount($createdVideoCount, $result->getCreated());
        static::assertDatabaseCount(Video::class, $createdVideoCount);
    }

    /**
     * If videos are deleted, the Reconcile Video Repository Action shall return deleted videos.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testDeleted(): void
    {
        $deletedVideoCount = $this->faker->numberBetween(2, 9);

        $videos = Video::factory()->count($deletedVideoCount)->create();

        $this->mock(VideoSourceRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('get')->once()->andReturn(Collection::make());
        });

        $action = new ReconcileVideoRepositoriesAction();

        $source = App::make(VideoSourceRepository::class);
        $destination = App::make(VideoDestinationRepository::class);

        $result = $action->reconcileRepositories($source, $destination);

        static::assertTrue(ActionStatus::PASSED === $result->getStatus());
        static::assertTrue($result->hasChanges());
        static::assertCount($deletedVideoCount, $result->getDeleted());

        static::assertDatabaseCount(Video::class, $deletedVideoCount);
        foreach ($videos as $video) {
            static::assertSoftDeleted($video);
        }
    }

    /**
     * If videos are updated, the Reconcile Video Repository Action shall return updated videos.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testUpdated(): void
    {
        $updatedVideoCount = $this->faker->numberBetween(2, 9);

        $basenames = collect($this->faker->words($updatedVideoCount));

        Video::factory()
            ->count($updatedVideoCount)
            ->sequence(fn ($sequence) => [Video::ATTRIBUTE_BASENAME => $basenames->get($sequence->index)])
            ->create();

        $sourceVideos = Video::factory()
            ->count($updatedVideoCount)
            ->sequence(fn ($sequence) => [Video::ATTRIBUTE_BASENAME => $basenames->get($sequence->index)])
            ->make();

        $this->mock(VideoRepository::class, function (MockInterface $mock) use ($sourceVideos) {
            $mock->shouldReceive('get')->once()->andReturn($sourceVideos);
        });

        $action = new ReconcileVideoRepositoriesAction();

        $source = App::make(VideoSourceRepository::class);
        $destination = App::make(VideoDestinationRepository::class);

        $result = $action->reconcileRepositories($source, $destination);

        static::assertTrue(ActionStatus::PASSED === $result->getStatus());
        static::assertTrue($result->hasChanges());
        static::assertCount($updatedVideoCount, $result->getUpdated());
        static::assertDatabaseCount(Video::class, $updatedVideoCount);
    }
}
