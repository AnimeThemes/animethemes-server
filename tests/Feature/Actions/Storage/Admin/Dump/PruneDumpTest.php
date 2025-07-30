<?php

declare(strict_types=1);

use App\Actions\Storage\Admin\Dump\DumpWikiAction;
use App\Actions\Storage\Admin\Dump\PruneDumpAction;
use App\Constants\Config\DumpConstants;
use App\Enums\Actions\ActionStatus;
use App\Models\Admin\Dump;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('no results', function () {
    $fs = Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

    $action = new PruneDumpAction(fake()->numberBetween(2, 9));

    $pruneResults = $action->handle();

    $result = $pruneResults->toActionResult();

    static::assertEmpty($fs->allFiles());
    static::assertTrue($result->hasFailed());
    static::assertDatabaseCount(Dump::class, 0);
});

test('pruned', function () {
    $fs = Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

    $prunedCount = fake()->randomDigitNotNull();

    Collection::times($prunedCount, function () {
        Date::setTestNow(fake()->iso8601());

        $action = new DumpWikiAction();

        $action->handle();
    });

    Date::setTestNow();

    $action = new PruneDumpAction(-1);

    $pruneResults = $action->handle();

    $action->then($pruneResults);

    $result = $pruneResults->toActionResult();

    static::assertEmpty($fs->allFiles());
    static::assertTrue($result->getStatus() === ActionStatus::PASSED);
    static::assertEmpty(Dump::all());
});
