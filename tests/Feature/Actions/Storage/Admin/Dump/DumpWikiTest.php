<?php

declare(strict_types=1);

use App\Actions\Storage\Admin\Dump\DumpContentAction;
use App\Constants\Config\DumpConstants;
use App\Enums\Actions\ActionStatus;
use App\Models\Admin\Dump;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;

pest()->use(WithFaker::class);

test('database dump output', function (): void {
    $local = Storage::fake('local');
    $fs = Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

    Date::setTestNow(fake()->iso8601());

    $action = new DumpContentAction();

    $result = $action->handle();

    expect($result->getStatus())->toBe(ActionStatus::PASSED);
    expect($local->allFiles())->toBeEmpty();
    expect($fs->allFiles())->toHaveCount(1);
    $this->assertDatabaseCount(Dump::class, 1);
});
