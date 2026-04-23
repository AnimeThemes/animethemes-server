<?php

declare(strict_types=1);

use App\Console\Commands\Repositories\Storage\Wiki\AudioReconcileCommand;
use App\Models\Wiki\Audio;
use App\Repositories\Storage\Wiki\AudioRepository;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Mockery\MockInterface;

uses(WithFaker::class);

test('no results', function (): void {
    $this->mock(AudioRepository::class, function (MockInterface $mock): void {
        $mock->shouldReceive('get')->once()->andReturn(Collection::make());
    });

    $this->artisan(AudioReconcileCommand::class)
        ->assertSuccessful()
        ->expectsOutput('No Audio created or deleted or updated');
});

test('created', function (): void {
    $createdAudioCount = fake()->numberBetween(2, 9);

    $audios = Audio::factory()->count($createdAudioCount)->make();

    $this->mock(AudioRepository::class, function (MockInterface $mock) use ($audios): void {
        $mock->shouldReceive('get')->once()->andReturn($audios);
    });

    $this->artisan(AudioReconcileCommand::class)
        ->assertSuccessful()
        ->expectsOutput("$createdAudioCount Audio created, 0 Audio deleted, 0 Audio updated");
});

test('deleted', function (): void {
    $deletedAudioCount = fake()->numberBetween(2, 9);

    Audio::factory()->count($deletedAudioCount)->create();

    $this->mock(AudioRepository::class, function (MockInterface $mock): void {
        $mock->shouldReceive('get')->once()->andReturn(Collection::make());
    });

    $this->artisan(AudioReconcileCommand::class)
        ->assertSuccessful()
        ->expectsOutput("0 Audio created, $deletedAudioCount Audio deleted, 0 Audio updated");
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
        ->create();

    $this->mock(AudioRepository::class, function (MockInterface $mock) use ($sourceAudios): void {
        $mock->shouldReceive('get')->once()->andReturn($sourceAudios);
    });

    $this->artisan(AudioReconcileCommand::class)
        ->assertSuccessful()
        ->expectsOutput("0 Audio created, 0 Audio deleted, $updatedAudioCount Audio updated");
});
