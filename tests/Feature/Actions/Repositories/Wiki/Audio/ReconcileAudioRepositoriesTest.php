<?php

declare(strict_types=1);

use App\Actions\Repositories\Wiki\Audio\ReconcileAudioRepositoriesAction;
use App\Enums\Actions\ActionStatus;
use App\Models\Wiki\Audio;
use App\Repositories\Eloquent\Wiki\AudioRepository as AudioDestinationRepository;
use App\Repositories\Storage\Wiki\AudioRepository;
use App\Repositories\Storage\Wiki\AudioRepository as AudioSourceRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Mockery\MockInterface;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('no results', function () {
    $this->mock(AudioSourceRepository::class, function (MockInterface $mock) {
        $mock->shouldReceive('get')->once()->andReturn(Collection::make());
    });

    $action = new ReconcileAudioRepositoriesAction();

    $source = App::make(AudioSourceRepository::class);
    $destination = App::make(AudioDestinationRepository::class);

    $result = $action->reconcileRepositories($source, $destination);

    static::assertTrue($result->getStatus() === ActionStatus::PASSED);
    static::assertFalse($result->hasChanges());
    static::assertDatabaseCount(Audio::class, 0);
});

test('created', function () {
    $createdAudioCount = fake()->numberBetween(2, 9);

    $audios = Audio::factory()->count($createdAudioCount)->make();

    $this->mock(AudioSourceRepository::class, function (MockInterface $mock) use ($audios) {
        $mock->shouldReceive('get')->once()->andReturn($audios);
    });

    $action = new ReconcileAudioRepositoriesAction();

    $source = App::make(AudioSourceRepository::class);
    $destination = App::make(AudioDestinationRepository::class);

    $result = $action->reconcileRepositories($source, $destination);

    static::assertTrue($result->getStatus() === ActionStatus::PASSED);
    static::assertTrue($result->hasChanges());
    static::assertCount($createdAudioCount, $result->getCreated());
    static::assertDatabaseCount(Audio::class, $createdAudioCount);
});

test('deleted', function () {
    $deletedAudioCount = fake()->numberBetween(2, 9);

    $audios = Audio::factory()->count($deletedAudioCount)->create();

    $this->mock(AudioSourceRepository::class, function (MockInterface $mock) {
        $mock->shouldReceive('get')->once()->andReturn(Collection::make());
    });

    $action = new ReconcileAudioRepositoriesAction();

    $source = App::make(AudioSourceRepository::class);
    $destination = App::make(AudioDestinationRepository::class);

    $result = $action->reconcileRepositories($source, $destination);

    static::assertTrue($result->getStatus() === ActionStatus::PASSED);
    static::assertTrue($result->hasChanges());
    static::assertCount($deletedAudioCount, $result->getDeleted());

    static::assertDatabaseCount(Audio::class, $deletedAudioCount);
    foreach ($audios as $audio) {
        static::assertSoftDeleted($audio);
    }
});

test('updated', function () {
    $updatedAudioCount = fake()->numberBetween(2, 9);

    $basenames = collect(fake()->words($updatedAudioCount));

    Audio::factory()
        ->count($updatedAudioCount)
        ->sequence(fn ($sequence) => [Audio::ATTRIBUTE_BASENAME => $basenames->get($sequence->index)])
        ->create();

    $sourceAudios = Audio::factory()
        ->count($updatedAudioCount)
        ->sequence(fn ($sequence) => [Audio::ATTRIBUTE_BASENAME => $basenames->get($sequence->index)])
        ->make();

    $this->mock(AudioRepository::class, function (MockInterface $mock) use ($sourceAudios) {
        $mock->shouldReceive('get')->once()->andReturn($sourceAudios);
    });

    $action = new ReconcileAudioRepositoriesAction();

    $source = App::make(AudioSourceRepository::class);
    $destination = App::make(AudioDestinationRepository::class);

    $result = $action->reconcileRepositories($source, $destination);

    static::assertTrue($result->getStatus() === ActionStatus::PASSED);
    static::assertTrue($result->hasChanges());
    static::assertCount($updatedAudioCount, $result->getUpdated());
    static::assertDatabaseCount(Audio::class, $updatedAudioCount);
});
