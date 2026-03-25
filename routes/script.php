<?php

declare(strict_types=1);

use App\Features\AllowScriptDownloading;
use App\Http\Controllers\Wiki\Video\Script\ScriptController;
use Illuminate\Support\Facades\Route;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

Route::get('/{videoscript}', [ScriptController::class, 'show'])
    ->name('videoscript.show')
    ->middleware(EnsureFeaturesAreActive::using(AllowScriptDownloading::class));
