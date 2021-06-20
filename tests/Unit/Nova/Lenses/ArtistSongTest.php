<?php

declare(strict_types=1);

namespace Nova\Lenses;

use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
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
use JoshGaber\NovaUnit\Exceptions\InvalidModelException;
use JoshGaber\NovaUnit\Fields\FieldNotFoundException;
use JoshGaber\NovaUnit\Lenses\InvalidNovaLensException;
use JoshGaber\NovaUnit\Lenses\NovaLensTest;
use Tests\TestCase;

/**
 * Class ArtistSongTest.
 */
class ArtistSongTest extends TestCase
{
    use NovaLensTest;
    use RefreshDatabase;
    use WithFaker;
    use WithoutEvents;

    /**
     * The Artist Song Lens shall contain Artist Fields.
     *
     * @return void
     * @throws InvalidNovaLensException
     */
    public function testFields()
    {
        $lens = static::novaLens(ArtistSongLens::class);

        $lens->assertHasField(__('nova.id'));
        $lens->assertHasField(__('nova.name'));
        $lens->assertHasField(__('nova.slug'));
    }

    /**
     * The Artist Song Lens fields shall be sortable.
     *
     * @return void
     * @throws FieldNotFoundException
     * @throws InvalidNovaLensException
     */
    public function testSortable()
    {
        $lens = static::novaLens(ArtistSongLens::class);

        $lens->field(__('nova.id'))->assertSortable();
        $lens->field(__('nova.name'))->assertSortable();
        $lens->field(__('nova.slug'))->assertSortable();
    }

    /**
     * The Artist Song Lens shall contain Artist Filters.
     *
     * @return void
     * @throws InvalidNovaLensException
     */
    public function testFilters()
    {
        $lens = static::novaLens(ArtistSongLens::class);

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
     * @throws InvalidNovaLensException
     */
    public function testActions()
    {
        $lens = static::novaLens(ArtistSongLens::class);

        $lens->assertHasNoActions();
    }

    /**
     * The Artist Song Lens shall use the 'withFilters' request.
     *
     * @return void
     * @throws InvalidModelException
     * @throws InvalidNovaLensException
     */
    public function testWithFilters()
    {
        $lens = static::novaLens(ArtistSongLens::class);

        $query = $lens->query(Artist::class);

        $query->assertWithFilters();
    }

    /**
     * The Artist Song Lens shall use the 'withOrdering' request.
     *
     * @return void
     * @throws InvalidModelException
     * @throws InvalidNovaLensException
     */
    public function testWithOrdering()
    {
        $lens = static::novaLens(ArtistSongLens::class);

        $query = $lens->query(Artist::class);

        $query->assertWithOrdering();
    }

    /**
     * The Artist Song Lens shall filter Artist without Songs.
     *
     * @return void
     * @throws InvalidModelException
     * @throws InvalidNovaLensException
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

        $filteredArtists = Artist::whereDoesntHave('songs')->get();

        $lens = static::novaLens(ArtistSongLens::class);

        $query = $lens->query(Artist::class);

        foreach ($filteredArtists as $filteredArtist) {
            $query->assertContains($filteredArtist);
        }
        $query->assertCount($filteredArtists->count());
    }
}
