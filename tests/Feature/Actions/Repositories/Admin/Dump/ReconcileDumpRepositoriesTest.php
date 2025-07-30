<?php

declare(strict_types=1);

use App\Actions\Repositories\Admin\Dump\ReconcileDumpRepositoriesAction;
use App\Constants\Config\DumpConstants;
use App\Enums\Actions\ActionStatus;
use App\Models\Admin\Dump;
use App\Repositories\Eloquent\Admin\DumpRepository as DumpDestinationRepository;
use App\Repositories\Storage\Admin\DumpRepository as DumpSourceRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('no results', function () {
    Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

    $this->mock(DumpSourceRepository::class, function (MockInterface $mock) {
        $mock->shouldReceive('get')->once()->andReturn(Collection::make());
    });

    $action = new ReconcileDumpRepositoriesAction();

    $source = App::make(DumpSourceRepository::class);
    $destination = App::make(DumpDestinationRepository::class);

    $result = $action->reconcileRepositories($source, $destination);

    $this->assertTrue($result->getStatus() === ActionStatus::PASSED);
    $this->assertFalse($result->hasChanges());
    $this->assertDatabaseCount(Dump::class, 0);
});

test('created', function () {
    Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

    $createdDumpCount = fake()->numberBetween(2, 9);

    $dumps = Dump::factory()->count($createdDumpCount)->make();

    $this->mock(DumpSourceRepository::class, function (MockInterface $mock) use ($dumps) {
        $mock->shouldReceive('get')->once()->andReturn($dumps);
    });

    $action = new ReconcileDumpRepositoriesAction();

    $source = App::make(DumpSourceRepository::class);
    $destination = App::make(DumpDestinationRepository::class);

    $result = $action->reconcileRepositories($source, $destination);

    $this->assertTrue($result->getStatus() === ActionStatus::PASSED);
    $this->assertTrue($result->hasChanges());
    $this->assertCount($createdDumpCount, $result->getCreated());
    $this->assertDatabaseCount(Dump::class, $createdDumpCount);
});

test('deleted', function () {
    Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

    $deletedDumpCount = fake()->numberBetween(2, 9);

    Dump::factory()->count($deletedDumpCount)->create();

    $this->mock(DumpSourceRepository::class, function (MockInterface $mock) {
        $mock->shouldReceive('get')->once()->andReturn(Collection::make());
    });

    $action = new ReconcileDumpRepositoriesAction();

    $source = App::make(DumpSourceRepository::class);
    $destination = App::make(DumpDestinationRepository::class);

    $result = $action->reconcileRepositories($source, $destination);

    $this->assertTrue($result->getStatus() === ActionStatus::PASSED);
    $this->assertTrue($result->hasChanges());
    $this->assertCount($deletedDumpCount, $result->getDeleted());
    $this->assertEmpty(Dump::all());
});
