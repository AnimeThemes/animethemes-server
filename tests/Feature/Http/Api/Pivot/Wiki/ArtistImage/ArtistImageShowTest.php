<?php

declare(strict_types=1);

namespace Http\Api\Pivot\Wiki\ArtistImage;

use App\Enums\Models\Wiki\ImageFacet;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Pivot\Wiki\ArtistImageSchema;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistImageResource;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use App\Pivots\Wiki\ArtistImage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class ArtistImageShowTest.
 */
class ArtistImageShowTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * The Artist Image Show Endpoint shall return an error if the artist image does not exist.
     *
     * @return void
     */
    public function testNotFound(): void
    {
        $artist = Artist::factory()->createOne();
        $image = Image::factory()->createOne();

        $response = $this->get(route('api.artistimage.show', ['artist' => $artist, 'image' => $image]));

        $response->assertNotFound();
    }

    /**
     * By default, the Artist Image Show Endpoint shall return an Artist Image Resource.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $artistImage = ArtistImage::factory()
            ->for(Artist::factory())
            ->for(Image::factory())
            ->createOne();

        $response = $this->get(route('api.artistimage.show', ['artist' => $artistImage->artist, 'image' => $artistImage->image]));

        $artistImage->unsetRelations();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ArtistImageResource($artistImage, new Query()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Image Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new ArtistImageSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        $artistImage = ArtistImage::factory()
            ->for(Artist::factory())
            ->for(Image::factory())
            ->createOne();

        $response = $this->get(route('api.artistimage.show', ['artist' => $artistImage->artist, 'image' => $artistImage->image] + $parameters));

        $artistImage->unsetRelations()->load($includedPaths->all());

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ArtistImageResource($artistImage, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Image Show Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $schema = new ArtistImageSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                ArtistImageResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $artistImage = ArtistImage::factory()
            ->for(Artist::factory())
            ->for(Image::factory())
            ->createOne();

        $response = $this->get(route('api.artistimage.show', ['artist' => $artistImage->artist, 'image' => $artistImage->image] + $parameters));

        $artistImage->unsetRelations();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ArtistImageResource($artistImage, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Image Show Endpoint shall support constrained eager loading of images by facet.
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
            IncludeParser::param() => ArtistImage::RELATION_IMAGE,
        ];

        $artistImage = ArtistImage::factory()
            ->for(Artist::factory())
            ->for(Image::factory())
            ->createOne();

        $response = $this->get(route('api.artistimage.show', ['artist' => $artistImage->artist, 'image' => $artistImage->image] + $parameters));

        $artistImage->unsetRelations()->load([
            ArtistImage::RELATION_IMAGE => function (BelongsTo $query) use ($facetFilter) {
                $query->where(Image::ATTRIBUTE_FACET, $facetFilter->value);
            },
        ]);

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ArtistImageResource($artistImage, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
