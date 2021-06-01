<?php

declare(strict_types=1);

namespace Nova\Lenses;

use App\Models\Anime;
use App\Models\Artist;
use App\Models\ExternalResource;
use App\Nova\Filters\CreatedEndDateFilter;
use App\Nova\Filters\CreatedStartDateFilter;
use App\Nova\Filters\DeletedEndDateFilter;
use App\Nova\Filters\DeletedStartDateFilter;
use App\Nova\Filters\UpdatedEndDateFilter;
use App\Nova\Filters\UpdatedStartDateFilter;
use App\Nova\Lenses\ExternalResourceUnlinkedLens;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Exceptions\InvalidModelException;
use JoshGaber\NovaUnit\Fields\FieldNotFoundException;
use JoshGaber\NovaUnit\Lenses\InvalidNovaLensException;
use JoshGaber\NovaUnit\Lenses\NovaLensTest;
use Tests\TestCase;

/**
 * Class ExternalResourceUnlinkedTest
 * @package Nova\Lenses
 */
class ExternalResourceUnlinkedTest extends TestCase
{
    use NovaLensTest;
    use RefreshDatabase;
    use WithFaker;
    use WithoutEvents;

    /**
     * The Resource Unlinked Lens shall contain Resource Fields.
     *
     * @return void
     * @throws InvalidNovaLensException
     */
    public function testFields()
    {
        $lens = static::novaLens(ExternalResourceUnlinkedLens::class);

        $lens->assertHasField(__('nova.id'));
        $lens->assertHasField(__('nova.link'));
        $lens->assertHasField(__('nova.external_id'));
    }

    /**
     * The Resource Unlinked Lens fields shall be sortable.
     *
     * @return void
     * @throws FieldNotFoundException
     * @throws InvalidNovaLensException
     */
    public function testSortable()
    {
        $lens = static::novaLens(ExternalResourceUnlinkedLens::class);

        $lens->field(__('nova.id'))->assertSortable();
        $lens->field(__('nova.link'))->assertSortable();
        $lens->field(__('nova.external_id'))->assertSortable();
    }

    /**
     * The Resource Unlinked Lens shall contain Resource Filters.
     *
     * @return void
     * @throws InvalidNovaLensException
     */
    public function testFilters()
    {
        $lens = static::novaLens(ExternalResourceUnlinkedLens::class);

        $lens->assertHasFilter(CreatedStartDateFilter::class);
        $lens->assertHasFilter(CreatedEndDateFilter::class);
        $lens->assertHasFilter(UpdatedStartDateFilter::class);
        $lens->assertHasFilter(UpdatedEndDateFilter::class);
        $lens->assertHasFilter(DeletedStartDateFilter::class);
        $lens->assertHasFilter(DeletedEndDateFilter::class);
    }

    /**
     * The Resource Unlinked Lens shall contain no Actions.
     *
     * @return void
     * @throws InvalidNovaLensException
     */
    public function testActions()
    {
        $lens = static::novaLens(ExternalResourceUnlinkedLens::class);

        $lens->assertHasNoActions();
    }

    /**
     * The Resource Unlinked Lens shall use the 'withFilters' request.
     *
     * @return void
     * @throws InvalidModelException
     * @throws InvalidNovaLensException
     */
    public function testWithFilters()
    {
        $lens = static::novaLens(ExternalResourceUnlinkedLens::class);

        $query = $lens->query(ExternalResource::class);

        $query->assertWithFilters();
    }

    /**
     * The Resource Unlinked Lens shall use the 'withOrdering' request.
     *
     * @return void
     * @throws InvalidModelException
     * @throws InvalidNovaLensException
     */
    public function testWithOrdering()
    {
        $lens = static::novaLens(ExternalResourceUnlinkedLens::class);

        $query = $lens->query(ExternalResource::class);

        $query->assertWithOrdering();
    }

    /**
     * The Resource Unlinked Lens shall filter Resources without Anime or Artists.
     *
     * @return void
     * @throws InvalidModelException
     * @throws InvalidNovaLensException
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

        $filteredResources = ExternalResource::whereDoesntHave('anime')
            ->whereDoesntHave('artists')
            ->get();

        $lens = static::novaLens(ExternalResourceUnlinkedLens::class);

        $query = $lens->query(ExternalResource::class);

        foreach ($filteredResources as $filteredResource) {
            $query->assertContains($filteredResource);
        }
        $query->assertCount($filteredResources->count());
    }
}
