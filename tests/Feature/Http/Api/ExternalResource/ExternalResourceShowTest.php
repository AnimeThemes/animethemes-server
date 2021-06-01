<?php declare(strict_types=1);

namespace Http\Api\ExternalResource;

use App\Enums\AnimeSeason;
use App\Http\Resources\ExternalResourceResource;
use App\JsonApi\QueryParser;
use App\Models\Anime;
use App\Models\Artist;
use App\Models\ExternalResource;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class ExternalResourceShowTest
 * @package Http\Api\ExternalResource
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
                    ExternalResourceResource::make($resource, QueryParser::make())
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
                    ExternalResourceResource::make($resource, QueryParser::make())
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
            QueryParser::PARAM_INCLUDE => $includedPaths->join(','),
        ];

        ExternalResource::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull))
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $resource = ExternalResource::with($includedPaths->all())->first();

        $response = $this->get(route('api.resource.show', ['resource' => $resource] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ExternalResourceResource::make($resource, QueryParser::make($parameters))
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
            QueryParser::PARAM_FIELDS => [
                ExternalResourceResource::$wrap => $includedFields->join(','),
            ],
        ];

        $resource = ExternalResource::factory()->create();

        $response = $this->get(route('api.resource.show', ['resource' => $resource] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ExternalResourceResource::make($resource, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'season' => $seasonFilter->key,
            ],
            QueryParser::PARAM_INCLUDE => 'anime',
        ];

        ExternalResource::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull))
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
                    ExternalResourceResource::make($resource, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'year' => $yearFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'anime',
        ];

        ExternalResource::factory()
            ->has(
                Anime::factory()
                ->count($this->faker->randomDigitNotNull)
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
                    ExternalResourceResource::make($resource, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
