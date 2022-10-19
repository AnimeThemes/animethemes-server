<?php

declare(strict_types=1);

use App\Constants\Config\FlagConstants;
use App\Http\Controllers\Admin\DumpController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

$isDumpDownloadingAllowed = Str::of('is_feature_enabled:')
    ->append(FlagConstants::ALLOW_DUMP_DOWNLOADING_FLAG_QUALIFIED)
    ->append(',Dump Downloading Disabled')
    ->__toString();

Route::get('/{dump}', [DumpController::class, 'show'])
    ->name('dump.show')
    ->middleware($isDumpDownloadingAllowed);
