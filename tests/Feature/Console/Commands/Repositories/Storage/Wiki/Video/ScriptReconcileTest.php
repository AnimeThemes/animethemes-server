<?php

declare(strict_types=1);

use App\Console\Commands\Repositories\Storage\Wiki\Video\ScriptReconcileCommand;
use App\Constants\Config\VideoConstants;
use App\Models\Wiki\Video\VideoScript;
use App\Repositories\Storage\Wiki\Video\ScriptRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('no results', function () {
    Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

    $this->mock(ScriptRepository::class, function (MockInterface $mock) {
        $mock->shouldReceive('get')->once()->andReturn(Collection::make());
    });

    $this->artisan(ScriptReconcileCommand::class)
        ->assertSuccessful()
        ->expectsOutput('No Video Scripts created or deleted or updated');
});

test('created', function () {
    Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

    $createdScriptCount = fake()->numberBetween(2, 9);

    $scripts = VideoScript::factory()->count($createdScriptCount)->make();

    $this->mock(ScriptRepository::class, function (MockInterface $mock) use ($scripts) {
        $mock->shouldReceive('get')->once()->andReturn($scripts);
    });

    $this->artisan(ScriptReconcileCommand::class)
        ->assertSuccessful()
        ->expectsOutput("$createdScriptCount Video Scripts created, 0 Video Scripts deleted, 0 Video Scripts updated");
});

test('deleted', function () {
    Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

    $deletedScriptCount = fake()->numberBetween(2, 9);

    VideoScript::factory()->count($deletedScriptCount)->create();

    $this->mock(ScriptRepository::class, function (MockInterface $mock) {
        $mock->shouldReceive('get')->once()->andReturn(Collection::make());
    });

    $this->artisan(ScriptReconcileCommand::class)
        ->assertSuccessful()
        ->expectsOutput("0 Video Scripts created, $deletedScriptCount Video Scripts deleted, 0 Video Scripts updated");
});
