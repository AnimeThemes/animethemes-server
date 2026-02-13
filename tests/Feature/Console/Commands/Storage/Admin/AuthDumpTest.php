<?php

declare(strict_types=1);

use App\Console\Commands\Storage\Admin\AuthDumpCommand;
use App\Constants\Config\DumpConstants;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('database dump output', function () {
    Storage::fake('local');
    Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

    Date::setTestNow(fake()->iso8601());

    $this->artisan(AuthDumpCommand::class)
        ->assertSuccessful()
        ->expectsOutputToContain('has been created');
});

test('database dump file', function () {
    $local = Storage::fake('local');
    $fs = Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

    Date::setTestNow(fake()->iso8601());

    $this->artisan(AuthDumpCommand::class)->run();

    $this->assertEmpty($local->allFiles());
    $this->assertCount(1, $fs->allFiles());
});
