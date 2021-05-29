<?php

namespace Tests\Unit\Nova\Filters;

use App\Enums\ImageFacet;
use App\Models\Image;
use App\Nova\Filters\ImageFacetFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

class ImageFacetTest extends TestCase
{
    use NovaFilterTest, RefreshDatabase, WithFaker, WithoutEvents;

    /**
     * The Image Facet Filter shall be a select filter.
     *
     * @return void
     */
    public function testSelectFilter()
    {
        $this->novaFilter(ImageFacetFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The Image Facet Filter shall have an option for each ImageFacet instance.
     *
     * @return void
     */
    public function testOptions()
    {
        $filter = $this->novaFilter(ImageFacetFilter::class);

        foreach (ImageFacet::getInstances() as $facet) {
            $filter->assertHasOption($facet->description);
        }
    }

    /**
     * The Image Facet Filter shall filter Images By Facet.
     *
     * @return void
     */
    public function testFilter()
    {
        $facet = ImageFacet::getRandomInstance();

        Image::factory()->count($this->faker->randomDigitNotNull)->create();

        $filter = $this->novaFilter(ImageFacetFilter::class);

        $response = $filter->apply(Image::class, $facet->value);

        $filteredImages = Image::where('facet', $facet->value)->get();
        foreach ($filteredImages as $filteredImage) {
            $response->assertContains($filteredImage);
        }
        $response->assertCount($filteredImages->count());
    }
}
