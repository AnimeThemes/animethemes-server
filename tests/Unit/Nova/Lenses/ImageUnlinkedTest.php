<?php

namespace Tests\Unit\Nova\Lenses;

use App\Models\Anime;
use App\Models\Artist;
use App\Models\Image;
use App\Nova\Filters\CreatedEndDateFilter;
use App\Nova\Filters\CreatedStartDateFilter;
use App\Nova\Filters\DeletedEndDateFilter;
use App\Nova\Filters\DeletedStartDateFilter;
use App\Nova\Filters\UpdatedEndDateFilter;
use App\Nova\Filters\UpdatedStartDateFilter;
use App\Nova\Lenses\ImageUnlinkedLens;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Lenses\NovaLensTest;
use Tests\TestCase;

class ImageUnlinkedTest extends TestCase
{
    use NovaLensTest, RefreshDatabase, WithFaker, WithoutEvents;

    /**
     * The Image Unlinked Lens shall contain Image Fields.
     *
     * @return void
     */
    public function testFields()
    {
        $lens = $this->novaLens(ImageUnlinkedLens::class);

        $lens->assertHasField(__('nova.id'));
        $lens->assertHasField(__('nova.facet'));
        $lens->assertHasField(__('nova.image'));
    }

    /**
     * The Image Unlinked Lens fields shall be sortable.
     *
     * @return void
     */
    public function testSortable()
    {
        $lens = $this->novaLens(ImageUnlinkedLens::class);

        $lens->field(__('nova.id'))->assertSortable();
        $lens->field(__('nova.facet'))->assertSortable();
        $lens->field(__('nova.image'))->assertNotSortable();
    }

    /**
     * The Image Unlinked Lens shall contain Image Filters.
     *
     * @return void
     */
    public function testFilters()
    {
        $lens = $this->novaLens(ImageUnlinkedLens::class);

        $lens->assertHasFilter(CreatedStartDateFilter::class);
        $lens->assertHasFilter(CreatedEndDateFilter::class);
        $lens->assertHasFilter(UpdatedStartDateFilter::class);
        $lens->assertHasFilter(UpdatedEndDateFilter::class);
        $lens->assertHasFilter(DeletedStartDateFilter::class);
        $lens->assertHasFilter(DeletedEndDateFilter::class);
    }

    /**
     * The Image Unlinked Lens shall contain no Actions.
     *
     * @return void
     */
    public function testActions()
    {
        $lens = $this->novaLens(ImageUnlinkedLens::class);

        $lens->assertHasNoActions();
    }

    /**
     * The Image Unlinked Lens shall use the 'withFilters' request.
     *
     * @return void
     */
    public function testWithFilters()
    {
        $lens = $this->novaLens(ImageUnlinkedLens::class);

        $query = $lens->query(Image::class);

        $query->assertWithFilters();
    }

    /**
     * The Image Unlinked Lens shall use the 'withOrdering' request.
     *
     * @return void
     */
    public function testWithOrdering()
    {
        $lens = $this->novaLens(ImageUnlinkedLens::class);

        $query = $lens->query(Image::class);

        $query->assertWithOrdering();
    }

    /**
     * The Image Unlinked Lens shall filter Images without Anime or Artists.
     *
     * @return void
     */
    public function testQuery()
    {
        Image::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        Image::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        Image::factory()
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        Image::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull))
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $filteredImages = Image::whereDoesntHave('anime')
            ->whereDoesntHave('artists')
            ->get();

        $lens = $this->novaLens(ImageUnlinkedLens::class);

        $query = $lens->query(Image::class);

        foreach ($filteredImages as $filteredImage) {
            $query->assertContains($filteredImage);
        }
        $query->assertCount($filteredImages->count());
    }
}
