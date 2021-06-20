<?php

declare(strict_types=1);

namespace Http\Api;

use App\Http\Api\QueryParser;
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use App\Http\Resources\Wiki\Collection\ArtistCollection;
use App\Http\Resources\Wiki\Collection\EntryCollection;
use App\Http\Resources\Wiki\Collection\SeriesCollection;
use App\Http\Resources\Wiki\Collection\SongCollection;
use App\Http\Resources\Wiki\Collection\SynonymCollection;
use App\Http\Resources\Wiki\Collection\ThemeCollection;
use App\Http\Resources\Wiki\Collection\VideoCollection;
use App\Http\Resources\Wiki\Resource\SearchResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

/**
 * Class SearchTest.
 */
class SearchTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * The Search Endpoint shall require a search term.
     *
     * @return void
     */
    public function testNoSearchTerm()
    {
        $response = $this->get(route('api.search.index'));

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

        $response = $this->get(route('api.search.index', $parameters));

        $response->assertJson([
            SearchResource::$wrap => [
                AnimeCollection::$wrap => [],
                ArtistCollection::$wrap => [],
                EntryCollection::$wrap => [],
                SeriesCollection::$wrap => [],
                SongCollection::$wrap => [],
                SynonymCollection::$wrap => [],
                ThemeCollection::$wrap => [],
                VideoCollection::$wrap => [],
            ],
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

        $includedFields = Arr::random($fields, $this->faker->numberBetween(1, count($fields)));

        $q = $this->faker->word();

        $parameters = [
            QueryParser::PARAM_SEARCH => $q,
            QueryParser::PARAM_FIELDS => [
                SearchResource::$wrap => implode(',', $includedFields),
            ],
        ];

        $response = $this->get(route('api.search.index', $parameters));

        $response->assertJsonStructure([
            SearchResource::$wrap => $includedFields,
        ]);
    }
}
