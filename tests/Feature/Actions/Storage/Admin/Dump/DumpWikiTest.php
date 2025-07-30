<?php

declare(strict_types=1);

use App\Actions\Storage\Admin\Dump\DumpWikiAction;
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

    $action = new DumpWikiAction();

    $result = $action->handle();

    $this->assertTrue($result->getStatus() === ActionStatus::PASSED);
    $this->assertEmpty($local->allFiles());
    $this->assertCount(1, $fs->allFiles());
    $this->assertDatabaseCount(Dump::class, 1);
});
