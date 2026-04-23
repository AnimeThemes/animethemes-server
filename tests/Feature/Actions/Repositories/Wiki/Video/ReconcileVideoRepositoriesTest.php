<?php

declare(strict_types=1);

use App\Actions\Repositories\Wiki\Video\ReconcileVideoRepositoriesAction;
use App\Enums\Actions\ActionStatus;
use App\Models\Wiki\Video;
use App\Repositories\Eloquent\Wiki\VideoRepository as VideoDestinationRepository;
use App\Repositories\Storage\Wiki\VideoRepository;
use App\Repositories\Storage\Wiki\VideoRepository as VideoSourceRepository;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Mockery\MockInterface;

uses(WithFaker::class);

test('no results', function (): void {
    $this->mock(VideoSourceRepository::class, function (MockInterface $mock): void {
        $mock->shouldReceive('get')->once()->andReturn(Collection::make());
    });

    $action = new ReconcileVideoRepositoriesAction();

    $source = App::make(VideoSourceRepository::class);
    $destination = App::make(VideoDestinationRepository::class);

    $result = $action->reconcileRepositories($source, $destination);

    $this->assertTrue($result->getStatus() === ActionStatus::PASSED);
    $this->assertFalse($result->hasChanges());
    $this->assertDatabaseCount(Video::class, 0);
});

test('created', function (): void {
    $createdVideoCount = fake()->numberBetween(2, 9);

    $videos = Video::factory()->count($createdVideoCount)->make();

    $this->mock(VideoSourceRepository::class, function (MockInterface $mock) use ($videos): void {
        $mock->shouldReceive('get')->once()->andReturn($videos);
    });

    $action = new ReconcileVideoRepositoriesAction();

    $source = App::make(VideoSourceRepository::class);
    $destination = App::make(VideoDestinationRepository::class);

    $result = $action->reconcileRepositories($source, $destination);

    $this->assertTrue($result->getStatus() === ActionStatus::PASSED);
    $this->assertTrue($result->hasChanges());
    $this->assertCount($createdVideoCount, $result->getCreated());
    $this->assertDatabaseCount(Video::class, $createdVideoCount);
});

test('deleted', function (): void {
    $deletedVideoCount = fake()->numberBetween(2, 9);

    $videos = Video::factory()->count($deletedVideoCount)->create();

    $this->mock(VideoSourceRepository::class, function (MockInterface $mock): void {
        $mock->shouldReceive('get')->once()->andReturn(Collection::make());
    });

    $action = new ReconcileVideoRepositoriesAction();

    $source = App::make(VideoSourceRepository::class);
    $destination = App::make(VideoDestinationRepository::class);

    $result = $action->reconcileRepositories($source, $destination);

    $this->assertTrue($result->getStatus() === ActionStatus::PASSED);
    $this->assertTrue($result->hasChanges());
    $this->assertCount($deletedVideoCount, $result->getDeleted());

    $this->assertDatabaseCount(Video::class, $deletedVideoCount);
    foreach ($videos as $video) {
        $this->assertSoftDeleted($video);
    }
});

test('updated', function (): void {
    $updatedVideoCount = fake()->numberBetween(2, 9);

    $basenames = collect(fake()->words($updatedVideoCount));

    Video::factory()
        ->count($updatedVideoCount)
        ->sequence(fn ($sequence): array => [Video::ATTRIBUTE_BASENAME => $basenames->get($sequence->index)])
        ->create();

    $sourceVideos = Video::factory()
        ->count($updatedVideoCount)
        ->sequence(fn ($sequence): array => [Video::ATTRIBUTE_BASENAME => $basenames->get($sequence->index)])
        ->make();

    $this->mock(VideoRepository::class, function (MockInterface $mock) use ($sourceVideos): void {
        $mock->shouldReceive('get')->once()->andReturn($sourceVideos);
    });

    $action = new ReconcileVideoRepositoriesAction();

    $source = App::make(VideoSourceRepository::class);
    $destination = App::make(VideoDestinationRepository::class);

    $result = $action->reconcileRepositories($source, $destination);

    $this->assertTrue($result->getStatus() === ActionStatus::PASSED);
    $this->assertTrue($result->hasChanges());
    $this->assertCount($updatedVideoCount, $result->getUpdated());
    $this->assertDatabaseCount(Video::class, $updatedVideoCount);
});
