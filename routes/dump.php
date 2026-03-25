<?php

declare(strict_types=1);

use App\Features\AllowDumpDownloading;
use App\Http\Controllers\Admin\DumpController;
use App\Http\Controllers\Admin\LatestContentDumpController;
use Illuminate\Support\Facades\Route;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

$isDumpDownloadingAllowed = EnsureFeaturesAreActive::using(AllowDumpDownloading::class);

Route::get('/latest/content', [LatestContentDumpController::class, 'show'])
    ->name('dump.latest.content.show')
    ->middleware($isDumpDownloadingAllowed);

Route::get('/latest/wiki', [LatestContentDumpController::class, 'show'])
    ->name('dump.latest.wiki.show')
    ->middleware($isDumpDownloadingAllowed);

Route::get('/{dump}', [DumpController::class, 'show'])
    ->name('dump.show')
    ->middleware($isDumpDownloadingAllowed);
