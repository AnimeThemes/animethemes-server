<?php

declare(strict_types=1);

use App\Actions\Repositories\Wiki\Audio\ReconcileAudioRepositoriesAction;
use App\Enums\Actions\ActionStatus;
use App\Models\Wiki\Audio;
use App\Repositories\Eloquent\Wiki\AudioRepository as AudioDestinationRepository;
use App\Repositories\Storage\Wiki\AudioRepository;
use App\Repositories\Storage\Wiki\AudioRepository as AudioSourceRepository;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Mockery\MockInterface;

uses(WithFaker::class);

test('no results', function (): void {
    $this->mock(AudioSourceRepository::class, function (MockInterface $mock): void {
        $mock->shouldReceive('get')->once()->andReturn(Collection::make());
    });

    $action = new ReconcileAudioRepositoriesAction();

    $source = App::make(AudioSourceRepository::class);
    $destination = App::make(AudioDestinationRepository::class);

    $result = $action->reconcileRepositories($source, $destination);

    $this->assertTrue($result->getStatus() === ActionStatus::PASSED);
    $this->assertFalse($result->hasChanges());
    $this->assertDatabaseCount(Audio::class, 0);
});

test('created', function (): void {
    $createdAudioCount = fake()->numberBetween(2, 9);

    $audios = Audio::factory()->count($createdAudioCount)->make();

    $this->mock(AudioSourceRepository::class, function (MockInterface $mock) use ($audios): void {
        $mock->shouldReceive('get')->once()->andReturn($audios);
    });

    $action = new ReconcileAudioRepositoriesAction();

    $source = App::make(AudioSourceRepository::class);
    $destination = App::make(AudioDestinationRepository::class);

    $result = $action->reconcileRepositories($source, $destination);

    $this->assertTrue($result->getStatus() === ActionStatus::PASSED);
    $this->assertTrue($result->hasChanges());
    $this->assertCount($createdAudioCount, $result->getCreated());
    $this->assertDatabaseCount(Audio::class, $createdAudioCount);
});

test('deleted', function (): void {
    $deletedAudioCount = fake()->numberBetween(2, 9);

    $audios = Audio::factory()->count($deletedAudioCount)->create();

    $this->mock(AudioSourceRepository::class, function (MockInterface $mock): void {
        $mock->shouldReceive('get')->once()->andReturn(Collection::make());
    });

    $action = new ReconcileAudioRepositoriesAction();

    $source = App::make(AudioSourceRepository::class);
    $destination = App::make(AudioDestinationRepository::class);

    $result = $action->reconcileRepositories($source, $destination);

    $this->assertTrue($result->getStatus() === ActionStatus::PASSED);
    $this->assertTrue($result->hasChanges());
    $this->assertCount($deletedAudioCount, $result->getDeleted());

    $this->assertDatabaseCount(Audio::class, $deletedAudioCount);
    foreach ($audios as $audio) {
        $this->assertSoftDeleted($audio);
    }
});

test('updated', function (): void {
    $updatedAudioCount = fake()->numberBetween(2, 9);

    $basenames = collect(fake()->words($updatedAudioCount));

    Audio::factory()
        ->count($updatedAudioCount)
        ->sequence(fn ($sequence): array => [Audio::ATTRIBUTE_BASENAME => $basenames->get($sequence->index)])
        ->create();

    $sourceAudios = Audio::factory()
        ->count($updatedAudioCount)
        ->sequence(fn ($sequence): array => [Audio::ATTRIBUTE_BASENAME => $basenames->get($sequence->index)])
        ->make();

    $this->mock(AudioRepository::class, function (MockInterface $mock) use ($sourceAudios): void {
        $mock->shouldReceive('get')->once()->andReturn($sourceAudios);
    });

    $action = new ReconcileAudioRepositoriesAction();

    $source = App::make(AudioSourceRepository::class);
    $destination = App::make(AudioDestinationRepository::class);

    $result = $action->reconcileRepositories($source, $destination);

    $this->assertTrue($result->getStatus() === ActionStatus::PASSED);
    $this->assertTrue($result->hasChanges());
    $this->assertCount($updatedAudioCount, $result->getUpdated());
    $this->assertDatabaseCount(Audio::class, $updatedAudioCount);
});
