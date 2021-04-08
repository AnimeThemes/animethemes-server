<?php

namespace Tests\Unit\Nova\Lenses;

use App\Enums\ResourceSite;
use App\Models\Artist;
use App\Models\ExternalResource;
use App\Nova\Filters\RecentlyCreatedFilter;
use App\Nova\Filters\RecentlyUpdatedFilter;
use App\Nova\Lenses\ArtistAnilistResourceLens;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Lenses\NovaLensTest;
use Tests\TestCase;

class ArtistAnilistResourceTest extends TestCase
{
    use NovaLensTest, RefreshDatabase, WithFaker, WithoutEvents;

    /**
     * The Artist Anilist Resource Lens shall contain Artist Fields.
     *
     * @return void
     */
    public function testFields()
    {
        $lens = $this->novaLens(ArtistAnilistResourceLens::class);

        $lens->assertHasField(__('nova.id'));
        $lens->assertHasField(__('nova.name'));
        $lens->assertHasField(__('nova.slug'));
    }

    /**
     * The Artist Anilist Resource Lens fields shall be sortable.
     *
     * @return void
     */
    public function testSortable()
    {
        $lens = $this->novaLens(ArtistAnilistResourceLens::class);

        $lens->field(__('nova.id'))->assertSortable();
        $lens->field(__('nova.name'))->assertSortable();
        $lens->field(__('nova.slug'))->assertSortable();
    }

    /**
     * The Artist Anilist Resource Lens shall contain Artist Filters.
     *
     * @return void
     */
    public function testFilters()
    {
        $lens = $this->novaLens(ArtistAnilistResourceLens::class);

        $lens->assertHasFilter(RecentlyCreatedFilter::class);
        $lens->assertHasFilter(RecentlyUpdatedFilter::class);
    }

    // TODO: testActions()

    /**
     * The Artist Anilist Resource Lens shall use the 'withFilters' request.
     *
     * @return void
     */
    public function testWithFilters()
    {
        $lens = $this->novaLens(ArtistAnilistResourceLens::class);

        $query = $lens->query(Artist::class);

        $query->assertWithFilters();
    }

    /**
     * The Artist Anilist Resource Lens shall use the 'withOrdering' request.
     *
     * @return void
     */
    public function testWithOrdering()
    {
        $lens = $this->novaLens(ArtistAnilistResourceLens::class);

        $query = $lens->query(Artist::class);

        $query->assertWithOrdering();
    }

    /**
     * The Artist Anilist Resource Lens shall filter Artist without an Anilist Resource.
     *
     * @return void
     */
    public function testQuery()
    {
        Artist::factory()
            ->has(ExternalResource::factory()->count($this->faker->randomDigitNotNull))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $filtered_artists = Artist::whereDoesntHave('externalResources', function (Builder $resource_query) {
            $resource_query->where('site', ResourceSite::ANILIST);
        })
        ->get();

        $lens = $this->novaLens(ArtistAnilistResourceLens::class);

        $query = $lens->query(Artist::class);

        foreach ($filtered_artists as $filtered_artist) {
            $query->assertContains($filtered_artist);
        }
        $query->assertCount($filtered_artists->count());
    }
}
