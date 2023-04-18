<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands\Repositories\Storage\Wiki\Video;

use App\Console\Commands\Repositories\Storage\Wiki\Video\ScriptReconcileCommand;
use App\Constants\Config\VideoConstants;
use App\Models\Wiki\Video\VideoScript;
use App\Repositories\Storage\Wiki\Video\ScriptRepository;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * Class ScriptReconcileTest.
 */
class ScriptReconcileTest extends TestCase
{
    use WithFaker;

    /**
     * If no changes are needed, the Reconcile Script Command shall output 'No Video Scripts created or deleted or updated'.
     *
     * @return void
     */
    public function testNoResults(): void
    {
        Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

        $this->baseRefreshDatabase(); // Cannot lazily refresh database within pending command

        $this->mock(ScriptRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('get')->once()->andReturn(Collection::make());
        });

        $this->artisan(ScriptReconcileCommand::class)
            ->assertSuccessful()
            ->expectsOutput('No Video Scripts created or deleted or updated');
    }

    /**
     * If scripts are created, the Reconcile Script Command shall output '{Created Count} Video Scripts created, 0 Video Scripts deleted, 0 Video Scripts updated'.
     *
     * @return void
     */
    public function testCreated(): void
    {
        Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

        $this->baseRefreshDatabase(); // Cannot lazily refresh database within pending command

        $createdScriptCount = $this->faker->numberBetween(2, 9);

        $scripts = VideoScript::factory()->count($createdScriptCount)->make();

        $this->mock(ScriptRepository::class, function (MockInterface $mock) use ($scripts) {
            $mock->shouldReceive('get')->once()->andReturn($scripts);
        });

        $this->artisan(ScriptReconcileCommand::class)
            ->assertSuccessful()
            ->expectsOutput("$createdScriptCount Video Scripts created, 0 Video Scripts deleted, 0 Video Scripts updated");
    }

    /**
     * If scripts are deleted, the Reconcile Script Command shall output '0 Video Scripts created, {Deleted Count} Video Scripts deleted, 0 Video Scripts updated'.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

        $deletedScriptCount = $this->faker->numberBetween(2, 9);

        VideoScript::factory()->count($deletedScriptCount)->create();

        $this->mock(ScriptRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('get')->once()->andReturn(Collection::make());
        });

        $this->artisan(ScriptReconcileCommand::class)
            ->assertSuccessful()
            ->expectsOutput("0 Video Scripts created, $deletedScriptCount Video Scripts deleted, 0 Video Scripts updated");
    }
}
