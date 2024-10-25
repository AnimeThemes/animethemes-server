<?php

declare(strict_types=1);

use App\Actions\Models\Wiki\BackfillSongAction;
use App\Models\Wiki\Song;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/test', function () {
    $action = new BackfillSongAction(Song::find(2), 'https://claris.lnk.to/Hitorigoto');

    $action->handle();

});