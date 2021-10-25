<?php

declare(strict_types=1);

namespace Tests\Unit\Nova\Lenses\Anime;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Studio;
use App\Nova\Filters\Base\CreatedEndDateFilter;
use App\Nova\Filters\Base\CreatedStartDateFilter;
use App\Nova\Filters\Base\DeletedEndDateFilter;
use App\Nova\Filters\Base\DeletedStartDateFilter;
use App\Nova\Filters\Base\UpdatedEndDateFilter;
use App\Nova\Filters\Base\UpdatedStartDateFilter;
use App\Nova\Filters\Wiki\Anime\AnimeSeasonFilter;
use App\Nova\Filters\Wiki\Anime\AnimeYearFilter;
use App\Nova\Lenses\Anime\AnimeStudioLens;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Exceptions\InvalidModelException;
use JoshGaber\NovaUnit\Fields\FieldNotFoundException;
use JoshGaber\NovaUnit\Lenses\InvalidNovaLensException;
use JoshGaber\NovaUnit\Lenses\NovaLensTest;
use Tests\TestCase;

/**
 * Class AnimeStudioTest.
 */
class AnimeStudioTest extends TestCase
{
    use NovaLensTest;
    use WithFaker;
    use WithoutEvents;

    /**
     * The Anime Studio Resource Lens shall contain Anime Fields.
     *
     * @return void
     *
     * @throws InvalidNovaLensException
     */
    public function testFields()
    {
        $lens = static::novaLens(AnimeStudioLens::class);

        $lens->assertHasField(__('nova.id'));
        $lens->assertHasField(__('nova.name'));
        $lens->assertHasField(__('nova.slug'));
        $lens->assertHasField(__('nova.year'));
        $lens->assertHasField(__('nova.season'));
    }

    /**
     * The Anime Studio Resource Lens fields shall be sortable.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaLensException
     */
    public function testSortable()
    {
        $lens = static::novaLens(AnimeStudioLens::class);

        $lens->field(__('nova.id'))->assertSortable();
        $lens->field(__('nova.name'))->assertSortable();
        $lens->field(__('nova.slug'))->assertSortable();
        $lens->field(__('nova.year'))->assertSortable();
        $lens->field(__('nova.season'))->assertSortable();
    }

    /**
     * The Anime Studio Resource Lens shall contain Anime Filters.
     *
     * @return void
     *
     * @throws InvalidNovaLensException
     */
    public function testFilters()
    {
        $lens = static::novaLens(AnimeStudioLens::class);

        $lens->assertHasFilter(AnimeSeasonFilter::class);
        $lens->assertHasFilter(AnimeYearFilter::class);
        $lens->assertHasFilter(CreatedStartDateFilter::class);
        $lens->assertHasFilter(CreatedEndDateFilter::class);
        $lens->assertHasFilter(UpdatedStartDateFilter::class);
        $lens->assertHasFilter(UpdatedEndDateFilter::class);
        $lens->assertHasFilter(DeletedStartDateFilter::class);
        $lens->assertHasFilter(DeletedEndDateFilter::class);
    }

    /**
     * The Anime Studio Resource Lens shall use the 'withFilters' request.
     *
     * @return void
     *
     * @throws InvalidModelException
     * @throws InvalidNovaLensException
     */
    public function testWithFilters()
    {
        $lens = static::novaLens(AnimeStudioLens::class);

        $query = $lens->query(Anime::class);

        $query->assertWithFilters();
    }

    /**
     * The Anime Studio Resource Lens shall use the 'withOrdering' request.
     *
     * @return void
     *
     * @throws InvalidModelException
     * @throws InvalidNovaLensException
     */
    public function testWithOrdering()
    {
        $lens = static::novaLens(AnimeStudioLens::class);

        $query = $lens->query(Anime::class);

        $query->assertWithOrdering();
    }

    /**
     * The Anime Studio Lens shall filter Anime without Studios.
     *
     * @return void
     *
     * @throws InvalidModelException
     * @throws InvalidNovaLensException
     */
    public function testQuery()
    {
        Anime::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create();

        Anime::factory()
            ->has(Studio::factory()->count($this->faker->randomDigitNotNull()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $filteredAnimes = Anime::query()->whereDoesntHave(Anime::RELATION_STUDIOS)->get();

        $lens = static::novaLens(AnimeStudioLens::class);

        $query = $lens->query(Anime::class);

        foreach ($filteredAnimes as $filteredAnime) {
            $query->assertContains($filteredAnime);
        }
        $query->assertCount($filteredAnimes->count());
    }
}
