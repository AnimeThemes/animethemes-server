<?php

declare(strict_types=1);

use App\Constants\Config\FlagConstants;
use App\Http\Controllers\Admin\DumpController;
use App\Http\Controllers\Admin\LatestDocumentDumpController;
use App\Http\Controllers\Admin\LatestWikiDumpController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

$isDumpDownloadingAllowed = Str::of('is_feature_enabled:')
    ->append(FlagConstants::ALLOW_DUMP_DOWNLOADING_FLAG_QUALIFIED)
    ->append(',Dump Downloading Disabled')
    ->__toString();

Route::get('/latest/document', [LatestDocumentDumpController::class, 'show'])
    ->name('dump.latest.document.show')
    ->middleware($isDumpDownloadingAllowed);

Route::get('/latest/wiki', [LatestWikiDumpController::class, 'show'])
    ->name('dump.latest.wiki.show')
    ->middleware($isDumpDownloadingAllowed);

Route::get('/{dump}', [DumpController::class, 'show'])
    ->name('dump.show')
    ->middleware([$isDumpDownloadingAllowed, 'without_trashed:dump']);
