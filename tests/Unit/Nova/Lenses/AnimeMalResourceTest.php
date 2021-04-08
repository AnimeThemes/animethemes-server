<?php

namespace Tests\Unit\Nova\Lenses;

use App\Enums\ResourceSite;
use App\Models\Anime;
use App\Models\ExternalResource;
use App\Nova\Filters\AnimeSeasonFilter;
use App\Nova\Filters\AnimeYearFilter;
use App\Nova\Filters\RecentlyCreatedFilter;
use App\Nova\Filters\RecentlyUpdatedFilter;
use App\Nova\Lenses\AnimeMalResourceLens;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Lenses\NovaLensTest;
use Tests\TestCase;

class AnimeMalResourceTest extends TestCase
{
    use NovaLensTest, RefreshDatabase, WithFaker, WithoutEvents;

    /**
     * The Anime Mal Resource Lens shall contain Anime Fields.
     *
     * @return void
     */
    public function testFields()
    {
        $lens = $this->novaLens(AnimeMalResourceLens::class);

        $lens->assertHasField(__('nova.id'));
        $lens->assertHasField(__('nova.name'));
        $lens->assertHasField(__('nova.slug'));
        $lens->assertHasField(__('nova.year'));
        $lens->assertHasField(__('nova.season'));
    }

    /**
     * The Anime Mal Resource Lens fields shall be sortable.
     *
     * @return void
     */
    public function testSortable()
    {
        $lens = $this->novaLens(AnimeMalResourceLens::class);

        $lens->field(__('nova.id'))->assertSortable();
        $lens->field(__('nova.name'))->assertSortable();
        $lens->field(__('nova.slug'))->assertSortable();
        $lens->field(__('nova.year'))->assertSortable();
        $lens->field(__('nova.season'))->assertSortable();
    }

    /**
     * The Anime Mal Resource Lens shall contain Anime Filters.
     *
     * @return void
     */
    public function testFilters()
    {
        $lens = $this->novaLens(AnimeMalResourceLens::class);

        $lens->assertHasFilter(AnimeSeasonFilter::class);
        $lens->assertHasFilter(AnimeYearFilter::class);
        $lens->assertHasFilter(RecentlyCreatedFilter::class);
        $lens->assertHasFilter(RecentlyUpdatedFilter::class);
    }

    // TODO: testActions()

    /**
     * The Anime Mal Resource Lens shall use the 'withFilters' request.
     *
     * @return void
     */
    public function testWithFilters()
    {
        $lens = $this->novaLens(AnimeMalResourceLens::class);

        $query = $lens->query(Anime::class);

        $query->assertWithFilters();
    }

    /**
     * The Anime Mal Resource Lens shall use the 'withOrdering' request.
     *
     * @return void
     */
    public function testWithOrdering()
    {
        $lens = $this->novaLens(AnimeMalResourceLens::class);

        $query = $lens->query(Anime::class);

        $query->assertWithOrdering();
    }

    /**
     * The Anime Mal Resource Lens shall filter Anime without an Mal Resource.
     *
     * @return void
     */
    public function testQuery()
    {
        Anime::factory()
            ->has(ExternalResource::factory()->count($this->faker->randomDigitNotNull))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $filtered_animes = Anime::whereDoesntHave('externalResources', function (Builder $resource_query) {
            $resource_query->where('site', ResourceSite::MAL);
        })
        ->get();

        $lens = $this->novaLens(AnimeMalResourceLens::class);

        $query = $lens->query(Anime::class);

        foreach ($filtered_animes as $filtered_anime) {
            $query->assertContains($filtered_anime);
        }
        $query->assertCount($filtered_animes->count());
    }
}
