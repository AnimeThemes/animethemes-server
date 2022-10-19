<?php

declare(strict_types=1);

use App\Constants\Config\FlagConstants;
use App\Http\Controllers\Wiki\Video\Script\ScriptController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

$isScriptDownloadingAllowed = Str::of('is_feature_enabled:')
    ->append(FlagConstants::ALLOW_SCRIPT_DOWNLOADING_FLAG_QUALIFIED)
    ->append(',Script Downloading Disabled')
    ->__toString();

Route::get('/{videoscript}', [ScriptController::class, 'show'])
    ->name('videoscript.show')
    ->middleware($isScriptDownloadingAllowed);
