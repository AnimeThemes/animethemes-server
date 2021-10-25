<?php

declare(strict_types=1);

namespace Tests\Unit\Nova\Lenses\Video;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Nova\Filters\Base\CreatedEndDateFilter;
use App\Nova\Filters\Base\CreatedStartDateFilter;
use App\Nova\Filters\Base\DeletedEndDateFilter;
use App\Nova\Filters\Base\DeletedStartDateFilter;
use App\Nova\Filters\Base\UpdatedEndDateFilter;
use App\Nova\Filters\Base\UpdatedStartDateFilter;
use App\Nova\Filters\Wiki\Video\VideoTypeFilter;
use App\Nova\Lenses\Video\VideoUnlinkedLens;
use Illuminate\Foundation\Testing\WithFaker;
use JoshGaber\NovaUnit\Exceptions\InvalidModelException;
use JoshGaber\NovaUnit\Fields\FieldNotFoundException;
use JoshGaber\NovaUnit\Lenses\InvalidNovaLensException;
use JoshGaber\NovaUnit\Lenses\NovaLensTest;
use Tests\TestCase;

/**
 * Class VideoUnlinkedTest.
 */
class VideoUnlinkedTest extends TestCase
{
    use NovaLensTest;
    use WithFaker;

    /**
     * The Video Source Lens shall contain Video Fields.
     *
     * @return void
     *
     * @throws InvalidNovaLensException
     * @throws InvalidNovaLensException
     */
    public function testFields()
    {
        $this->withoutEvents();

        $lens = static::novaLens(VideoUnlinkedLens::class);

        $lens->assertHasField(__('nova.id'));
        $lens->assertHasField(__('nova.filename'));
        $lens->assertHasField(__('nova.resolution'));
        $lens->assertHasField(__('nova.nc'));
        $lens->assertHasField(__('nova.subbed'));
        $lens->assertHasField(__('nova.lyrics'));
        $lens->assertHasField(__('nova.uncen'));
    }

    /**
     * The Video Source Lens fields shall be sortable.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaLensException
     */
    public function testSortable()
    {
        $this->withoutEvents();

        $lens = static::novaLens(VideoUnlinkedLens::class);

        $lens->field(__('nova.id'))->assertSortable();
        $lens->field(__('nova.filename'))->assertSortable();
        $lens->field(__('nova.resolution'))->assertSortable();
        $lens->field(__('nova.nc'))->assertSortable();
        $lens->field(__('nova.subbed'))->assertSortable();
        $lens->field(__('nova.lyrics'))->assertSortable();
        $lens->field(__('nova.uncen'))->assertSortable();
    }

    /**
     * The Video Source Lens shall contain Video Filters.
     *
     * @return void
     *
     * @throws InvalidNovaLensException
     */
    public function testFilters()
    {
        $this->withoutEvents();

        $lens = static::novaLens(VideoUnlinkedLens::class);

        $lens->assertHasFilter(VideoTypeFilter::class);
        $lens->assertHasFilter(CreatedStartDateFilter::class);
        $lens->assertHasFilter(CreatedEndDateFilter::class);
        $lens->assertHasFilter(UpdatedStartDateFilter::class);
        $lens->assertHasFilter(UpdatedEndDateFilter::class);
        $lens->assertHasFilter(DeletedStartDateFilter::class);
        $lens->assertHasFilter(DeletedEndDateFilter::class);
    }

    /**
     * The Video Source Lens shall contain no Actions.
     *
     * @return void
     *
     * @throws InvalidNovaLensException
     */
    public function testActions()
    {
        $this->withoutEvents();

        $lens = static::novaLens(VideoUnlinkedLens::class);

        $lens->assertHasNoActions();
    }

    /**
     * The Video Source Lens shall use the 'withFilters' request.
     *
     * @return void
     *
     * @throws InvalidModelException
     * @throws InvalidNovaLensException
     */
    public function testWithFilters()
    {
        $this->withoutEvents();

        $lens = static::novaLens(VideoUnlinkedLens::class);

        $query = $lens->query(Video::class);

        $query->assertWithFilters();
    }

    /**
     * The Video Source Lens shall use the 'withOrdering' request.
     *
     * @return void
     *
     * @throws InvalidModelException
     * @throws InvalidNovaLensException
     */
    public function testWithOrdering()
    {
        $this->withoutEvents();

        $lens = static::novaLens(VideoUnlinkedLens::class);

        $query = $lens->query(Video::class);

        $query->assertWithOrdering();
    }

    /**
     * The Video Source Lens shall filter Videos without Source.
     *
     * @return void
     *
     * @throws InvalidModelException
     * @throws InvalidNovaLensException
     */
    public function testQuery()
    {
        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create();

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->has(
                AnimeThemeEntry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(AnimeTheme::factory()->for(Anime::factory()))
            )
            ->create();

        $filteredVideos = Video::query()->whereDoesntHave(Video::RELATION_ANIMETHEMEENTRIES)->get();

        $lens = static::novaLens(VideoUnlinkedLens::class);

        $query = $lens->query(Video::class);

        foreach ($filteredVideos as $filteredVideo) {
            $query->assertContains($filteredVideo);
        }
        $query->assertCount($filteredVideos->count());
    }
}
