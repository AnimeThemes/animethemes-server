<?php

declare(strict_types=1);

use App\Features\AllowDumpDownloading;
use App\Http\Controllers\Admin\DumpController;
use Illuminate\Support\Facades\Route;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

$isDumpDownloadingAllowed = EnsureFeaturesAreActive::using(AllowDumpDownloading::class);

Route::get('/{dump}', [DumpController::class, 'show'])
    ->name('dump.show')
    ->middleware($isDumpDownloadingAllowed);
