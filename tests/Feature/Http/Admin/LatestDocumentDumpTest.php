<?php

declare(strict_types=1);

use App\Actions\Storage\Admin\Dump\DumpDocumentAction;
use App\Actions\Storage\Admin\Dump\DumpWikiAction;
use App\Constants\Config\DumpConstants;
use App\Enums\Auth\SpecialPermission;
use App\Features\AllowDumpDownloading;
use App\Models\Admin\Dump;
use App\Models\Auth\User;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Pennant\Feature;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\get;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('dump downloading not allowed forbidden', function () {
    Storage::fake('local');
    Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

    Feature::deactivate(AllowDumpDownloading::class);

    $action = new DumpWikiAction();

    $action->handle();

    $response = get(route('dump.latest.document.show'));

    $response->assertForbidden();
});

test('video streaming permitted for bypass', function () {
    Storage::fake('local');
    $fs = Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

    Feature::activate(AllowDumpDownloading::class, fake()->boolean());

    Collection::times(fake()->randomDigitNotNull(), function () {
        $action = new DumpWikiAction();

        $action->handle();
    });

    Collection::times(fake()->randomDigitNotNull(), function () {
        $action = new DumpDocumentAction();

        $action->handle();
    });

    $path = Str::of(DumpDocumentAction::FILENAME_PREFIX)
        ->append(fake()->word())
        ->append('.sql')
        ->__toString();

    $file = File::fake()->create($path);
    $fsFile = $fs->putFileAs('', $file, $path);

    $dump = Dump::factory()->createOne([
        Dump::ATTRIBUTE_PATH => $fsFile,
    ]);

    $user = User::factory()->withPermissions(SpecialPermission::BYPASS_FEATURE_FLAGS->value)->createOne();

    Sanctum::actingAs($user);

    $response = get(route('dump.latest.document.show'));

    $response->assertDownload($dump->path);
});

test('not found if no document dumps', function () {
    Storage::fake('local');
    Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

    Feature::activate(AllowDumpDownloading::class);

    $response = get(route('dump.latest.document.show'));

    $response->assertNotFound();
});

test('not found if wiki dumps exist', function () {
    Storage::fake('local');
    Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

    Feature::activate(AllowDumpDownloading::class);

    Collection::times(fake()->randomDigitNotNull(), function () {
        $action = new DumpWikiAction();

        $action->handle();
    });

    $response = get(route('dump.latest.document.show'));

    $response->assertNotFound();
});

test('latest document dump downloaded', function () {
    Storage::fake('local');
    $fs = Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

    Feature::activate(AllowDumpDownloading::class);

    Collection::times(fake()->randomDigitNotNull(), function () {
        $action = new DumpWikiAction();

        $action->handle();
    });

    Collection::times(fake()->randomDigitNotNull(), function () {
        $action = new DumpDocumentAction();

        $action->handle();
    });

    $path = Str::of(DumpDocumentAction::FILENAME_PREFIX)
        ->append(fake()->word())
        ->append('.sql')
        ->__toString();

    $file = File::fake()->create($path);
    $fsFile = $fs->putFileAs('', $file, $path);

    $dump = Dump::factory()->createOne([
        Dump::ATTRIBUTE_PATH => $fsFile,
    ]);

    $response = get(route('dump.latest.document.show'));

    $response->assertDownload($dump->path);
});
