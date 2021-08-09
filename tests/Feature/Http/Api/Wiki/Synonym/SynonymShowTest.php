<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Synonym;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query;
use App\Http\Resources\Wiki\Resource\SynonymResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Synonym;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class SynonymShowTest.
 */
class SynonymShowTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    use WithoutEvents;

    /**
     * By default, the Synonym Show Endpoint shall return a Synonym Resource.
     *
     * @return void
     */
    public function testDefault()
    {
        $synonym = Synonym::factory()->for(Anime::factory())->createOne();

        $synonym->unsetRelations();

        $response = $this->get(route('api.synonym.show', ['synonym' => $synonym]));

        $response->assertJson(
            json_decode(
                json_encode(
                    SynonymResource::make($synonym, Query::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Synonym Show Endpoint shall return an Synonym Synonym for soft deleted synonyms.
     *
     * @return void
     */
    public function testSoftDelete()
    {
        $synonym = Synonym::factory()->for(Anime::factory())->createOne();

        $synonym->delete();

        $synonym->unsetRelations();

        $response = $this->get(route('api.synonym.show', ['synonym' => $synonym]));

        $response->assertJson(
            json_decode(
                json_encode(
                    SynonymResource::make($synonym, Query::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Synonym Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths()
    {
        $allowedPaths = collect(SynonymResource::allowedIncludePaths());
        $includedPaths = $allowedPaths->random($this->faker->numberBetween(0, count($allowedPaths)));

        $parameters = [
            IncludeParser::$param => $includedPaths->join(','),
        ];

        Synonym::factory()->for(Anime::factory())->create();

        $synonym = Synonym::with($includedPaths->all())->first();

        $response = $this->get(route('api.synonym.show', ['synonym' => $synonym] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SynonymResource::make($synonym, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Synonym Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets()
    {
        $fields = collect([
            'id',
            'text',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);

        $includedFields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            FieldParser::$param => [
                SynonymResource::$wrap => $includedFields->join(','),
            ],
        ];

        $synonym = Synonym::factory()->for(Anime::factory())->createOne();

        $synonym->unsetRelations();

        $response = $this->get(route('api.synonym.show', ['synonym' => $synonym] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SynonymResource::make($synonym, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Synonym Show Endpoint shall support constrained eager loading of anime by season.
     *
     * @return void
     */
    public function testAnimeBySeason()
    {
        $seasonFilter = AnimeSeason::getRandomInstance();

        $parameters = [
            FilterParser::$param => [
                'season' => $seasonFilter->description,
            ],
            IncludeParser::$param => 'anime',
        ];

        Synonym::factory()->for(Anime::factory())->create();

        $synonym = Synonym::with([
            'anime' => function (BelongsTo $query) use ($seasonFilter) {
                $query->where('season', $seasonFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.synonym.show', ['synonym' => $synonym] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SynonymResource::make($synonym, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Synonym Show Endpoint shall support constrained eager loading of anime by year.
     *
     * @return void
     */
    public function testAnimeByYear()
    {
        $yearFilter = intval($this->faker->year());
        $excludedYear = $yearFilter + 1;

        $parameters = [
            FilterParser::$param => [
                'year' => $yearFilter,
            ],
            IncludeParser::$param => 'anime',
        ];

        Synonym::factory()
            ->for(
                Anime::factory()
                    ->state([
                        'year' => $this->faker->boolean() ? $yearFilter : $excludedYear,
                    ])
            )
            ->create();

        $synonym = Synonym::with([
            'anime' => function (BelongsTo $query) use ($yearFilter) {
                $query->where('year', $yearFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.synonym.show', ['synonym' => $synonym] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SynonymResource::make($synonym, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
