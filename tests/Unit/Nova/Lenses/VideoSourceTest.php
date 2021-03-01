<?php

namespace Tests\Unit\Nova\Lenses;

use App\Enums\VideoSource;
use App\Models\Video;
use App\Nova\Filters\RecentlyCreatedFilter;
use App\Nova\Filters\RecentlyUpdatedFilter;
use App\Nova\Filters\VideoTypeFilter;
use App\Nova\Lenses\VideoSourceLens;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JoshGaber\NovaUnit\Lenses\NovaLensTest;
use Tests\TestCase;

class VideoSourceTest extends TestCase
{
    use NovaLensTest, RefreshDatabase, WithFaker;

    /**
     * The Video Source Lens shall contain Video Fields.
     *
     * @return void
     */
    public function testFields()
    {
        $lens = $this->novaLens(VideoSourceLens::class);

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
     */
    public function testSortable()
    {
        $lens = $this->novaLens(VideoSourceLens::class);

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
     */
    public function testFilters()
    {
        $lens = $this->novaLens(VideoSourceLens::class);

        $lens->assertHasFilter(VideoTypeFilter::class);
        $lens->assertHasFilter(RecentlyCreatedFilter::class);
        $lens->assertHasFilter(RecentlyUpdatedFilter::class);
    }

    /**
     * The Video Source Lens shall contain no Actions.
     *
     * @return void
     */
    public function testActions()
    {
        $lens = $this->novaLens(VideoSourceLens::class);

        $lens->assertHasNoActions();
    }

    /**
     * The Video Source Lens shall use the 'withFilters' request.
     *
     * @return void
     */
    public function testWithFilters()
    {
        $lens = $this->novaLens(VideoSourceLens::class);

        $query = $lens->query(Video::class);

        $query->assertWithFilters();
    }

    /**
     * The Video Source Lens shall use the 'withOrdering' request.
     *
     * @return void
     */
    public function testWithOrdering()
    {
        $lens = $this->novaLens(VideoSourceLens::class);

        $query = $lens->query(Video::class);

        $query->assertWithOrdering();
    }

    /**
     * The Video Source Lens shall filter Videos without Source.
     *
     * @return void
     */
    public function testQuery()
    {
        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create([
                'source' => $this->faker->boolean() ? VideoSource::getRandomValue() : null,
            ]);

        $filtered_videos = Video::whereNull('source')->get();

        $lens = $this->novaLens(VideoSourceLens::class);

        $query = $lens->query(Video::class);

        foreach ($filtered_videos as $filtered_video) {
            $query->assertContains($filtered_video);
        }
        $query->assertCount($filtered_videos->count());
    }
}
