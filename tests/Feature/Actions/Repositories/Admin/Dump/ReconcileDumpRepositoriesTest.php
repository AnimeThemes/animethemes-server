<?php

declare(strict_types=1);

use App\Actions\Repositories\Admin\Dump\ReconcileDumpRepositoriesAction;
use App\Constants\Config\DumpConstants;
use App\Enums\Actions\ActionStatus;
use App\Models\Admin\Dump;
use App\Repositories\Eloquent\Admin\DumpRepository as DumpDestinationRepository;
use App\Repositories\Storage\Admin\DumpRepository as DumpSourceRepository;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;

pest()->use(WithFaker::class);

test('no results', function (): void {
    Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

    $this->mock(DumpSourceRepository::class, function (MockInterface $mock): void {
        $mock->shouldReceive('get')->once()->andReturn(Collection::make());
    });

    $action = new ReconcileDumpRepositoriesAction();

    $source = App::make(DumpSourceRepository::class);
    $destination = App::make(DumpDestinationRepository::class);

    $result = $action->reconcileRepositories($source, $destination);

    expect($result->getStatus())->toBe(ActionStatus::PASSED);
    expect($result->hasChanges())->toBeFalse();
    $this->assertDatabaseCount(Dump::class, 0);
});

test('created', function (): void {
    Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

    $createdDumpCount = fake()->numberBetween(2, 9);

    $dumps = Dump::factory()->count($createdDumpCount)->make();

    $this->mock(DumpSourceRepository::class, function (MockInterface $mock) use ($dumps): void {
        $mock->shouldReceive('get')->once()->andReturn($dumps);
    });

    $action = new ReconcileDumpRepositoriesAction();

    $source = App::make(DumpSourceRepository::class);
    $destination = App::make(DumpDestinationRepository::class);

    $result = $action->reconcileRepositories($source, $destination);

    expect($result->getStatus())->toBe(ActionStatus::PASSED);
    expect($result->hasChanges())->toBeTrue();
    expect($result->getCreated())->toHaveCount($createdDumpCount);
    $this->assertDatabaseCount(Dump::class, $createdDumpCount);
});

test('deleted', function (): void {
    Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

    $deletedDumpCount = fake()->numberBetween(2, 9);

    Dump::factory()->count($deletedDumpCount)->create();

    $this->mock(DumpSourceRepository::class, function (MockInterface $mock): void {
        $mock->shouldReceive('get')->once()->andReturn(Collection::make());
    });

    $action = new ReconcileDumpRepositoriesAction();

    $source = App::make(DumpSourceRepository::class);
    $destination = App::make(DumpDestinationRepository::class);

    $result = $action->reconcileRepositories($source, $destination);

    expect($result->getStatus())->toBe(ActionStatus::PASSED);
    expect($result->hasChanges())->toBeTrue();
    expect($result->getDeleted())->toHaveCount($deletedDumpCount);
    expect(Dump::all())->toBeEmpty();
});
