<?php

namespace Tests\Unit\Nova\Lenses;

use App\Models\Artist;
use App\Models\Song;
use App\Nova\Filters\CreatedEndDateFilter;
use App\Nova\Filters\CreatedStartDateFilter;
use App\Nova\Filters\DeletedEndDateFilter;
use App\Nova\Filters\DeletedStartDateFilter;
use App\Nova\Filters\UpdatedEndDateFilter;
use App\Nova\Filters\UpdatedStartDateFilter;
use App\Nova\Lenses\ArtistSongLens;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Lenses\NovaLensTest;
use Tests\TestCase;

class ArtistSongTest extends TestCase
{
    use NovaLensTest, RefreshDatabase, WithFaker, WithoutEvents;

    /**
     * The Artist Song Lens shall contain Artist Fields.
     *
     * @return void
     */
    public function testFields()
    {
        $lens = $this->novaLens(ArtistSongLens::class);

        $lens->assertHasField(__('nova.id'));
        $lens->assertHasField(__('nova.name'));
        $lens->assertHasField(__('nova.slug'));
    }

    /**
     * The Artist Song Lens fields shall be sortable.
     *
     * @return void
     */
    public function testSortable()
    {
        $lens = $this->novaLens(ArtistSongLens::class);

        $lens->field(__('nova.id'))->assertSortable();
        $lens->field(__('nova.name'))->assertSortable();
        $lens->field(__('nova.slug'))->assertSortable();
    }

    /**
     * The Artist Song Lens shall contain Artist Filters.
     *
     * @return void
     */
    public function testFilters()
    {
        $lens = $this->novaLens(ArtistSongLens::class);

        $lens->assertHasFilter(CreatedStartDateFilter::class);
        $lens->assertHasFilter(CreatedEndDateFilter::class);
        $lens->assertHasFilter(UpdatedStartDateFilter::class);
        $lens->assertHasFilter(UpdatedEndDateFilter::class);
        $lens->assertHasFilter(DeletedStartDateFilter::class);
        $lens->assertHasFilter(DeletedEndDateFilter::class);
    }

    /**
     * The Artist Song Lens shall contain no Actions.
     *
     * @return void
     */
    public function testActions()
    {
        $lens = $this->novaLens(ArtistSongLens::class);

        $lens->assertHasNoActions();
    }

    /**
     * The Artist Song Lens shall use the 'withFilters' request.
     *
     * @return void
     */
    public function testWithFilters()
    {
        $lens = $this->novaLens(ArtistSongLens::class);

        $query = $lens->query(Artist::class);

        $query->assertWithFilters();
    }

    /**
     * The Artist Song Lens shall use the 'withOrdering' request.
     *
     * @return void
     */
    public function testWithOrdering()
    {
        $lens = $this->novaLens(ArtistSongLens::class);

        $query = $lens->query(Artist::class);

        $query->assertWithOrdering();
    }

    /**
     * The Artist Song Lens shall filter Artist without Songs.
     *
     * @return void
     */
    public function testQuery()
    {
        Artist::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        Artist::factory()
            ->has(Song::factory()->count($this->faker->randomDigitNotNull))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $filtered_artists = Artist::whereDoesntHave('songs')->get();

        $lens = $this->novaLens(ArtistSongLens::class);

        $query = $lens->query(Artist::class);

        foreach ($filtered_artists as $filtered_artist) {
            $query->assertContains($filtered_artist);
        }
        $query->assertCount($filtered_artists->count());
    }
}
