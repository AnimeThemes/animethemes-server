<?php

declare(strict_types=1);

namespace Http\Api;

use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class FallbackTest
 * @package Http\Api
 */
class FallbackTest extends TestCase
{
    /**
     * The API shall return an errors object when requests are made to an
     * unregistered route.
     *
     * @return void
     */
    public function testAbortJson()
    {
        $response = $this->get(url('api/'.Str::random()));

        $response->assertJsonStructure([
            'errors',
        ]);
    }
}
