<?php

namespace Tests\Unit\Nova\Lenses;

use App\Enums\ImageFacet;
use App\Models\Artist;
use App\Models\Image;
use App\Nova\Filters\CreatedEndDateFilter;
use App\Nova\Filters\CreatedStartDateFilter;
use App\Nova\Filters\DeletedEndDateFilter;
use App\Nova\Filters\DeletedStartDateFilter;
use App\Nova\Filters\UpdatedEndDateFilter;
use App\Nova\Filters\UpdatedStartDateFilter;
use App\Nova\Lenses\ArtistCoverLargeLens;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Lenses\NovaLensTest;
use Tests\TestCase;

class ArtistCoverLargeTest extends TestCase
{
    use NovaLensTest, RefreshDatabase, WithFaker, WithoutEvents;

    /**
     * The Artist Large Cover Lens shall contain Artist Fields.
     *
     * @return void
     */
    public function testFields()
    {
        $lens = $this->novaLens(ArtistCoverLargeLens::class);

        $lens->assertHasField(__('nova.id'));
        $lens->assertHasField(__('nova.name'));
        $lens->assertHasField(__('nova.slug'));
    }

    /**
     * The Artist Large Cover Lens fields shall be sortable.
     *
     * @return void
     */
    public function testSortable()
    {
        $lens = $this->novaLens(ArtistCoverLargeLens::class);

        $lens->field(__('nova.id'))->assertSortable();
        $lens->field(__('nova.name'))->assertSortable();
        $lens->field(__('nova.slug'))->assertSortable();
    }

    /**
     * The Artist Large Cover Lens shall contain Artist Filters.
     *
     * @return void
     */
    public function testFilters()
    {
        $lens = $this->novaLens(ArtistCoverLargeLens::class);

        $lens->assertHasFilter(CreatedStartDateFilter::class);
        $lens->assertHasFilter(CreatedEndDateFilter::class);
        $lens->assertHasFilter(UpdatedStartDateFilter::class);
        $lens->assertHasFilter(UpdatedEndDateFilter::class);
        $lens->assertHasFilter(DeletedStartDateFilter::class);
        $lens->assertHasFilter(DeletedEndDateFilter::class);
    }

    /**
     * The Artist Large Cover Lens shall contain no Actions.
     *
     * @return void
     */
    public function testActions()
    {
        $lens = $this->novaLens(ArtistCoverLargeLens::class);

        $lens->assertHasNoActions();
    }

    /**
     * The Artist Large Cover Lens shall use the 'withFilters' request.
     *
     * @return void
     */
    public function testWithFilters()
    {
        $lens = $this->novaLens(ArtistCoverLargeLens::class);

        $query = $lens->query(Artist::class);

        $query->assertWithFilters();
    }

    /**
     * The Artist Large Cover Lens shall use the 'withOrdering' request.
     *
     * @return void
     */
    public function testWithOrdering()
    {
        $lens = $this->novaLens(ArtistCoverLargeLens::class);

        $query = $lens->query(Artist::class);

        $query->assertWithOrdering();
    }

    /**
     * The Artist Large Cover Lens shall filter Artist without a Large Cover image.
     *
     * @return void
     */
    public function testQuery()
    {
        Artist::factory()
            ->has(Image::factory()->count($this->faker->randomDigitNotNull))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $filteredArtists = Artist::whereDoesntHave('images', function (Builder $imageQuery) {
            $imageQuery->where('facet', ImageFacet::COVER_LARGE);
        })
        ->get();

        $lens = $this->novaLens(ArtistCoverLargeLens::class);

        $query = $lens->query(Artist::class);

        foreach ($filteredArtists as $filteredArtist) {
            $query->assertContains($filteredArtist);
        }
        $query->assertCount($filteredArtists->count());
    }
}
