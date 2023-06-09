<?php

declare(strict_types=1);

namespace Http\Api\Pivot\Wiki\ArtistResource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Pivot\Wiki\ArtistResourceSchema;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistResourceResource;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\ArtistResource;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

/**
 * Class ArtistResourceShowTest.
 */
class ArtistResourceShowTest extends TestCase
{
    use WithFaker;

    /**
     * The Artist Resource Show Endpoint shall return an error if the artist resource does not exist.
     *
     * @return void
     */
    public function testNotFound(): void
    {
        $artist = Artist::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $response = $this->get(route('api.artistresource.show', ['artist' => $artist, 'resource' => $resource]));

        $response->assertNotFound();
    }

    /**
     * By default, the Artist Resource Show Endpoint shall return an Artist Resource Resource.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $artistResource = ArtistResource::factory()
            ->for(Artist::factory())
            ->for(ExternalResource::factory(), ArtistResource::RELATION_RESOURCE)
            ->createOne();

        $response = $this->get(route('api.artistresource.show', ['artist' => $artistResource->artist, 'resource' => $artistResource->resource]));

        $artistResource->unsetRelations();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ArtistResourceResource($artistResource, new Query()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Resource Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new ArtistResourceSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        $artistResource = ArtistResource::factory()
            ->for(Artist::factory())
            ->for(ExternalResource::factory(), ArtistResource::RELATION_RESOURCE)
            ->createOne();

        $response = $this->get(route('api.artistresource.show', ['artist' => $artistResource->artist, 'resource' => $artistResource->resource] + $parameters));

        $artistResource->unsetRelations()->load($includedPaths->all());

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ArtistResourceResource($artistResource, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Resource Show Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $schema = new ArtistResourceSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                ArtistResourceResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $artistResource = ArtistResource::factory()
            ->for(Artist::factory())
            ->for(ExternalResource::factory(), ArtistResource::RELATION_RESOURCE)
            ->createOne();

        $response = $this->get(route('api.artistresource.show', ['artist' => $artistResource->artist, 'resource' => $artistResource->resource] + $parameters));

        $artistResource->unsetRelations();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ArtistResourceResource($artistResource, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Resource Show Endpoint shall support constrained eager loading of resources by site.
     *
     * @return void
     */
    public function testResourcesBySite(): void
    {
        $siteFilter = Arr::random(ResourceSite::cases());

        $parameters = [
            FilterParser::param() => [
                ExternalResource::ATTRIBUTE_SITE => $siteFilter->localize(),
            ],
            IncludeParser::param() => ArtistResource::RELATION_RESOURCE,
        ];

        $artistResource = ArtistResource::factory()
            ->for(Artist::factory())
            ->for(ExternalResource::factory(), ArtistResource::RELATION_RESOURCE)
            ->createOne();

        $response = $this->get(route('api.artistresource.show', ['artist' => $artistResource->artist, 'resource' => $artistResource->resource] + $parameters));

        $artistResource->unsetRelations()->load([
            ArtistResource::RELATION_RESOURCE => function (BelongsTo $query) use ($siteFilter) {
                $query->where(ExternalResource::ATTRIBUTE_SITE, $siteFilter->value);
            },
        ]);

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ArtistResourceResource($artistResource, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
