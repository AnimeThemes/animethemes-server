<?php

declare(strict_types=1);

use App\Console\Commands\Repositories\Storage\Admin\DumpReconcileCommand;
use App\Constants\Config\DumpConstants;
use App\Models\Admin\Dump;
use App\Repositories\Storage\Admin\DumpRepository;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;

uses(WithFaker::class);

test('no results', function (): void {
    $this->mock(DumpRepository::class, function (MockInterface $mock): void {
        $mock->shouldReceive('get')->once()->andReturn(Collection::make());
    });

    $this->artisan(DumpReconcileCommand::class)
        ->assertSuccessful()
        ->expectsOutput('No Dumps created or deleted or updated');
});

test('created', function (): void {
    Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

    $createdDumpCount = fake()->numberBetween(2, 9);

    $dumps = Dump::factory()->count($createdDumpCount)->make();

    $this->mock(DumpRepository::class, function (MockInterface $mock) use ($dumps): void {
        $mock->shouldReceive('get')->once()->andReturn($dumps);
    });

    $this->artisan(DumpReconcileCommand::class)
        ->assertSuccessful()
        ->expectsOutput("$createdDumpCount Dumps created, 0 Dumps deleted, 0 Dumps updated");
});

test('deleted', function (): void {
    Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

    $deletedDumpCount = fake()->numberBetween(2, 9);

    Dump::factory()->count($deletedDumpCount)->create();

    $this->mock(DumpRepository::class, function (MockInterface $mock): void {
        $mock->shouldReceive('get')->once()->andReturn(Collection::make());
    });

    $this->artisan(DumpReconcileCommand::class)
        ->assertSuccessful()
        ->expectsOutput("0 Dumps created, $deletedDumpCount Dumps deleted, 0 Dumps updated");
});
