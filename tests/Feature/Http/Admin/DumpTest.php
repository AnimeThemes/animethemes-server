<?php

declare(strict_types=1);

use App\Constants\Config\DumpConstants;
use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Features\AllowDumpDownloading;
use App\Models\Admin\Dump;
use App\Models\Auth\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Laravel\Pennant\Feature;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(WithFaker::class);

test('dump downloading not allowed forbidden', function (): void {
    Feature::deactivate(AllowDumpDownloading::class);

    $dump = Dump::factory()->createOne();

    $response = get(route('dump.show', ['dump' => $dump]));

    $response->assertForbidden();
});

test('dump downloading permitted for bypass', function (): void {
    Feature::activate(AllowDumpDownloading::class, fake()->boolean());

    $fs = Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));
    $filename = Dump::factory()->makeOne()->path.'.sql';
    $file = File::fake()->create($filename);
    $fsFile = $fs->putFileAs('', $file, $filename);

    $dump = Dump::factory()->createOne([
        Dump::ATTRIBUTE_PATH => $fsFile,
    ]);

    $user = User::factory()
        ->withAdmin()
        ->withPermissions(
            CrudPermission::VIEW->format(Dump::class),
            SpecialPermission::BYPASS_FEATURE_FLAGS->value
        )
        ->createOne();

    actingAs($user);

    $response = get(route('dump.show', ['dump' => $dump]));

    $response->assertDownload($dump->path);
});

test('downloaded through response', function (): void {
    Feature::activate(AllowDumpDownloading::class);

    $fs = Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));
    $filename = Dump::factory()->makeOne()->path.'.sql';
    $file = File::fake()->create($filename);
    $fsFile = $fs->putFileAs('', $file, $filename);

    $dump = Dump::factory()->createOne([
        Dump::ATTRIBUTE_PATH => $fsFile,
    ]);

    $user = User::factory()
        ->withAdmin()
        ->withPermissions(
            CrudPermission::VIEW->format(Dump::class)
        )
        ->createOne();

    actingAs($user);

    $response = get(route('dump.show', ['dump' => $dump]));

    $response->assertDownload($dump->path);
});
