<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\ExternalResource;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query;
use App\Http\Resources\Wiki\Resource\ExternalResourceResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class ExternalResourceShowTest.
 */
class ExternalResourceShowTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    use WithoutEvents;

    /**
     * By default, the Resource Show Endpoint shall return an ExternalResource Resource.
     *
     * @return void
     */
    public function testDefault()
    {
        $resource = ExternalResource::factory()->create();

        $response = $this->get(route('api.resource.show', ['resource' => $resource]));

        $response->assertJson(
            json_decode(
                json_encode(
                    ExternalResourceResource::make($resource, Query::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Resource Show Endpoint shall return an Resource Resource for soft deleted images.
     *
     * @return void
     */
    public function testSoftDelete()
    {
        $resource = ExternalResource::factory()->createOne();

        $resource->delete();

        $resource->unsetRelations();

        $response = $this->get(route('api.resource.show', ['resource' => $resource]));

        $response->assertJson(
            json_decode(
                json_encode(
                    ExternalResourceResource::make($resource, Query::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Resource Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths()
    {
        $allowedPaths = collect(ExternalResourceResource::allowedIncludePaths());
        $includedPaths = $allowedPaths->random($this->faker->numberBetween(0, count($allowedPaths)));

        $parameters = [
            IncludeParser::$param => $includedPaths->join(','),
        ];

        ExternalResource::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull()))
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull()))
            ->create();

        $resource = ExternalResource::with($includedPaths->all())->first();

        $response = $this->get(route('api.resource.show', ['resource' => $resource] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ExternalResourceResource::make($resource, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Resource Show Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets()
    {
        $fields = collect([
            'id',
            'link',
            'external_id',
            'site',
            'as',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);

        $includedFields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            FieldParser::$param => [
                ExternalResourceResource::$wrap => $includedFields->join(','),
            ],
        ];

        $resource = ExternalResource::factory()->create();

        $response = $this->get(route('api.resource.show', ['resource' => $resource] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ExternalResourceResource::make($resource, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Resource Show Endpoint shall support constrained eager loading of anime by season.
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

        ExternalResource::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull()))
            ->create();

        $resource = ExternalResource::with([
            'anime' => function (BelongsToMany $query) use ($seasonFilter) {
                $query->where('season', $seasonFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.resource.show', ['resource' => $resource] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ExternalResourceResource::make($resource, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Resource Show Endpoint shall support constrained eager loading of anime by year.
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

        ExternalResource::factory()
            ->has(
                Anime::factory()
                ->count($this->faker->randomDigitNotNull())
                ->state([
                    'year' => $this->faker->boolean() ? $yearFilter : $excludedYear,
                ])
            )
            ->create();

        $resource = ExternalResource::with([
            'anime' => function (BelongsToMany $query) use ($yearFilter) {
                $query->where('year', $yearFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.resource.show', ['resource' => $resource] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ExternalResourceResource::make($resource, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
