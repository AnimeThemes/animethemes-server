<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Studio;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ResourceSite;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Wiki\Studio\StudioReadQuery;
use App\Http\Api\Schema\Wiki\StudioSchema;
use App\Http\Resources\Wiki\Resource\StudioResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class StudioShowTest.
 */
class StudioShowTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * By default, the Studio Show Endpoint shall return a Studio Resource.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $studio = Studio::factory()->create();

        $response = $this->get(route('api.studio.show', ['studio' => $studio]));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new StudioResource($studio, new StudioReadQuery()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Show Endpoint shall return a Studio Resource for soft deleted studios.
     *
     * @return void
     */
    public function testSoftDelete(): void
    {
        $studio = Studio::factory()->createOne();

        $studio->delete();

        $studio->unsetRelations();

        $response = $this->get(route('api.studio.show', ['studio' => $studio]));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new StudioResource($studio, new StudioReadQuery()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new StudioSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        $studio = Studio::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull()))
            ->createOne();

        $response = $this->get(route('api.studio.show', ['studio' => $studio] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new StudioResource($studio, new StudioReadQuery($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Show Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $schema = new StudioSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                StudioResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $studio = Studio::factory()->create();

        $response = $this->get(route('api.studio.show', ['studio' => $studio] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new StudioResource($studio, new StudioReadQuery($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Show Endpoint shall support constrained eager loading of anime by season.
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
            IncludeParser::param() => Studio::RELATION_ANIME,
        ];

        $studio = Studio::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull()))
            ->create();

        $studio->unsetRelations()->load([
            Studio::RELATION_ANIME => function (BelongsToMany $query) use ($seasonFilter) {
                $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
            },
        ]);

        $response = $this->get(route('api.studio.show', ['studio' => $studio] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new StudioResource($studio, new StudioReadQuery($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Index Endpoint shall support constrained eager loading of anime by year.
     *
     * @return void
     */
    public function testAnimeByYear(): void
    {
        $yearFilter = $this->faker->numberBetween(2000, 2002);

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_YEAR => $yearFilter,
            ],
            IncludeParser::param() => Studio::RELATION_ANIME,
        ];

        $studio = Studio::factory()
            ->has(
                Anime::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->state(new Sequence(
                        [Anime::ATTRIBUTE_YEAR => 2000],
                        [Anime::ATTRIBUTE_YEAR => 2001],
                        [Anime::ATTRIBUTE_YEAR => 2002],
                    ))
            )
            ->createOne();

        $studio->unsetRelations()->load([
            Studio::RELATION_ANIME => function (BelongsToMany $query) use ($yearFilter) {
                $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
            },
        ]);

        $response = $this->get(route('api.studio.show', ['studio' => $studio] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new StudioResource($studio, new StudioReadQuery($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Show Endpoint shall support constrained eager loading of resources by site.
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
            IncludeParser::param() => Studio::RELATION_RESOURCES,
        ];

        $studio = Studio::factory()
            ->has(ExternalResource::factory()->count($this->faker->randomDigitNotNull()), Studio::RELATION_RESOURCES)
            ->createOne();

        $studio->unsetRelations()->load([
            Studio::RELATION_RESOURCES => function (BelongsToMany $query) use ($siteFilter) {
                $query->where(ExternalResource::ATTRIBUTE_SITE, $siteFilter->value);
            },
        ]);

        $response = $this->get(route('api.studio.show', ['studio' => $studio] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new StudioResource($studio, new StudioReadQuery($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Show Endpoint shall support constrained eager loading of images by facet.
     *
     * @return void
     */
    public function testImagesByFacet(): void
    {
        $facetFilter = ImageFacet::getRandomInstance();

        $parameters = [
            FilterParser::param() => [
                Image::ATTRIBUTE_FACET => $facetFilter->description,
            ],
            IncludeParser::param() => Studio::RELATION_IMAGES,
        ];

        $studio = Studio::factory()
            ->has(Image::factory()->count($this->faker->randomDigitNotNull()))
            ->createOne();

        $studio->unsetRelations()->load([
            Studio::RELATION_IMAGES => function (BelongsToMany $query) use ($facetFilter) {
                $query->where(Image::ATTRIBUTE_FACET, $facetFilter->value);
            },
        ]);

        $response = $this->get(route('api.studio.show', ['studio' => $studio] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new StudioResource($studio, new StudioReadQuery($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
