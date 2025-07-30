<?php

declare(strict_types=1);

use App\Constants\Config\DumpConstants;
use App\Enums\Auth\SpecialPermission;
use App\Features\AllowDumpDownloading;
use App\Models\Admin\Dump;
use App\Models\Auth\User;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Laravel\Pennant\Feature;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\get;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('dump downloading not allowed forbidden', function () {
    Feature::deactivate(AllowDumpDownloading::class);

    $dump = Dump::factory()->createOne();

    $response = get(route('dump.show', ['dump' => $dump]));

    $response->assertForbidden();
});

test('dump downloading forbidden for unsafe dumps', function () {
    Feature::activate(AllowDumpDownloading::class);

    $dump = Dump::factory()
        ->unsafe()
        ->createOne();

    $response = get(route('dump.show', ['dump' => $dump]));

    $response->assertForbidden();
});

test('video streaming permitted for bypass', function () {
    Feature::activate(AllowDumpDownloading::class, fake()->boolean());

    $fs = Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));
    $filename = Dump::factory()->makeOne()->path.'.sql';
    $file = File::fake()->create($filename);
    $fsFile = $fs->putFileAs('', $file, $filename);

    $dump = Dump::factory()->createOne([
        Dump::ATTRIBUTE_PATH => $fsFile,
    ]);

    $user = User::factory()->withPermissions(SpecialPermission::BYPASS_FEATURE_FLAGS->value)->createOne();

    Sanctum::actingAs($user);

    $response = get(route('dump.show', ['dump' => $dump]));

    $response->assertDownload($dump->path);
});

test('downloaded through response', function () {
    Feature::activate(AllowDumpDownloading::class);

    $fs = Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));
    $filename = Dump::factory()->makeOne()->path.'.sql';
    $file = File::fake()->create($filename);
    $fsFile = $fs->putFileAs('', $file, $filename);

    $dump = Dump::factory()->createOne([
        Dump::ATTRIBUTE_PATH => $fsFile,
    ]);

    $response = get(route('dump.show', ['dump' => $dump]));

    $response->assertDownload($dump->path);
});
