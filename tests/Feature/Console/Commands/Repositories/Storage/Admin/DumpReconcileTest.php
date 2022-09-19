<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands\Repositories\Storage\Admin;

use App\Console\Commands\Repositories\Storage\Admin\DumpReconcileCommand;
use App\Constants\Config\DumpConstants;
use App\Models\Admin\Dump;
use App\Repositories\Storage\Admin\DumpRepository;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * Class DumpReconcileTest.
 */
class DumpReconcileTest extends TestCase
{
    use WithFaker;

    /**
     * If no changes are needed, the Reconcile Dump Command shall output 'No Dumps created or deleted or updated'.
     *
     * @return void
     */
    public function testNoResults(): void
    {
        $this->baseRefreshDatabase(); // Cannot lazily refresh database within pending command

        $this->mock(DumpRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('get')->once()->andReturn(Collection::make());
        });

        $this->artisan(DumpReconcileCommand::class)
            ->assertSuccessful()
            ->expectsOutput('No Dumps created or deleted or updated');
    }

    /**
     * If dumps are created, the Reconcile Dump Command shall output '{Created Count} Dumps created, 0 Dumps deleted, 0 Dumps updated'.
     *
     * @return void
     */
    public function testCreated(): void
    {
        Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

        $this->baseRefreshDatabase(); // Cannot lazily refresh database within pending command

        $createdDumpCount = $this->faker->numberBetween(2, 9);

        $dumps = Dump::factory()->count($createdDumpCount)->make();

        $this->mock(DumpRepository::class, function (MockInterface $mock) use ($dumps) {
            $mock->shouldReceive('get')->once()->andReturn($dumps);
        });

        $this->artisan(DumpReconcileCommand::class)
            ->assertSuccessful()
            ->expectsOutput("$createdDumpCount Dumps created, 0 Dumps deleted, 0 Dumps updated");
    }

    /**
     * If dumps are deleted, the Reconcile Dump Command shall output '0 Dumps created, {Deleted Count} Dumps deleted, 0 Dumps updated'.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

        $deletedDumpCount = $this->faker->numberBetween(2, 9);

        Dump::factory()->count($deletedDumpCount)->create();

        $this->mock(DumpRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('get')->once()->andReturn(Collection::make());
        });

        $this->artisan(DumpReconcileCommand::class)
            ->assertSuccessful()
            ->expectsOutput("0 Dumps created, $deletedDumpCount Dumps deleted, 0 Dumps updated");
    }
}
