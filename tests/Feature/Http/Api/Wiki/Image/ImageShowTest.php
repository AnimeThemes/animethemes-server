<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Image;

use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Wiki\ImageSchema;
use App\Http\Resources\Wiki\Resource\ImageResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

/**
 * Class ImageShowTest.
 */
class ImageShowTest extends TestCase
{
    use WithFaker;

    /**
     * By default, the Image Show Endpoint shall return an Image Resource.
     *
     * @return void
     */
    public function test_default(): void
    {
        $image = Image::factory()->create();

        $response = $this->get(route('api.image.show', ['image' => $image]));

        $response->assertJson(
            json_decode(
                json_encode(
                    new ImageResource($image, new Query())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Image Show Endpoint shall return an Image Resource for soft deleted images.
     *
     * @return void
     */
    public function test_soft_delete(): void
    {
        $image = Image::factory()->trashed()->createOne();

        $image->unsetRelations();

        $response = $this->get(route('api.image.show', ['image' => $image]));

        $response->assertJson(
            json_decode(
                json_encode(
                    new ImageResource($image, new Query())
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
    public function test_allowed_include_paths(): void
    {
        $schema = new ImageSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        $image = Image::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull()))
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull()))
            ->createOne();

        $response = $this->get(route('api.image.show', ['image' => $image] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new ImageResource($image, new Query($parameters))
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
    public function test_sparse_fieldsets(): void
    {
        $schema = new ImageSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                ImageResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $image = Image::factory()->create();

        $response = $this->get(route('api.image.show', ['image' => $image] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new ImageResource($image, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Image Show Endpoint shall support constrained eager loading of anime by media format.
     *
     * @return void
     */
    public function test_anime_by_media_format(): void
    {
        $mediaFormatFilter = Arr::random(AnimeMediaFormat::cases());

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_MEDIA_FORMAT => $mediaFormatFilter->localize(),
            ],
            IncludeParser::param() => Image::RELATION_ANIME,
        ];

        $image = Image::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull()))
            ->createOne();

        $image->unsetRelations()->load([
            Image::RELATION_ANIME => function (BelongsToMany $query) use ($mediaFormatFilter) {
                $query->where(Anime::ATTRIBUTE_MEDIA_FORMAT, $mediaFormatFilter->value);
            },
        ]);

        $response = $this->get(route('api.image.show', ['image' => $image] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new ImageResource($image, new Query($parameters))
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
    public function test_anime_by_season(): void
    {
        $seasonFilter = Arr::random(AnimeSeason::cases());

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_SEASON => $seasonFilter->localize(),
            ],
            IncludeParser::param() => Image::RELATION_ANIME,
        ];

        $image = Image::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull()))
            ->createOne();

        $image->unsetRelations()->load([
            Image::RELATION_ANIME => function (BelongsToMany $query) use ($seasonFilter) {
                $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
            },
        ]);

        $response = $this->get(route('api.image.show', ['image' => $image] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new ImageResource($image, new Query($parameters))
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
    public function test_anime_by_year(): void
    {
        $yearFilter = intval($this->faker->year());
        $excludedYear = $yearFilter + 1;

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_YEAR => $yearFilter,
            ],
            IncludeParser::param() => Image::RELATION_ANIME,
        ];

        $image = Image::factory()
            ->has(
                Anime::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->state([
                        Anime::ATTRIBUTE_YEAR => $this->faker->boolean() ? $yearFilter : $excludedYear,
                    ])
            )
            ->createOne();

        $image->unsetRelations()->load([
            Image::RELATION_ANIME => function (BelongsToMany $query) use ($yearFilter) {
                $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
            },
        ]);

        $response = $this->get(route('api.image.show', ['image' => $image] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new ImageResource($image, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
