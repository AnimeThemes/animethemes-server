<?php

namespace Tests\Unit\Nova\Lenses;

use App\Models\Anime;
use App\Models\Artist;
use App\Models\ExternalResource;
use App\Nova\Filters\RecentlyCreatedFilter;
use App\Nova\Filters\RecentlyUpdatedFilter;
use App\Nova\Lenses\ExternalResourceUnlinkedLens;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JoshGaber\NovaUnit\Lenses\NovaLensTest;
use Tests\TestCase;

class ExternalResourceUnlinkedTest extends TestCase
{
    use NovaLensTest, RefreshDatabase, WithFaker;

    /**
     * The Resource Unlinked Lens shall contain Resource Fields.
     *
     * @return void
     */
    public function testFields()
    {
        $lens = $this->novaLens(ExternalResourceUnlinkedLens::class);

        $lens->assertHasField(__('nova.id'));
        $lens->assertHasField(__('nova.link'));
        $lens->assertHasField(__('nova.external_id'));
    }

    /**
     * The Resource Unlinked Lens fields shall be sortable.
     *
     * @return void
     */
    public function testSortable()
    {
        $lens = $this->novaLens(ExternalResourceUnlinkedLens::class);

        $lens->field(__('nova.id'))->assertSortable();
        $lens->field(__('nova.link'))->assertSortable();
        $lens->field(__('nova.external_id'))->assertSortable();
    }

    /**
     * The Resource Unlinked Lens shall contain Resource Filters.
     *
     * @return void
     */
    public function testFilters()
    {
        $lens = $this->novaLens(ExternalResourceUnlinkedLens::class);

        $lens->assertHasFilter(RecentlyCreatedFilter::class);
        $lens->assertHasFilter(RecentlyUpdatedFilter::class);
    }

    /**
     * The Resource Unlinked Lens shall contain no Actions.
     *
     * @return void
     */
    public function testActions()
    {
        $lens = $this->novaLens(ExternalResourceUnlinkedLens::class);

        $lens->assertHasNoActions();
    }

    /**
     * The Resource Unlinked Lens shall use the 'withFilters' request.
     *
     * @return void
     */
    public function testWithFilters()
    {
        $lens = $this->novaLens(ExternalResourceUnlinkedLens::class);

        $query = $lens->query(ExternalResource::class);

        $query->assertWithFilters();
    }

    /**
     * The Resource Unlinked Lens shall use the 'withOrdering' request.
     *
     * @return void
     */
    public function testWithOrdering()
    {
        $lens = $this->novaLens(ExternalResourceUnlinkedLens::class);

        $query = $lens->query(ExternalResource::class);

        $query->assertWithOrdering();
    }

    /**
     * The Resource Unlinked Lens shall filter Resources without Anime or Artists.
     *
     * @return void
     */
    public function testQuery()
    {
        ExternalResource::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        ExternalResource::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        ExternalResource::factory()
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        ExternalResource::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull))
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $filtered_resources = ExternalResource::whereDoesntHave('anime')
            ->whereDoesntHave('artists')
            ->get();

        $lens = $this->novaLens(ExternalResourceUnlinkedLens::class);

        $query = $lens->query(ExternalResource::class);

        foreach ($filtered_resources as $filtered_resource) {
            $query->assertContains($filtered_resource);
        }
        $query->assertCount($filtered_resources->count());
    }
}
