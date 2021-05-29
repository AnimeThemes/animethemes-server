<?php

namespace Tests\Unit\Nova\Lenses;

use App\Enums\ResourceSite;
use App\Models\Artist;
use App\Models\ExternalResource;
use App\Nova\Filters\CreatedEndDateFilter;
use App\Nova\Filters\CreatedStartDateFilter;
use App\Nova\Filters\DeletedEndDateFilter;
use App\Nova\Filters\DeletedStartDateFilter;
use App\Nova\Filters\UpdatedEndDateFilter;
use App\Nova\Filters\UpdatedStartDateFilter;
use App\Nova\Lenses\ArtistAnnResourceLens;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Lenses\NovaLensTest;
use Tests\TestCase;

class ArtistAnnResourceTest extends TestCase
{
    use NovaLensTest, RefreshDatabase, WithFaker, WithoutEvents;

    /**
     * The Artist Ann Resource Lens shall contain Artist Fields.
     *
     * @return void
     */
    public function testFields()
    {
        $lens = $this->novaLens(ArtistAnnResourceLens::class);

        $lens->assertHasField(__('nova.id'));
        $lens->assertHasField(__('nova.name'));
        $lens->assertHasField(__('nova.slug'));
    }

    /**
     * The Artist Ann Resource Lens fields shall be sortable.
     *
     * @return void
     */
    public function testSortable()
    {
        $lens = $this->novaLens(ArtistAnnResourceLens::class);

        $lens->field(__('nova.id'))->assertSortable();
        $lens->field(__('nova.name'))->assertSortable();
        $lens->field(__('nova.slug'))->assertSortable();
    }

    /**
     * The Artist Ann Resource Lens shall contain Artist Filters.
     *
     * @return void
     */
    public function testFilters()
    {
        $lens = $this->novaLens(ArtistAnnResourceLens::class);

        $lens->assertHasFilter(CreatedStartDateFilter::class);
        $lens->assertHasFilter(CreatedEndDateFilter::class);
        $lens->assertHasFilter(UpdatedStartDateFilter::class);
        $lens->assertHasFilter(UpdatedEndDateFilter::class);
        $lens->assertHasFilter(DeletedStartDateFilter::class);
        $lens->assertHasFilter(DeletedEndDateFilter::class);
    }

    // TODO: testActions()

    /**
     * The Artist Ann Resource Lens shall use the 'withFilters' request.
     *
     * @return void
     */
    public function testWithFilters()
    {
        $lens = $this->novaLens(ArtistAnnResourceLens::class);

        $query = $lens->query(Artist::class);

        $query->assertWithFilters();
    }

    /**
     * The Artist Ann Resource Lens shall use the 'withOrdering' request.
     *
     * @return void
     */
    public function testWithOrdering()
    {
        $lens = $this->novaLens(ArtistAnnResourceLens::class);

        $query = $lens->query(Artist::class);

        $query->assertWithOrdering();
    }

    /**
     * The Artist Ann Resource Lens shall filter Artist without an ANN Resource.
     *
     * @return void
     */
    public function testQuery()
    {
        Artist::factory()
            ->has(ExternalResource::factory()->count($this->faker->randomDigitNotNull))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $filteredArtists = Artist::whereDoesntHave('externalResources', function (Builder $resourceQuery) {
            $resourceQuery->where('site', ResourceSite::ANN);
        })
        ->get();

        $lens = $this->novaLens(ArtistAnnResourceLens::class);

        $query = $lens->query(Artist::class);

        foreach ($filteredArtists as $filteredArtist) {
            $query->assertContains($filteredArtist);
        }
        $query->assertCount($filteredArtists->count());
    }
}
