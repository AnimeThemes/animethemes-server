<?php

declare(strict_types=1);

use Illuminate\Support\Str;

use function Pest\Laravel\get;

test('abort json', function () {
    $response = get(route('api.anime.index').Str::random());

    $response->assertJsonStructure([
        'message',
    ]);
});
