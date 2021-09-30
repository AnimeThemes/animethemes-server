<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime\Synonym;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query;
use App\Http\Api\Schema\Wiki\Anime\SynonymSchema;
use App\Http\Resources\Wiki\Anime\Resource\SynonymResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
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
        $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

        $synonym->unsetRelations();

        $response = $this->get(route('api.animesynonym.show', ['animesynonym' => $synonym]));

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
        $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

        $synonym->delete();

        $synonym->unsetRelations();

        $response = $this->get(route('api.animesynonym.show', ['animesynonym' => $synonym]));

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
        $schema = new SynonymSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::$param => $includedPaths->join(','),
        ];

        AnimeSynonym::factory()->for(Anime::factory())->create();

        $synonym = AnimeSynonym::with($includedPaths->all())->first();

        $response = $this->get(route('api.animesynonym.show', ['animesynonym' => $synonym] + $parameters));

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
        $schema = new SynonymSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::$param => [
                SynonymResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

        $synonym->unsetRelations();

        $response = $this->get(route('api.animesynonym.show', ['animesynonym' => $synonym] + $parameters));

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
                Anime::ATTRIBUTE_SEASON => $seasonFilter->description,
            ],
            IncludeParser::$param => AnimeSynonym::RELATION_ANIME,
        ];

        AnimeSynonym::factory()->for(Anime::factory())->create();

        $synonym = AnimeSynonym::with([
            AnimeSynonym::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter) {
                $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.animesynonym.show', ['animesynonym' => $synonym] + $parameters));

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
                Anime::ATTRIBUTE_YEAR => $yearFilter,
            ],
            IncludeParser::$param => AnimeSynonym::RELATION_ANIME,
        ];

        AnimeSynonym::factory()
            ->for(
                Anime::factory()
                    ->state([
                        Anime::ATTRIBUTE_YEAR => $this->faker->boolean() ? $yearFilter : $excludedYear,
                    ])
            )
            ->create();

        $synonym = AnimeSynonym::with([
            AnimeSynonym::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter) {
                $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.animesynonym.show', ['animesynonym' => $synonym] + $parameters));

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
