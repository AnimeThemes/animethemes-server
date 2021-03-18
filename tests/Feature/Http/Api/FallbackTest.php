<?php

namespace Tests\Feature\Http\Api;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FallbackTest extends TestCase
{
    use WithFaker;

    /**
     * The API shall return an errors object when requests are made to an
     * unregistered route.
     *
     * @return void
     */
    public function testAbortJson()
    {
        $response = $this->get(url('api/'.$this->faker->word));

        $response->assertJsonStructure([
            'errors'
        ]);
    }
}
