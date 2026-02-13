<?php

declare(strict_types=1);

use App\Features\AllowDumpDownloading;
use App\Http\Controllers\Admin\DumpController;
use App\Http\Controllers\Admin\LatestWikiDumpController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

$isDumpDownloadingAllowed = Str::of(EnsureFeaturesAreActive::class)
    ->append(':')
    ->append(AllowDumpDownloading::class)
    ->__toString();

Route::get('/latest/wiki', [LatestWikiDumpController::class, 'show'])
    ->name('dump.latest.wiki.show')
    ->middleware($isDumpDownloadingAllowed);

Route::get('/{dump}', [DumpController::class, 'show'])
    ->name('dump.show')
    ->middleware($isDumpDownloadingAllowed);
