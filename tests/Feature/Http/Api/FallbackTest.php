<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api;

use Illuminate\Support\Str;
use Tests\TestCase;

class FallbackTest extends TestCase
{
    /**
     * The API shall return an errors object when requests are made to an
     * unregistered route.
     */
    public function testAbortJson(): void
    {
        $response = $this->get(route('api.anime.index').Str::random());

        $response->assertJsonStructure([
            'message',
        ]);
    }
}
