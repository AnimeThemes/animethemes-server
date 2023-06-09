<?php

declare(strict_types=1);

namespace Http\Api\Pivot\Wiki\StudioImage;

use App\Enums\Models\Wiki\ImageFacet;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Pivot\Wiki\StudioImageSchema;
use App\Http\Resources\Pivot\Wiki\Resource\StudioImageResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\StudioImage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

/**
 * Class StudioImageShowTest.
 */
class StudioImageShowTest extends TestCase
{
    use WithFaker;

    /**
     * The Studio Image Show Endpoint shall return an error if the studio image does not exist.
     *
     * @return void
     */
    public function testNotFound(): void
    {
        $studio = Studio::factory()->createOne();
        $image = Image::factory()->createOne();

        $response = $this->get(route('api.studioimage.show', ['studio' => $studio, 'image' => $image]));

        $response->assertNotFound();
    }

    /**
     * By default, the Studio Image Show Endpoint shall return an Studio Image Resource.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $studioImage = StudioImage::factory()
            ->for(Studio::factory())
            ->for(Image::factory())
            ->createOne();

        $response = $this->get(route('api.studioimage.show', ['studio' => $studioImage->studio, 'image' => $studioImage->image]));

        $studioImage->unsetRelations();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new StudioImageResource($studioImage, new Query()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Image Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new StudioImageSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        $studioImage = StudioImage::factory()
            ->for(Studio::factory())
            ->for(Image::factory())
            ->createOne();

        $response = $this->get(route('api.studioimage.show', ['studio' => $studioImage->studio, 'image' => $studioImage->image] + $parameters));

        $studioImage->unsetRelations()->load($includedPaths->all());

        $response->assertJson(
            json_decode(
                json_encode(
                    (new StudioImageResource($studioImage, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Image Show Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $schema = new StudioImageSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                StudioImageResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $studioImage = StudioImage::factory()
            ->for(Studio::factory())
            ->for(Image::factory())
            ->createOne();

        $response = $this->get(route('api.studioimage.show', ['studio' => $studioImage->studio, 'image' => $studioImage->image] + $parameters));

        $studioImage->unsetRelations();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new StudioImageResource($studioImage, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Image Show Endpoint shall support constrained eager loading of images by facet.
     *
     * @return void
     */
    public function testImagesByFacet(): void
    {
        $facetFilter = Arr::random(ImageFacet::cases());

        $parameters = [
            FilterParser::param() => [
                Image::ATTRIBUTE_FACET => $facetFilter->localize(),
            ],
            IncludeParser::param() => StudioImage::RELATION_IMAGE,
        ];

        $studioImage = StudioImage::factory()
            ->for(Studio::factory())
            ->for(Image::factory())
            ->createOne();

        $response = $this->get(route('api.studioimage.show', ['studio' => $studioImage->studio, 'image' => $studioImage->image] + $parameters));

        $studioImage->unsetRelations()->load([
            StudioImage::RELATION_IMAGE => function (BelongsTo $query) use ($facetFilter) {
                $query->where(Image::ATTRIBUTE_FACET, $facetFilter->value);
            },
        ]);

        $response->assertJson(
            json_decode(
                json_encode(
                    (new StudioImageResource($studioImage, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
