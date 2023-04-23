<?php

declare(strict_types=1);

use App\Features\AllowScriptDownloading;
use App\Http\Controllers\Wiki\Video\Script\ScriptController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

$isScriptDownloadingAllowed = Str::of(EnsureFeaturesAreActive::class)
    ->append(':')
    ->append(AllowScriptDownloading::class)
    ->__toString();

Route::get('/{videoscript}', [ScriptController::class, 'show'])
    ->name('videoscript.show')
    ->middleware($isScriptDownloadingAllowed);
