<?php

declare(strict_types=1);

use App\Console\Commands\Repositories\Storage\Wiki\VideoReconcileCommand;
use App\Constants\Config\VideoConstants;
use App\Models\Wiki\Video;
use App\Repositories\Storage\Wiki\VideoRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('no results', function () {
    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

    $this->mock(VideoRepository::class, function (MockInterface $mock) {
        $mock->shouldReceive('get')->once()->andReturn(Collection::make());
    });

    $this->artisan(VideoReconcileCommand::class)
        ->assertSuccessful()
        ->expectsOutput('No Videos created or deleted or updated');
});

test('created', function () {
    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

    $createdVideoCount = fake()->numberBetween(2, 9);

    $videos = Video::factory()->count($createdVideoCount)->make();

    $this->mock(VideoRepository::class, function (MockInterface $mock) use ($videos) {
        $mock->shouldReceive('get')->once()->andReturn($videos);
    });

    $this->artisan(VideoReconcileCommand::class)
        ->assertSuccessful()
        ->expectsOutput("$createdVideoCount Videos created, 0 Videos deleted, 0 Videos updated");
});

test('deleted', function () {
    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

    $deletedVideoCount = fake()->numberBetween(2, 9);

    Video::factory()->count($deletedVideoCount)->create();

    $this->mock(VideoRepository::class, function (MockInterface $mock) {
        $mock->shouldReceive('get')->once()->andReturn(Collection::make());
    });

    $this->artisan(VideoReconcileCommand::class)
        ->assertSuccessful()
        ->expectsOutput("0 Videos created, $deletedVideoCount Videos deleted, 0 Videos updated");
});

test('updated', function () {
    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

    $updatedVideoCount = fake()->numberBetween(2, 9);

    $basenames = collect(fake()->words($updatedVideoCount));

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
});
