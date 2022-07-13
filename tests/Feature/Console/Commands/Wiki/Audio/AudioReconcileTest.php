<?php

declare(strict_types=1);

namespace Console\Commands\Wiki\Audio;

use App\Console\Commands\Wiki\Audio\AudioReconcileCommand;
use App\Models\Wiki\Audio;
use App\Repositories\Service\DigitalOcean\Wiki\AudioRepository;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Collection;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * Class AudioReconcileTest.
 */
class AudioReconcileTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * If no changes are needed, the Reconcile Audio Command shall output 'No Audios created or deleted or updated'.
     *
     * @return void
     */
    public function testNoResults(): void
    {
        $this->mock(AudioRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('get')->once()->andReturn(Collection::make());
        });

        $this->artisan(AudioReconcileCommand::class)->expectsOutput('No Audios created or deleted or updated');
    }

    /**
     * If audios are created, the Reconcile Audio Command shall output '{Created Count} Audios created, 0 Audios deleted, 0 Audios updated'.
     *
     * @return void
     */
    public function testCreated(): void
    {
        $this->baseRefreshDatabase(); // Cannot lazily refresh database within pending command

        $createdAudioCount = $this->faker->randomDigitNotNull();

        $audios = Audio::factory()->count($createdAudioCount)->make();

        $this->mock(AudioRepository::class, function (MockInterface $mock) use ($audios) {
            $mock->shouldReceive('get')->once()->andReturn($audios);
        });

        $this->artisan(AudioReconcileCommand::class)->expectsOutput("$createdAudioCount Audios created, 0 Audios deleted, 0 Audios updated");
    }

    /**
     * If audios are deleted, the Reconcile Audio Command shall output '0 Audios created, {Deleted Count} Audios deleted, 0 Audios updated'.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $deletedAudioCount = $this->faker->randomDigitNotNull();

        Audio::factory()->count($deletedAudioCount)->create();

        $this->mock(AudioRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('get')->once()->andReturn(Collection::make());
        });

        $this->artisan(AudioReconcileCommand::class)->expectsOutput("0 Audios created, $deletedAudioCount Audios deleted, 0 Audios updated");
    }

    /**
     * If audios are updated, the Reconcile Audio Command shall output '0 Audios created, 0 Audios deleted, {Updated Count} Audios updated'.
     *
     * @return void
     */
    public function testUpdated(): void
    {
        $updatedAudioCount = $this->faker->randomDigitNotNull();

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

        $this->artisan(AudioReconcileCommand::class)->expectsOutput("0 Audios created, 0 Audios deleted, $updatedAudioCount Audios updated");
    }
}
