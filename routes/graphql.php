<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| GraphQL Routes
|--------------------------------------------------------------------------
|
| Here is where you can register graphql routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/sandbox', function () {
    return view('graphql.sandbox', [
        'endpoint' => route('graphql'),
    ]);
});