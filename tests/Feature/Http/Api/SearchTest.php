<?php

namespace Tests\Feature\Http\Api;

use App\Http\Resources\AnimeCollection;
use App\Http\Resources\ArtistCollection;
use App\Http\Resources\EntryCollection;
use App\Http\Resources\SeriesCollection;
use App\Http\Resources\SongCollection;
use App\Http\Resources\SynonymCollection;
use App\Http\Resources\ThemeCollection;
use App\Http\Resources\VideoCollection;
use App\JsonApi\QueryParser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * The Search Endpoint shall require a search term.
     *
     * @return void
     */
    public function testNoSearchTerm()
    {
        $response = $this->get(route('api.search'));

        $response->assertJson([]);
    }

    /**
     * The Search Endpoint shall display the Search attributes.
     *
     * @return void
     */
    public function testSearchAttributes()
    {
        $q = $this->faker->word();

        $response = $this->get(route('api.search', [QueryParser::PARAM_SEARCH => $q]));

        $response->assertJson([
            AnimeCollection::$wrap => [],
            ArtistCollection::$wrap => [],
            EntryCollection::$wrap => [],
            SeriesCollection::$wrap => [],
            SongCollection::$wrap => [],
            SynonymCollection::$wrap => [],
            ThemeCollection::$wrap => [],
            VideoCollection::$wrap => [],
        ]);
    }
}
