<?php

namespace Tests\Feature\Http\Api;

use App\Http\Resources\AnimeCollection;
use App\Http\Resources\ArtistCollection;
use App\Http\Resources\EntryCollection;
use App\Http\Resources\SearchResource;
use App\Http\Resources\SeriesCollection;
use App\Http\Resources\SongCollection;
use App\Http\Resources\SynonymCollection;
use App\Http\Resources\ThemeCollection;
use App\Http\Resources\VideoCollection;
use App\JsonApi\QueryParser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
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

        $parameters = [
            QueryParser::PARAM_SEARCH => $q,
        ];

        $response = $this->get(route('api.search', $parameters));

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

    /**
     * The Search Endpoint shall allow each resource to be included/excluded in a sparse fieldset.
     *
     * @return void
     */
    public function testSearchSparseFieldsets()
    {
        $fields = [
            AnimeCollection::$wrap,
            ArtistCollection::$wrap,
            EntryCollection::$wrap,
            SeriesCollection::$wrap,
            SongCollection::$wrap,
            SynonymCollection::$wrap,
            ThemeCollection::$wrap,
            VideoCollection::$wrap,
        ];

        $included_fields = Arr::random($fields, $this->faker->numberBetween(1, count($fields)));
        $excluded_fields = array_diff($fields, $included_fields);

        $q = $this->faker->word();

        $parameters = [
            QueryParser::PARAM_SEARCH => $q,
            QueryParser::PARAM_FIELDS => [
                SearchResource::$resourceType => implode(',', $included_fields),
            ],
        ];

        $response = $this->get(route('api.search', $parameters));

        $response->assertJson(array_fill_keys($included_fields, []));
        $response->assertJsonMissing(array_fill_keys($excluded_fields, []));
    }
}
