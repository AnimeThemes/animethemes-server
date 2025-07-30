<?php

declare(strict_types=1);

use App\Actions\Storage\Admin\Dump\DumpDocumentAction;
use App\Constants\Config\DumpConstants;
use App\Enums\Actions\ActionStatus;
use App\Models\Admin\Dump;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('database dump output', function () {
    $local = Storage::fake('local');
    $fs = Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

    Date::setTestNow(fake()->iso8601());

    $action = new DumpDocumentAction();

    $result = $action->handle();

    static::assertTrue($result->getStatus() === ActionStatus::PASSED);
    static::assertEmpty($local->allFiles());
    static::assertCount(1, $fs->allFiles());
    static::assertDatabaseCount(Dump::class, 1);
});
