<?php

declare(strict_types=1);

use App\Constants\Config\VideoConstants;
use App\Enums\Auth\SpecialPermission;
use App\Features\AllowScriptDownloading;
use App\Models\Auth\User;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Laravel\Pennant\Feature;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\get;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('script downloading not allowed forbidden', function () {
    Feature::deactivate(AllowScriptDownloading::class);

    $script = VideoScript::factory()->createOne();

    $response = get(route('videoscript.show', ['videoscript' => $script]));

    $response->assertForbidden();
});

test('video streaming permitted for bypass', function () {
    Feature::activate(AllowScriptDownloading::class, fake()->boolean());

    $fs = Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));
    $file = File::fake()->create(fake()->word().'.txt');
    $fsFile = $fs->putFile('', $file);

    $script = VideoScript::factory()->createOne([
        VideoScript::ATTRIBUTE_PATH => $fsFile,
    ]);

    $user = User::factory()->withPermissions(SpecialPermission::BYPASS_FEATURE_FLAGS->value)->createOne();

    Sanctum::actingAs($user);

    $response = get(route('videoscript.show', ['videoscript' => $script]));

    $response->assertDownload($script->path);
});

test('cannot stream soft deleted video', function () {
    Feature::activate(AllowScriptDownloading::class);

    $script = VideoScript::factory()->trashed()->createOne();

    $response = get(route('videoscript.show', ['videoscript' => $script]));

    $response->assertNotFound();
});

test('downloaded through response', function () {
    Feature::activate(AllowScriptDownloading::class);

    $fs = Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));
    $file = File::fake()->create(fake()->word().'.txt');
    $fsFile = $fs->putFile('', $file);

    $script = VideoScript::factory()->createOne([
        VideoScript::ATTRIBUTE_PATH => $fsFile,
    ]);

    $response = get(route('videoscript.show', ['videoscript' => $script]));

    $response->assertDownload($script->path);
});
