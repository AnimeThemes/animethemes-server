<?php

declare(strict_types=1);

namespace Http\Api\Pivot\Wiki\SongResource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Pivot\Wiki\SongResourceSchema;
use App\Http\Resources\Pivot\Wiki\Resource\SongResourceResource;
use App\Models\Wiki\Song;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\SongResource;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

/**
 * Class SongResourceShowTest.
 */
class SongResourceShowTest extends TestCase
{
    use WithFaker;

    /**
     * The Song Resource Show Endpoint shall return an error if the song resource does not exist.
     *
     * @return void
     */
    public function testNotFound(): void
    {
        $song = Song::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $response = $this->get(route('api.songresource.show', ['song' => $song, 'resource' => $resource]));

        $response->assertNotFound();
    }

    /**
     * By default, the Song Resource Show Endpoint shall return an Song Resource Resource.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $songResource = SongResource::factory()
            ->for(Song::factory())
            ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
            ->createOne();

        $response = $this->get(route('api.songresource.show', ['song' => $songResource->song, 'resource' => $songResource->resource]));

        $songResource->unsetRelations();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new SongResourceResource($songResource, new Query()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Song Resource Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new SongResourceSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        $songResource = SongResource::factory()
            ->for(Song::factory())
            ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
            ->createOne();

        $response = $this->get(route('api.songresource.show', ['song' => $songResource->song, 'resource' => $songResource->resource] + $parameters));

        $songResource->unsetRelations()->load($includedPaths->all());

        $response->assertJson(
            json_decode(
                json_encode(
                    (new SongResourceResource($songResource, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Song Resource Show Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $schema = new SongResourceSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                SongResourceResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $songResource = SongResource::factory()
            ->for(Song::factory())
            ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
            ->createOne();

        $response = $this->get(route('api.songresource.show', ['song' => $songResource->song, 'resource' => $songResource->resource] + $parameters));

        $songResource->unsetRelations();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new SongResourceResource($songResource, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Song Resource Show Endpoint shall support constrained eager loading of resources by site.
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
            IncludeParser::param() => SongResource::RELATION_RESOURCE,
        ];

        $songResource = SongResource::factory()
            ->for(Song::factory())
            ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
            ->createOne();

        $response = $this->get(route('api.songresource.show', ['song' => $songResource->song, 'resource' => $songResource->resource] + $parameters));

        $songResource->unsetRelations()->load([
            SongResource::RELATION_RESOURCE => function (BelongsTo $query) use ($siteFilter) {
                $query->where(ExternalResource::ATTRIBUTE_SITE, $siteFilter->value);
            },
        ]);

        $response->assertJson(
            json_decode(
                json_encode(
                    (new SongResourceResource($songResource, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
