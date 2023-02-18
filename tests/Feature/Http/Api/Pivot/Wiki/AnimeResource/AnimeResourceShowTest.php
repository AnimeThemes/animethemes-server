<?php

declare(strict_types=1);

namespace Http\Api\Pivot\Wiki\AnimeResource;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ResourceSite;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Pivot\Wiki\AnimeResourceSchema;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeResourceResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\AnimeResource;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class AnimeResourceShowTest.
 */
class AnimeResourceShowTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * The Anime Resource Show Endpoint shall return an error if the anime resource does not exist.
     *
     * @return void
     */
    public function testNotFound(): void
    {
        $anime = Anime::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $response = $this->get(route('api.animeresource.show', ['anime' => $anime, 'resource' => $resource]));

        $response->assertNotFound();
    }

    /**
     * By default, the Anime Resource Show Endpoint shall return an Anime Resource Resource.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $animeResource = AnimeResource::factory()
            ->for(Anime::factory())
            ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
            ->createOne();

        $response = $this->get(route('api.animeresource.show', ['anime' => $animeResource->anime, 'resource' => $animeResource->resource]));

        $animeResource->unsetRelations();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeResourceResource($animeResource, new Query()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Resource Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new AnimeResourceSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        $animeResource = AnimeResource::factory()
            ->for(Anime::factory())
            ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
            ->createOne();

        $response = $this->get(route('api.animeresource.show', ['anime' => $animeResource->anime, 'resource' => $animeResource->resource] + $parameters));

        $animeResource->unsetRelations()->load($includedPaths->all());

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeResourceResource($animeResource, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Resource Show Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $schema = new AnimeResourceSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                AnimeResourceResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $animeResource = AnimeResource::factory()
            ->for(Anime::factory())
            ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
            ->createOne();

        $response = $this->get(route('api.animeresource.show', ['anime' => $animeResource->anime, 'resource' => $animeResource->resource] + $parameters));

        $animeResource->unsetRelations();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeResourceResource($animeResource, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Resource Show Endpoint shall support constrained eager loading of resources by site.
     *
     * @return void
     */
    public function testResourcesBySite(): void
    {
        $siteFilter = ResourceSite::getRandomInstance();

        $parameters = [
            FilterParser::param() => [
                ExternalResource::ATTRIBUTE_SITE => $siteFilter->description,
            ],
            IncludeParser::param() => AnimeResource::RELATION_RESOURCE,
        ];

        $animeResource = AnimeResource::factory()
            ->for(Anime::factory())
            ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
            ->createOne();

        $response = $this->get(route('api.animeresource.show', ['anime' => $animeResource->anime, 'resource' => $animeResource->resource] + $parameters));

        $animeResource->unsetRelations()->load([
            AnimeResource::RELATION_RESOURCE => function (BelongsTo $query) use ($siteFilter) {
                $query->where(ExternalResource::ATTRIBUTE_SITE, $siteFilter->value);
            },
        ]);

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeResourceResource($animeResource, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Resource Show Endpoint shall support constrained eager loading of anime by season.
     *
     * @return void
     */
    public function testAnimeBySeason(): void
    {
        $seasonFilter = AnimeSeason::getRandomInstance();

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_SEASON => $seasonFilter->description,
            ],
            IncludeParser::param() => AnimeResource::RELATION_ANIME,
        ];

        $animeResource = AnimeResource::factory()
            ->for(Anime::factory())
            ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
            ->createOne();

        $response = $this->get(route('api.animeresource.show', ['anime' => $animeResource->anime, 'resource' => $animeResource->resource] + $parameters));

        $animeResource->unsetRelations()->load([
            AnimeResource::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter) {
                $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
            },
        ]);

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeResourceResource($animeResource, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Resource Show Endpoint shall support constrained eager loading of anime by year.
     *
     * @return void
     */
    public function testAnimeByYear(): void
    {
        $yearFilter = intval($this->faker->year());
        $excludedYear = $yearFilter + 1;

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_YEAR => $yearFilter,
            ],
            IncludeParser::param() => AnimeResource::RELATION_ANIME,
        ];

        $animeResource = AnimeResource::factory()
            ->for(
                Anime::factory()
                    ->state([
                        Anime::ATTRIBUTE_YEAR => $this->faker->boolean() ? $yearFilter : $excludedYear,
                    ])
            )
            ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
            ->createOne();

        $response = $this->get(route('api.animeresource.show', ['anime' => $animeResource->anime, 'resource' => $animeResource->resource] + $parameters));

        $animeResource->unsetRelations()->load([
            AnimeResource::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter) {
                $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
            },
        ]);

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeResourceResource($animeResource, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
