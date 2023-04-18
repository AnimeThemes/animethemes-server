<?php

declare(strict_types=1);

namespace Http\Api\Pivot\Wiki\AnimeImage;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ImageFacet;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Pivot\Wiki\AnimeImageSchema;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeImageResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Image;
use App\Pivots\Wiki\AnimeImage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class AnimeImageShowTest.
 */
class AnimeImageShowTest extends TestCase
{
    use WithFaker;

    /**
     * The Anime Image Show Endpoint shall return an error if the anime image does not exist.
     *
     * @return void
     */
    public function testNotFound(): void
    {
        $anime = Anime::factory()->createOne();
        $image = Image::factory()->createOne();

        $response = $this->get(route('api.animeimage.show', ['anime' => $anime, 'image' => $image]));

        $response->assertNotFound();
    }

    /**
     * By default, the Anime Image Show Endpoint shall return an Anime Image Resource.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $animeImage = AnimeImage::factory()
            ->for(Anime::factory())
            ->for(Image::factory())
            ->createOne();

        $response = $this->get(route('api.animeimage.show', ['anime' => $animeImage->anime, 'image' => $animeImage->image]));

        $animeImage->unsetRelations();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeImageResource($animeImage, new Query()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Image Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new AnimeImageSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        $animeImage = AnimeImage::factory()
            ->for(Anime::factory())
            ->for(Image::factory())
            ->createOne();

        $response = $this->get(route('api.animeimage.show', ['anime' => $animeImage->anime, 'image' => $animeImage->image] + $parameters));

        $animeImage->unsetRelations()->load($includedPaths->all());

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeImageResource($animeImage, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Image Show Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $schema = new AnimeImageSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                AnimeImageResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $animeImage = AnimeImage::factory()
            ->for(Anime::factory())
            ->for(Image::factory())
            ->createOne();

        $response = $this->get(route('api.animeimage.show', ['anime' => $animeImage->anime, 'image' => $animeImage->image] + $parameters));

        $animeImage->unsetRelations();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeImageResource($animeImage, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Image Show Endpoint shall support constrained eager loading of images by facet.
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
            IncludeParser::param() => AnimeImage::RELATION_IMAGE,
        ];

        $animeImage = AnimeImage::factory()
            ->for(Anime::factory())
            ->for(Image::factory())
            ->createOne();

        $response = $this->get(route('api.animeimage.show', ['anime' => $animeImage->anime, 'image' => $animeImage->image] + $parameters));

        $animeImage->unsetRelations()->load([
            AnimeImage::RELATION_IMAGE => function (BelongsTo $query) use ($facetFilter) {
                $query->where(Image::ATTRIBUTE_FACET, $facetFilter->value);
            },
        ]);

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeImageResource($animeImage, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Image Show Endpoint shall support constrained eager loading of anime by season.
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
            IncludeParser::param() => AnimeImage::RELATION_ANIME,
        ];

        $animeImage = AnimeImage::factory()
            ->for(Anime::factory())
            ->for(Image::factory())
            ->createOne();

        $response = $this->get(route('api.animeimage.show', ['anime' => $animeImage->anime, 'image' => $animeImage->image] + $parameters));

        $animeImage->unsetRelations()->load([
            AnimeImage::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter) {
                $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
            },
        ]);

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeImageResource($animeImage, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Image Show Endpoint shall support constrained eager loading of anime by year.
     *
     * @return void
     */
    public function testAnimeByYear(): void
    {
        $yearFilter = intval($this->faker->year());
        $excludedYear = $yearFilter + 1;

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_YEAR => $yearFilter,
            ],
            IncludeParser::param() => AnimeImage::RELATION_ANIME,
        ];

        $animeImage = AnimeImage::factory()
            ->for(
                Anime::factory()
                    ->state([
                        Anime::ATTRIBUTE_YEAR => $this->faker->boolean() ? $yearFilter : $excludedYear,
                    ])
            )
            ->for(Image::factory())
            ->createOne();

        $response = $this->get(route('api.animeimage.show', ['anime' => $animeImage->anime, 'image' => $animeImage->image] + $parameters));

        $animeImage->unsetRelations()->load([
            AnimeImage::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter) {
                $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
            },
        ]);

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeImageResource($animeImage, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
