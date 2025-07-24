<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Repositories\Wiki\Video\Script;

use App\Actions\Repositories\Wiki\Video\Script\ReconcileScriptRepositoriesAction;
use App\Enums\Actions\ActionStatus;
use App\Models\Wiki\Video\VideoScript;
use App\Repositories\Eloquent\Wiki\Video\ScriptRepository as ScriptDestinationRepository;
use App\Repositories\Storage\Wiki\Video\ScriptRepository as ScriptSourceRepository;
use Exception;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Mockery\MockInterface;
use Tests\TestCase;

class ReconcileScriptRepositoriesTest extends TestCase
{
    use WithFaker;

    /**
     * If no changes are needed, the Reconcile Script Repository Action shall indicate no changes were made.
     *
     * @throws Exception
     */
    public function testNoResults(): void
    {
        $this->mock(ScriptSourceRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('get')->once()->andReturn(Collection::make());
        });

        $action = new ReconcileScriptRepositoriesAction();

        $source = App::make(ScriptSourceRepository::class);
        $destination = App::make(ScriptDestinationRepository::class);

        $result = $action->reconcileRepositories($source, $destination);

        static::assertTrue($result->getStatus() === ActionStatus::PASSED);
        static::assertFalse($result->hasChanges());
        static::assertDatabaseCount(VideoScript::class, 0);
    }

    /**
     * If video scripts are created, the Reconcile Script Repository Action shall return created video scripts.
     *
     * @throws Exception
     */
    public function testCreated(): void
    {
        $createdScriptCount = $this->faker->numberBetween(2, 9);

        $scripts = VideoScript::factory()->count($createdScriptCount)->make();

        $this->mock(ScriptSourceRepository::class, function (MockInterface $mock) use ($scripts) {
            $mock->shouldReceive('get')->once()->andReturn($scripts);
        });

        $action = new ReconcileScriptRepositoriesAction();

        $source = App::make(ScriptSourceRepository::class);
        $destination = App::make(ScriptDestinationRepository::class);

        $result = $action->reconcileRepositories($source, $destination);

        static::assertTrue($result->getStatus() === ActionStatus::PASSED);
        static::assertTrue($result->hasChanges());
        static::assertCount($createdScriptCount, $result->getCreated());
        static::assertDatabaseCount(VideoScript::class, $createdScriptCount);
    }

    /**
     * If video scripts are deleted, the Reconcile Script Repository Action shall return deleted video scripts.
     *
     * @throws Exception
     */
    public function testDeleted(): void
    {
        $deletedScriptCount = $this->faker->numberBetween(2, 9);

        $scripts = VideoScript::factory()->count($deletedScriptCount)->create();

        $this->mock(ScriptSourceRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('get')->once()->andReturn(Collection::make());
        });

        $action = new ReconcileScriptRepositoriesAction();

        $source = App::make(ScriptSourceRepository::class);
        $destination = App::make(ScriptDestinationRepository::class);

        $result = $action->reconcileRepositories($source, $destination);

        static::assertTrue($result->getStatus() === ActionStatus::PASSED);
        static::assertTrue($result->hasChanges());
        static::assertCount($deletedScriptCount, $result->getDeleted());

        static::assertDatabaseCount(VideoScript::class, $deletedScriptCount);
        foreach ($scripts as $script) {
            static::assertSoftDeleted($script);
        }
    }
}
