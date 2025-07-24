<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Repositories\Admin\Dump;

use App\Actions\Repositories\Admin\Dump\ReconcileDumpRepositoriesAction;
use App\Constants\Config\DumpConstants;
use App\Enums\Actions\ActionStatus;
use App\Models\Admin\Dump;
use App\Repositories\Eloquent\Admin\DumpRepository as DumpDestinationRepository;
use App\Repositories\Storage\Admin\DumpRepository as DumpSourceRepository;
use Exception;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;
use Tests\TestCase;

class ReconcileDumpRepositoriesTest extends TestCase
{
    use WithFaker;

    /**
     * If no changes are needed, the Reconcile Dump Repository Action shall indicate no changes were made.
     *
     * @throws Exception
     */
    public function testNoResults(): void
    {
        Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

        $this->mock(DumpSourceRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('get')->once()->andReturn(Collection::make());
        });

        $action = new ReconcileDumpRepositoriesAction();

        $source = App::make(DumpSourceRepository::class);
        $destination = App::make(DumpDestinationRepository::class);

        $result = $action->reconcileRepositories($source, $destination);

        static::assertTrue($result->getStatus() === ActionStatus::PASSED);
        static::assertFalse($result->hasChanges());
        static::assertDatabaseCount(Dump::class, 0);
    }

    /**
     * If dumps are created, the Reconcile Dump Repository Action shall return created dumps.
     *
     * @throws Exception
     */
    public function testCreated(): void
    {
        Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

        $createdDumpCount = $this->faker->numberBetween(2, 9);

        $dumps = Dump::factory()->count($createdDumpCount)->make();

        $this->mock(DumpSourceRepository::class, function (MockInterface $mock) use ($dumps) {
            $mock->shouldReceive('get')->once()->andReturn($dumps);
        });

        $action = new ReconcileDumpRepositoriesAction();

        $source = App::make(DumpSourceRepository::class);
        $destination = App::make(DumpDestinationRepository::class);

        $result = $action->reconcileRepositories($source, $destination);

        static::assertTrue($result->getStatus() === ActionStatus::PASSED);
        static::assertTrue($result->hasChanges());
        static::assertCount($createdDumpCount, $result->getCreated());
        static::assertDatabaseCount(Dump::class, $createdDumpCount);
    }

    /**
     * If dumps are deleted, the Reconcile Dump Repository Action shall return deleted dumps.
     *
     * @throws Exception
     */
    public function testDeleted(): void
    {
        Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

        $deletedDumpCount = $this->faker->numberBetween(2, 9);

        Dump::factory()->count($deletedDumpCount)->create();

        $this->mock(DumpSourceRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('get')->once()->andReturn(Collection::make());
        });

        $action = new ReconcileDumpRepositoriesAction();

        $source = App::make(DumpSourceRepository::class);
        $destination = App::make(DumpDestinationRepository::class);

        $result = $action->reconcileRepositories($source, $destination);

        static::assertTrue($result->getStatus() === ActionStatus::PASSED);
        static::assertTrue($result->hasChanges());
        static::assertCount($deletedDumpCount, $result->getDeleted());
        static::assertEmpty(Dump::all());
    }
}
