<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Pivot\Wiki\StudioResource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Pivot\Wiki\StudioResourceSchema;
use App\Http\Resources\Pivot\Wiki\Resource\StudioResourceResource;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\StudioResource;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

/**
 * Class StudioResourceShowTest.
 */
class StudioResourceShowTest extends TestCase
{
    use WithFaker;

    /**
     * The Studio Resource Show Endpoint shall return an error if the studio resource does not exist.
     *
     * @return void
     */
    public function test_not_found(): void
    {
        $studio = Studio::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $response = $this->get(route('api.studioresource.show', ['studio' => $studio, 'resource' => $resource]));

        $response->assertNotFound();
    }

    /**
     * By default, the Studio Resource Show Endpoint shall return a Studio Resource Resource.
     *
     * @return void
     */
    public function test_default(): void
    {
        $studioResource = StudioResource::factory()
            ->for(Studio::factory())
            ->for(ExternalResource::factory(), StudioResource::RELATION_RESOURCE)
            ->createOne();

        $response = $this->get(route('api.studioresource.show', ['studio' => $studioResource->studio, 'resource' => $studioResource->resource]));

        $studioResource->unsetRelations();

        $response->assertJson(
            json_decode(
                json_encode(
                    new StudioResourceResource($studioResource, new Query())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Resource Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function test_allowed_include_paths(): void
    {
        $schema = new StudioResourceSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        $studioResource = StudioResource::factory()
            ->for(Studio::factory())
            ->for(ExternalResource::factory(), StudioResource::RELATION_RESOURCE)
            ->createOne();

        $response = $this->get(route('api.studioresource.show', ['studio' => $studioResource->studio, 'resource' => $studioResource->resource] + $parameters));

        $studioResource->unsetRelations()->load($includedPaths->all());

        $response->assertJson(
            json_decode(
                json_encode(
                    new StudioResourceResource($studioResource, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Resource Show Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function test_sparse_fieldsets(): void
    {
        $schema = new StudioResourceSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                StudioResourceResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $studioResource = StudioResource::factory()
            ->for(Studio::factory())
            ->for(ExternalResource::factory(), StudioResource::RELATION_RESOURCE)
            ->createOne();

        $response = $this->get(route('api.studioresource.show', ['studio' => $studioResource->studio, 'resource' => $studioResource->resource] + $parameters));

        $studioResource->unsetRelations();

        $response->assertJson(
            json_decode(
                json_encode(
                    new StudioResourceResource($studioResource, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Resource Show Endpoint shall support constrained eager loading of resources by site.
     *
     * @return void
     */
    public function test_resources_by_site(): void
    {
        $siteFilter = Arr::random(ResourceSite::cases());

        $parameters = [
            FilterParser::param() => [
                ExternalResource::ATTRIBUTE_SITE => $siteFilter->localize(),
            ],
            IncludeParser::param() => StudioResource::RELATION_RESOURCE,
        ];

        $studioResource = StudioResource::factory()
            ->for(Studio::factory())
            ->for(ExternalResource::factory(), StudioResource::RELATION_RESOURCE)
            ->createOne();

        $response = $this->get(route('api.studioresource.show', ['studio' => $studioResource->studio, 'resource' => $studioResource->resource] + $parameters));

        $studioResource->unsetRelations()->load([
            StudioResource::RELATION_RESOURCE => function (BelongsTo $query) use ($siteFilter) {
                $query->where(ExternalResource::ATTRIBUTE_SITE, $siteFilter->value);
            },
        ]);

        $response->assertJson(
            json_decode(
                json_encode(
                    new StudioResourceResource($studioResource, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
