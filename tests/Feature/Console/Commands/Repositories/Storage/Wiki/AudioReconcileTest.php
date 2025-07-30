<?php

declare(strict_types=1);

use App\Console\Commands\Repositories\Storage\Wiki\AudioReconcileCommand;
use App\Models\Wiki\Audio;
use App\Repositories\Storage\Wiki\AudioRepository;
use Illuminate\Support\Collection;
use Mockery\MockInterface;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('no results', function () {
    $this->mock(AudioRepository::class, function (MockInterface $mock) {
        $mock->shouldReceive('get')->once()->andReturn(Collection::make());
    });

    $this->artisan(AudioReconcileCommand::class)
        ->assertSuccessful()
        ->expectsOutput('No Audio created or deleted or updated');
});

test('created', function () {
    $createdAudioCount = fake()->numberBetween(2, 9);

    $audios = Audio::factory()->count($createdAudioCount)->make();

    $this->mock(AudioRepository::class, function (MockInterface $mock) use ($audios) {
        $mock->shouldReceive('get')->once()->andReturn($audios);
    });

    $this->artisan(AudioReconcileCommand::class)
        ->assertSuccessful()
        ->expectsOutput("$createdAudioCount Audio created, 0 Audio deleted, 0 Audio updated");
});

test('deleted', function () {
    $deletedAudioCount = fake()->numberBetween(2, 9);

    Audio::factory()->count($deletedAudioCount)->create();

    $this->mock(AudioRepository::class, function (MockInterface $mock) {
        $mock->shouldReceive('get')->once()->andReturn(Collection::make());
    });

    $this->artisan(AudioReconcileCommand::class)
        ->assertSuccessful()
        ->expectsOutput("0 Audio created, $deletedAudioCount Audio deleted, 0 Audio updated");
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
        ->create();

    $this->mock(AudioRepository::class, function (MockInterface $mock) use ($sourceAudios) {
        $mock->shouldReceive('get')->once()->andReturn($sourceAudios);
    });

    $this->artisan(AudioReconcileCommand::class)
        ->assertSuccessful()
        ->expectsOutput("0 Audio created, 0 Audio deleted, $updatedAudioCount Audio updated");
});
