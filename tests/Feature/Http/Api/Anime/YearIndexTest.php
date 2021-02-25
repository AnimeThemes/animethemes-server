<?php

namespace Tests\Feature\Http\Api\Anime;

use App\Models\Anime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class YearIndexTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * The Year Index Endpoint shall display a list of unique years of anime.
     *
     * @return void
     */
    public function testDefault()
    {
        $anime = Anime::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $response = $this->get(route('api.year.index'));

        $response->assertJson(
            $anime->unique('year')->sortBy('year')->pluck('year')->all(),
        );
    }
}
