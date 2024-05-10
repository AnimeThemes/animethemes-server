<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands\Repositories\Storage\Wiki;

use App\Console\Commands\Repositories\Storage\Wiki\AudioReconcileCommand;
use App\Models\Wiki\Audio;
use App\Repositories\Storage\Wiki\AudioRepository;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * Class AudioReconcileTest.
 */
class AudioReconcileTest extends TestCase
{
    use WithFaker;

    /**
     * If no changes are needed, the Reconcile Audio Command shall output 'No Audio created or deleted or updated'.
     *
     * @return void
     */
    public function testNoResults(): void
    {
        $this->mock(AudioRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('get')->once()->andReturn(Collection::make());
        });

        $this->artisan(AudioReconcileCommand::class)
            ->assertSuccessful()
            ->expectsOutput('No Audio created or deleted or updated');
    }

    /**
     * If audios are created, the Reconcile Audio Command shall output '{Created Count} Audio created, 0 Audio deleted, 0 Audio updated'.
     *
     * @return void
     */
    public function testCreated(): void
    {
        $createdAudioCount = $this->faker->numberBetween(2, 9);

        $audios = Audio::factory()->count($createdAudioCount)->make();

        $this->mock(AudioRepository::class, function (MockInterface $mock) use ($audios) {
            $mock->shouldReceive('get')->once()->andReturn($audios);
        });

        $this->artisan(AudioReconcileCommand::class)
            ->assertSuccessful()
            ->expectsOutput("$createdAudioCount Audio created, 0 Audio deleted, 0 Audio updated");
    }

    /**
     * If audios are deleted, the Reconcile Audio Command shall output '0 Audio created, {Deleted Count} Audio deleted, 0 Audio updated'.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $deletedAudioCount = $this->faker->numberBetween(2, 9);

        Audio::factory()->count($deletedAudioCount)->create();

        $this->mock(AudioRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('get')->once()->andReturn(Collection::make());
        });

        $this->artisan(AudioReconcileCommand::class)
            ->assertSuccessful()
            ->expectsOutput("0 Audio created, $deletedAudioCount Audio deleted, 0 Audio updated");
    }

    /**
     * If audios are updated, the Reconcile Audio Command shall output '0 Audio created, 0 Audio deleted, {Updated Count} Audio updated'.
     *
     * @return void
     */
    public function testUpdated(): void
    {
        $updatedAudioCount = $this->faker->numberBetween(2, 9);

        $basenames = collect($this->faker->words($updatedAudioCount));

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
    }
}
