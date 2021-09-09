<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Studio;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query;
use App\Http\Resources\Wiki\Resource\StudioResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Studio;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class StudioShowTest.
 */
class StudioShowTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * By default, the Studio Show Endpoint shall return a Studio Resource.
     *
     * @return void
     */
    public function testDefault()
    {
        $this->withoutEvents();

        $studio = Studio::factory()->create();

        $response = $this->get(route('api.studio.show', ['studio' => $studio]));

        $response->assertJson(
            json_decode(
                json_encode(
                    StudioResource::make($studio, Query::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Show Endpoint shall return an Studio Studio for soft deleted studios.
     *
     * @return void
     */
    public function testSoftDelete()
    {
        $this->withoutEvents();

        $studio = Studio::factory()->createOne();

        $studio->delete();

        $studio->unsetRelations();

        $response = $this->get(route('api.studio.show', ['studio' => $studio]));

        $response->assertJson(
            json_decode(
                json_encode(
                    StudioResource::make($studio, Query::make())
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
    public function testAllowedIncludePaths()
    {
        $allowedPaths = collect(StudioResource::allowedIncludePaths());
        $includedPaths = $allowedPaths->random($this->faker->numberBetween(1, count($allowedPaths)));

        $parameters = [
            IncludeParser::$param => $includedPaths->join(','),
        ];

        Studio::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull()))
            ->create();

        $studio = Studio::with($includedPaths->all())->first();

        $response = $this->get(route('api.studio.show', ['studio' => $studio] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    StudioResource::make($studio, Query::make($parameters))
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
    public function testSparseFieldsets()
    {
        $this->withoutEvents();

        $fields = collect([
            'id',
            'name',
            'slug',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);

        $includedFields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            FieldParser::$param => [
                StudioResource::$wrap => $includedFields->join(','),
            ],
        ];

        $studio = Studio::factory()->create();

        $response = $this->get(route('api.studio.show', ['studio' => $studio] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    StudioResource::make($studio, Query::make($parameters))
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
    public function testAnimeBySeason()
    {
        $this->withoutEvents();

        $seasonFilter = AnimeSeason::getRandomInstance();

        $parameters = [
            FilterParser::$param => [
                'season' => $seasonFilter->description,
            ],
            IncludeParser::$param => 'anime',
        ];

        Studio::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull()))
            ->create();

        $studio = Studio::with([
            'anime' => function (BelongsToMany $query) use ($seasonFilter) {
                $query->where('season', $seasonFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.studio.show', ['studio' => $studio] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    StudioResource::make($studio, Query::make($parameters))
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
    public function testAnimeByYear()
    {
        $this->withoutEvents();

        $yearFilter = $this->faker->numberBetween(2000, 2002);

        $parameters = [
            FilterParser::$param => [
                'year' => $yearFilter,
            ],
            IncludeParser::$param => 'anime',
        ];

        Studio::factory()
            ->has(
                Anime::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->state(new Sequence(
                        ['year' => 2000],
                        ['year' => 2001],
                        ['year' => 2002],
                    ))
            )
            ->create();

        $studio = Studio::with([
            'anime' => function (BelongsToMany $query) use ($yearFilter) {
                $query->where('year', $yearFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.studio.show', ['studio' => $studio] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    StudioResource::make($studio, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
