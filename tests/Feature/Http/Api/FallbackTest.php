<?php

declare(strict_types=1);

use Illuminate\Support\Str;

test('abort json', function () {
    $response = $this->get(route('api.anime.index').Str::random());

    $response->assertJsonStructure([
        'message',
    ]);
});
