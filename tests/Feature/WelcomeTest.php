<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WelcomeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The welcome route shall display the home screen.
     *
     * @return void
     */
    public function testWelcome()
    {
        $response = $this->get(route('welcome'));

        $response->assertViewIs('welcome');
    }
}
