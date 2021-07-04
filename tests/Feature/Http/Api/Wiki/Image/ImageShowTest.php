<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Image;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Http\Api\QueryParser;
use App\Http\Resources\Wiki\Resource\ImageResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class ImageShowTest.
 */
class ImageShowTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    use WithoutEvents;

    /**
     * By default, the Image Show Endpoint shall return an Image Resource.
     *
     * @return void
     */
    public function testDefault()
    {
        $image = Image::factory()->create();

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
     * The Image Show Endpoint shall return an Image Image for soft deleted images.
     *
     * @return void
     */
    public function testSoftDelete()
    {
        $image = Image::factory()->createOne();

        $image->delete();

        $image->unsetRelations();

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
        $allowedPaths = collect(ImageResource::allowedIncludePaths());
        $includedPaths = $allowedPaths->random($this->faker->numberBetween(0, count($allowedPaths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $includedPaths->join(','),
        ];

        Image::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull))
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $image = Image::with($includedPaths->all())->first();

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
            'image_id',
            'created_at',
            'updated_at',
            'deleted_at',
            'path',
            'size',
            'mimetype',
            'facet',
        ]);

        $includedFields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                ImageResource::$wrap => $includedFields->join(','),
            ],
        ];

        $image = Image::factory()->create();

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
        $seasonFilter = AnimeSeason::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'season' => $seasonFilter->key,
            ],
            QueryParser::PARAM_INCLUDE => 'anime',
        ];

        Image::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $image = Image::with([
            'anime' => function (BelongsToMany $query) use ($seasonFilter) {
                $query->where('season', $seasonFilter->value);
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
        $yearFilter = intval($this->faker->year());
        $excludedYear = $yearFilter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'year' => $yearFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'anime',
        ];

        Image::factory()
            ->has(
                Anime::factory()
                ->count($this->faker->randomDigitNotNull)
                ->state([
                    'year' => $this->faker->boolean() ? $yearFilter : $excludedYear,
                ])
            )
            ->create();

        $image = Image::with([
            'anime' => function (BelongsToMany $query) use ($yearFilter) {
                $query->where('year', $yearFilter);
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
