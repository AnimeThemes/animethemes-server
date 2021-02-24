<?php

namespace Tests\Feature\Http\Api\Image;

use App\Enums\AnimeSeason;
use App\Http\Resources\ImageResource;
use App\JsonApi\QueryParser;
use App\Models\Anime;
use App\Models\Artist;
use App\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ImageShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * By default, the Image Show Endpoint shall return an Image Resource with all allowed include paths.
     *
     * @return void
     */
    public function testDefault()
    {
        Image::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull))
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $image = Image::with(ImageResource::allowedIncludePaths())->first();

        $response = $this->get(route('api.image.show', ['image' => $image]));

        $response->assertJson(
            json_decode(
                json_encode(
                    ImageResource::make($image, QueryParser::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Image Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths()
    {
        $allowed_paths = collect(ImageResource::allowedIncludePaths());
        $included_paths = $allowed_paths->random($this->faker->numberBetween(0, count($allowed_paths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $included_paths->join(','),
        ];

        Image::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull))
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $image = Image::with($included_paths->all())->first();

        $response = $this->get(route('api.image.show', ['image' => $image] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ImageResource::make($image, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Image Show Endpoint shall implement sparse fieldsets.
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
        ]);

        $included_fields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                ImageResource::$resourceType => $included_fields->join(','),
            ],
        ];

        Image::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull))
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $image = Image::with(ImageResource::allowedIncludePaths())->first();

        $response = $this->get(route('api.image.show', ['image' => $image] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ImageResource::make($image, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Image Show Endpoint shall support constrained eager loading of anime by season.
     *
     * @return void
     */
    public function testAnimeBySeason()
    {
        $season_filter = AnimeSeason::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'season' => $season_filter->key,
            ],
        ];

        Image::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $image = Image::with([
            'anime' => function ($query) use ($season_filter) {
                $query->where('season', $season_filter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.image.show', ['image' => $image] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ImageResource::make($image, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Image Show Endpoint shall support constrained eager loading of anime by year.
     *
     * @return void
     */
    public function testAnimeByYear()
    {
        $year_filter = intval($this->faker->year());
        $excluded_year = $year_filter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'year' => $year_filter,
            ],
        ];

        Image::factory()
            ->has(
                Anime::factory()
                ->count($this->faker->randomDigitNotNull)
                ->state([
                    'year' => $this->faker->boolean() ? $year_filter : $excluded_year,
                ])
            )
            ->create();

        $image = Image::with([
            'anime' => function ($query) use ($year_filter) {
                $query->where('year', $year_filter);
            },
        ])
        ->first();

        $response = $this->get(route('api.image.show', ['image' => $image] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ImageResource::make($image, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
