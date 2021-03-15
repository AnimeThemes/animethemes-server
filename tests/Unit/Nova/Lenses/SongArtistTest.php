<?php

namespace Tests\Unit\Nova\Lenses;

use App\Models\Artist;
use App\Models\Song;
use App\Nova\Filters\RecentlyCreatedFilter;
use App\Nova\Filters\RecentlyUpdatedFilter;
use App\Nova\Lenses\SongArtistLens;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Lenses\NovaLensTest;
use Tests\TestCase;

class SongArtistTest extends TestCase
{
    use NovaLensTest, RefreshDatabase, WithFaker, WithoutEvents;

    /**
     * The Song Artist Lens shall contain Song Fields.
     *
     * @return void
     */
    public function testFields()
    {
        $lens = $this->novaLens(SongArtistLens::class);

        $lens->assertHasField(__('nova.id'));
        $lens->assertHasField(__('nova.title'));
    }

    /**
     * The Song Artist Lens fields shall be sortable.
     *
     * @return void
     */
    public function testSortable()
    {
        $lens = $this->novaLens(SongArtistLens::class);

        $lens->field(__('nova.id'))->assertSortable();
        $lens->field(__('nova.title'))->assertSortable();
    }

    /**
     * The Song Artist Lens shall contain Song Filters.
     *
     * @return void
     */
    public function testFilters()
    {
        $lens = $this->novaLens(SongArtistLens::class);

        $lens->assertHasFilter(RecentlyCreatedFilter::class);
        $lens->assertHasFilter(RecentlyUpdatedFilter::class);
    }

    /**
     * The Song Artist Lens shall contain no Actions.
     *
     * @return void
     */
    public function testActions()
    {
        $lens = $this->novaLens(SongArtistLens::class);

        $lens->assertHasNoActions();
    }

    /**
     * The Song Artist Lens shall use the 'withFilters' request.
     *
     * @return void
     */
    public function testWithFilters()
    {
        $lens = $this->novaLens(SongArtistLens::class);

        $query = $lens->query(Song::class);

        $query->assertWithFilters();
    }

    /**
     * The Song Artist Lens shall use the 'withOrdering' request.
     *
     * @return void
     */
    public function testWithOrdering()
    {
        $lens = $this->novaLens(SongArtistLens::class);

        $query = $lens->query(Song::class);

        $query->assertWithOrdering();
    }

    /**
     * The Song Artist Lens shall filter Songs without Artists.
     *
     * @return void
     */
    public function testQuery()
    {
        Song::factory()->count($this->faker->randomDigitNotNull)->create();

        Song::factory()
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $filtered_songs = Song::whereDoesntHave('artists')->get();

        $lens = $this->novaLens(SongArtistLens::class);

        $query = $lens->query(Song::class);

        foreach ($filtered_songs as $filtered_song) {
            $query->assertContains($filtered_song);
        }
        $query->assertCount($filtered_songs->count());
    }
}
