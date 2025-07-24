<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime\Synonym;

use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Wiki\Anime\SynonymSchema;
use App\Http\Resources\Wiki\Anime\Resource\SynonymResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

class SynonymShowTest extends TestCase
{
    use WithFaker;

    /**
     * By default, the Synonym Show Endpoint shall return a Synonym Resource.
     */
    public function testDefault(): void
    {
        $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

        $synonym->unsetRelations();

        $response = $this->get(route('api.animesynonym.show', ['animesynonym' => $synonym]));

        $response->assertJson(
            json_decode(
                json_encode(
                    new SynonymResource($synonym, new Query())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Synonym Show Endpoint shall return a Synonym Resource for soft deleted synonyms.
     */
    public function testSoftDelete(): void
    {
        $synonym = AnimeSynonym::factory()
            ->trashed()
            ->for(Anime::factory())
            ->createOne();

        $synonym->unsetRelations();

        $response = $this->get(route('api.animesynonym.show', ['animesynonym' => $synonym]));

        $response->assertJson(
            json_decode(
                json_encode(
                    new SynonymResource($synonym, new Query())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Synonym Show Endpoint shall allow inclusion of related resources.
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new SynonymSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

        $response = $this->get(route('api.animesynonym.show', ['animesynonym' => $synonym] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new SynonymResource($synonym, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Synonym Index Endpoint shall implement sparse fieldsets.
     */
    public function testSparseFieldsets(): void
    {
        $schema = new SynonymSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                SynonymResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

        $synonym->unsetRelations();

        $response = $this->get(route('api.animesynonym.show', ['animesynonym' => $synonym] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new SynonymResource($synonym, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Synonym Show Endpoint shall support constrained eager loading of anime by media format.
     */
    public function testAnimeByMediaFormat(): void
    {
        $mediaFormatFilter = Arr::random(AnimeMediaFormat::cases());

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_MEDIA_FORMAT => $mediaFormatFilter->localize(),
            ],
            IncludeParser::param() => AnimeSynonym::RELATION_ANIME,
        ];

        $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

        $synonym->unsetRelations()->load([
            AnimeSynonym::RELATION_ANIME => function (BelongsTo $query) use ($mediaFormatFilter) {
                $query->where(Anime::ATTRIBUTE_MEDIA_FORMAT, $mediaFormatFilter->value);
            },
        ]);

        $response = $this->get(route('api.animesynonym.show', ['animesynonym' => $synonym] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new SynonymResource($synonym, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Synonym Show Endpoint shall support constrained eager loading of anime by season.
     */
    public function testAnimeBySeason(): void
    {
        $seasonFilter = Arr::random(AnimeSeason::cases());

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_SEASON => $seasonFilter->localize(),
            ],
            IncludeParser::param() => AnimeSynonym::RELATION_ANIME,
        ];

        $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

        $synonym->unsetRelations()->load([
            AnimeSynonym::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter) {
                $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
            },
        ]);

        $response = $this->get(route('api.animesynonym.show', ['animesynonym' => $synonym] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new SynonymResource($synonym, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Synonym Show Endpoint shall support constrained eager loading of anime by year.
     */
    public function testAnimeByYear(): void
    {
        $yearFilter = intval($this->faker->year());
        $excludedYear = $yearFilter + 1;

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_YEAR => $yearFilter,
            ],
            IncludeParser::param() => AnimeSynonym::RELATION_ANIME,
        ];

        $synonym = AnimeSynonym::factory()
            ->for(
                Anime::factory()
                    ->state([
                        Anime::ATTRIBUTE_YEAR => $this->faker->boolean() ? $yearFilter : $excludedYear,
                    ])
            )
            ->createOne();

        $synonym->unsetRelations()->load([
            AnimeSynonym::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter) {
                $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
            },
        ]);

        $response = $this->get(route('api.animesynonym.show', ['animesynonym' => $synonym] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new SynonymResource($synonym, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
