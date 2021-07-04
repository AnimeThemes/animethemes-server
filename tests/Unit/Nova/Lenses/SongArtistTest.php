<?php

declare(strict_types=1);

namespace Tests\Unit\Nova\Lenses;

use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Nova\Filters\Base\CreatedEndDateFilter;
use App\Nova\Filters\Base\CreatedStartDateFilter;
use App\Nova\Filters\Base\DeletedEndDateFilter;
use App\Nova\Filters\Base\DeletedStartDateFilter;
use App\Nova\Filters\Base\UpdatedEndDateFilter;
use App\Nova\Filters\Base\UpdatedStartDateFilter;
use App\Nova\Lenses\SongArtistLens;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Exceptions\InvalidModelException;
use JoshGaber\NovaUnit\Fields\FieldNotFoundException;
use JoshGaber\NovaUnit\Lenses\InvalidNovaLensException;
use JoshGaber\NovaUnit\Lenses\NovaLensTest;
use Tests\TestCase;

/**
 * Class SongArtistTest.
 */
class SongArtistTest extends TestCase
{
    use NovaLensTest;
    use RefreshDatabase;
    use WithFaker;
    use WithoutEvents;

    /**
     * The Song Artist Lens shall contain Song Fields.
     *
     * @return void
     * @throws InvalidNovaLensException
     */
    public function testFields()
    {
        $lens = static::novaLens(SongArtistLens::class);

        $lens->assertHasField(__('nova.id'));
        $lens->assertHasField(__('nova.title'));
    }

    /**
     * The Song Artist Lens fields shall be sortable.
     *
     * @return void
     * @throws FieldNotFoundException
     * @throws InvalidNovaLensException
     */
    public function testSortable()
    {
        $lens = static::novaLens(SongArtistLens::class);

        $lens->field(__('nova.id'))->assertSortable();
        $lens->field(__('nova.title'))->assertSortable();
    }

    /**
     * The Song Artist Lens shall contain Song Filters.
     *
     * @return void
     * @throws InvalidNovaLensException
     */
    public function testFilters()
    {
        $lens = static::novaLens(SongArtistLens::class);

        $lens->assertHasFilter(CreatedStartDateFilter::class);
        $lens->assertHasFilter(CreatedEndDateFilter::class);
        $lens->assertHasFilter(UpdatedStartDateFilter::class);
        $lens->assertHasFilter(UpdatedEndDateFilter::class);
        $lens->assertHasFilter(DeletedStartDateFilter::class);
        $lens->assertHasFilter(DeletedEndDateFilter::class);
    }

    /**
     * The Song Artist Lens shall contain no Actions.
     *
     * @return void
     * @throws InvalidNovaLensException
     */
    public function testActions()
    {
        $lens = static::novaLens(SongArtistLens::class);

        $lens->assertHasNoActions();
    }

    /**
     * The Song Artist Lens shall use the 'withFilters' request.
     *
     * @return void
     * @throws InvalidModelException
     * @throws InvalidNovaLensException
     */
    public function testWithFilters()
    {
        $lens = static::novaLens(SongArtistLens::class);

        $query = $lens->query(Song::class);

        $query->assertWithFilters();
    }

    /**
     * The Song Artist Lens shall use the 'withOrdering' request.
     *
     * @return void
     * @throws InvalidModelException
     * @throws InvalidNovaLensException
     */
    public function testWithOrdering()
    {
        $lens = static::novaLens(SongArtistLens::class);

        $query = $lens->query(Song::class);

        $query->assertWithOrdering();
    }

    /**
     * The Song Artist Lens shall filter Songs without Artists.
     *
     * @return void
     * @throws InvalidModelException
     * @throws InvalidNovaLensException
     */
    public function testQuery()
    {
        Song::factory()->count($this->faker->randomDigitNotNull)->create();

        Song::factory()
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $filteredSongs = Song::whereDoesntHave('artists')->get();

        $lens = static::novaLens(SongArtistLens::class);

        $query = $lens->query(Song::class);

        foreach ($filteredSongs as $filteredSong) {
            $query->assertContains($filteredSong);
        }
        $query->assertCount($filteredSongs->count());
    }
}
