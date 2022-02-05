<?php

declare(strict_types=1);

namespace Tests\Unit\Nova\Lenses\Studio;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use App\Nova\Filters\Base\CreatedEndDateFilter;
use App\Nova\Filters\Base\CreatedStartDateFilter;
use App\Nova\Filters\Base\DeletedEndDateFilter;
use App\Nova\Filters\Base\DeletedStartDateFilter;
use App\Nova\Filters\Base\UpdatedEndDateFilter;
use App\Nova\Filters\Base\UpdatedStartDateFilter;
use App\Nova\Lenses\Studio\StudioAnimePlanetResourceLens;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Exceptions\InvalidModelException;
use JoshGaber\NovaUnit\Fields\FieldNotFoundException;
use JoshGaber\NovaUnit\Lenses\InvalidNovaLensException;
use JoshGaber\NovaUnit\Lenses\NovaLensTest;
use Tests\TestCase;

/**
 * Class StudioAnimePlanetResourceTest.
 */
class StudioAnimePlanetResourceTest extends TestCase
{
    use NovaLensTest;
    use WithFaker;
    use WithoutEvents;

    /**
     * The Studio Anime Planet Resource Lens shall contain Studio Fields.
     *
     * @return void
     *
     * @throws InvalidNovaLensException
     */
    public function testFields(): void
    {
        $lens = static::novaLens(StudioAnimePlanetResourceLens::class);

        $lens->assertHasField(__('nova.id'));
        $lens->assertHasField(__('nova.name'));
        $lens->assertHasField(__('nova.slug'));
    }

    /**
     * The Studio Anime Planet Resource Lens fields shall be sortable.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaLensException
     */
    public function testSortable(): void
    {
        $lens = static::novaLens(StudioAnimePlanetResourceLens::class);

        $lens->field(__('nova.id'))->assertSortable();
        $lens->field(__('nova.name'))->assertSortable();
        $lens->field(__('nova.slug'))->assertSortable();
    }

    /**
     * The Studio Anime Planet Resource Lens shall contain Studio Filters.
     *
     * @return void
     *
     * @throws InvalidNovaLensException
     */
    public function testFilters(): void
    {
        $lens = static::novaLens(StudioAnimePlanetResourceLens::class);

        $lens->assertHasFilter(CreatedStartDateFilter::class);
        $lens->assertHasFilter(CreatedEndDateFilter::class);
        $lens->assertHasFilter(UpdatedStartDateFilter::class);
        $lens->assertHasFilter(UpdatedEndDateFilter::class);
        $lens->assertHasFilter(DeletedStartDateFilter::class);
        $lens->assertHasFilter(DeletedEndDateFilter::class);
    }

    /**
     * The Studio Anime Planet Resource Lens shall use the 'withFilters' request.
     *
     * @return void
     *
     * @throws InvalidModelException
     * @throws InvalidNovaLensException
     */
    public function testWithFilters(): void
    {
        $lens = static::novaLens(StudioAnimePlanetResourceLens::class);

        $query = $lens->query(Studio::class);

        $query->assertWithFilters();
    }

    /**
     * The Studio Anime Planet Resource Lens shall use the 'withOrdering' request.
     *
     * @return void
     *
     * @throws InvalidModelException
     * @throws InvalidNovaLensException
     */
    public function testWithOrdering(): void
    {
        $lens = static::novaLens(StudioAnimePlanetResourceLens::class);

        $query = $lens->query(Studio::class);

        $query->assertWithOrdering();
    }

    /**
     * The Studio Anime Planet Resource Lens shall filter Studio without an Anime Planet Resource.
     *
     * @return void
     *
     * @throws InvalidModelException
     * @throws InvalidNovaLensException
     */
    public function testQuery(): void
    {
        Studio::factory()
            ->has(ExternalResource::factory()->count($this->faker->randomDigitNotNull()), 'resources')
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $filteredStudios = Studio::query()
            ->whereDoesntHave(Studio::RELATION_RESOURCES, function (Builder $resourceQuery) {
                $resourceQuery->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANIME_PLANET);
            })
            ->get();

        $lens = static::novaLens(StudioAnimePlanetResourceLens::class);

        $query = $lens->query(Studio::class);

        foreach ($filteredStudios as $filteredStudio) {
            $query->assertContains($filteredStudio);
        }
        $query->assertCount($filteredStudios->count());
    }
}
