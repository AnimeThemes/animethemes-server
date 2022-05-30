<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class FallbackTest.
 */
class FallbackTest extends TestCase
{
    /**
     * The API shall return an errors object when requests are made to an
     * unregistered route.
     *
     * @return void
     */
    public function testAbortJson(): void
    {
        $url = Str::of(Config::get('api.url'))
            ->append('/')
            ->append(Str::random())
            ->__toString();

        $response = $this->get($url);

        $response->assertJsonStructure([
            'message',
        ]);
    }
}
