<?php

declare(strict_types=1);

use App\Actions\Storage\Admin\Dump\DumpWikiAction;
use App\Console\Commands\Storage\Admin\DumpPruneCommand;
use App\Constants\Config\DumpConstants;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('no results', function () {
    Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

    $this->artisan(DumpPruneCommand::class, ['--hours' => 0])
        ->assertFailed()
        ->expectsOutput('No prunings were attempted.');
});

test('deleted', function () {
    Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

    $prunedCount = fake()->randomDigitNotNull();

    Collection::times($prunedCount, function () {
        Date::setTestNow(fake()->iso8601());

        $action = new DumpWikiAction();

        $action->handle();
    });

    Date::setTestNow();

    $this->artisan(DumpPruneCommand::class, ['--hours' => -1])
        ->assertSuccessful()
        ->expectsOutputToContain('Pruned');
});
