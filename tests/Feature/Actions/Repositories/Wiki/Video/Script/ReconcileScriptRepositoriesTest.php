<?php

declare(strict_types=1);

use App\Actions\Repositories\Wiki\Video\Script\ReconcileScriptRepositoriesAction;
use App\Enums\Actions\ActionStatus;
use App\Models\Wiki\Video\VideoScript;
use App\Repositories\Eloquent\Wiki\Video\ScriptRepository as ScriptDestinationRepository;
use App\Repositories\Storage\Wiki\Video\ScriptRepository as ScriptSourceRepository;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Mockery\MockInterface;

uses(WithFaker::class);

test('no results', function (): void {
    $this->mock(ScriptSourceRepository::class, function (MockInterface $mock): void {
        $mock->shouldReceive('get')->once()->andReturn(Collection::make());
    });

    $action = new ReconcileScriptRepositoriesAction();

    $source = App::make(ScriptSourceRepository::class);
    $destination = App::make(ScriptDestinationRepository::class);

    $result = $action->reconcileRepositories($source, $destination);

    $this->assertTrue($result->getStatus() === ActionStatus::PASSED);
    $this->assertFalse($result->hasChanges());
    $this->assertDatabaseCount(VideoScript::class, 0);
});

test('created', function (): void {
    $createdScriptCount = fake()->numberBetween(2, 9);

    $scripts = VideoScript::factory()->count($createdScriptCount)->make();

    $this->mock(ScriptSourceRepository::class, function (MockInterface $mock) use ($scripts): void {
        $mock->shouldReceive('get')->once()->andReturn($scripts);
    });

    $action = new ReconcileScriptRepositoriesAction();

    $source = App::make(ScriptSourceRepository::class);
    $destination = App::make(ScriptDestinationRepository::class);

    $result = $action->reconcileRepositories($source, $destination);

    $this->assertTrue($result->getStatus() === ActionStatus::PASSED);
    $this->assertTrue($result->hasChanges());
    $this->assertCount($createdScriptCount, $result->getCreated());
    $this->assertDatabaseCount(VideoScript::class, $createdScriptCount);
});

test('deleted', function (): void {
    $deletedScriptCount = fake()->numberBetween(2, 9);

    $scripts = VideoScript::factory()->count($deletedScriptCount)->create();

    $this->mock(ScriptSourceRepository::class, function (MockInterface $mock): void {
        $mock->shouldReceive('get')->once()->andReturn(Collection::make());
    });

    $action = new ReconcileScriptRepositoriesAction();

    $source = App::make(ScriptSourceRepository::class);
    $destination = App::make(ScriptDestinationRepository::class);

    $result = $action->reconcileRepositories($source, $destination);

    $this->assertTrue($result->getStatus() === ActionStatus::PASSED);
    $this->assertTrue($result->hasChanges());
    $this->assertCount($deletedScriptCount, $result->getDeleted());

    $this->assertDatabaseCount(VideoScript::class, $deletedScriptCount);
    foreach ($scripts as $script) {
        $this->assertSoftDeleted($script);
    }
});
