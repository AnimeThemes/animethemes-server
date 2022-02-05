<?php

declare(strict_types=1);

namespace Tests\Unit\Nova\Filters\Wiki\Image;

use App\Enums\Models\Wiki\ImageFacet;
use App\Models\Wiki\Image;
use App\Nova\Filters\Wiki\Image\ImageFacetFilter;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Exceptions\InvalidModelException;
use JoshGaber\NovaUnit\Filters\InvalidNovaFilterException;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

/**
 * Class ImageFacetTest.
 */
class ImageFacetTest extends TestCase
{
    use NovaFilterTest;
    use WithFaker;
    use WithoutEvents;

    /**
     * The Image Facet Filter shall be a select filter.
     *
     * @return void
     *
     * @throws InvalidNovaFilterException
     */
    public function testSelectFilter(): void
    {
        static::novaFilter(ImageFacetFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The Image Facet Filter shall have an option for each ImageFacet instance.
     *
     * @return void
     *
     * @throws InvalidNovaFilterException
     */
    public function testOptions(): void
    {
        $filter = static::novaFilter(ImageFacetFilter::class);

        foreach (ImageFacet::getInstances() as $facet) {
            $filter->assertHasOption($facet->description);
        }
    }

    /**
     * The Image Facet Filter shall filter Images By Facet.
     *
     * @return void
     *
     * @throws InvalidModelException
     * @throws InvalidNovaFilterException
     */
    public function testFilter(): void
    {
        $facet = ImageFacet::getRandomInstance();

        Image::factory()->count($this->faker->randomDigitNotNull())->create();

        $filter = static::novaFilter(ImageFacetFilter::class);

        $response = $filter->apply(Image::class, $facet->value);

        $filteredImages = Image::query()->where(Image::ATTRIBUTE_FACET, $facet->value)->get();
        foreach ($filteredImages as $filteredImage) {
            $response->assertContains($filteredImage);
        }
        $response->assertCount($filteredImages->count());
    }
}
