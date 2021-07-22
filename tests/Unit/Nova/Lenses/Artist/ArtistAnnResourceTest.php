<?php

declare(strict_types=1);

namespace Tests\Unit\Nova\Lenses\Artist;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Nova\Filters\Base\CreatedEndDateFilter;
use App\Nova\Filters\Base\CreatedStartDateFilter;
use App\Nova\Filters\Base\DeletedEndDateFilter;
use App\Nova\Filters\Base\DeletedStartDateFilter;
use App\Nova\Filters\Base\UpdatedEndDateFilter;
use App\Nova\Filters\Base\UpdatedStartDateFilter;
use App\Nova\Lenses\Artist\ArtistAnnResourceLens;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Exceptions\InvalidModelException;
use JoshGaber\NovaUnit\Fields\FieldNotFoundException;
use JoshGaber\NovaUnit\Lenses\InvalidNovaLensException;
use JoshGaber\NovaUnit\Lenses\NovaLensTest;
use Tests\TestCase;

/**
 * Class ArtistAnnResourceTest.
 */
class ArtistAnnResourceTest extends TestCase
{
    use NovaLensTest;
    use RefreshDatabase;
    use WithFaker;
    use WithoutEvents;

    /**
     * The Artist Ann Resource Lens shall contain Artist Fields.
     *
     * @return void
     * @throws InvalidNovaLensException
     */
    public function testFields()
    {
        $lens = static::novaLens(ArtistAnnResourceLens::class);

        $lens->assertHasField(__('nova.id'));
        $lens->assertHasField(__('nova.name'));
        $lens->assertHasField(__('nova.slug'));
    }

    /**
     * The Artist Ann Resource Lens fields shall be sortable.
     *
     * @return void
     * @throws FieldNotFoundException
     * @throws InvalidNovaLensException
     */
    public function testSortable()
    {
        $lens = static::novaLens(ArtistAnnResourceLens::class);

        $lens->field(__('nova.id'))->assertSortable();
        $lens->field(__('nova.name'))->assertSortable();
        $lens->field(__('nova.slug'))->assertSortable();
    }

    /**
     * The Artist Ann Resource Lens shall contain Artist Filters.
     *
     * @return void
     * @throws InvalidNovaLensException
     */
    public function testFilters()
    {
        $lens = static::novaLens(ArtistAnnResourceLens::class);

        $lens->assertHasFilter(CreatedStartDateFilter::class);
        $lens->assertHasFilter(CreatedEndDateFilter::class);
        $lens->assertHasFilter(UpdatedStartDateFilter::class);
        $lens->assertHasFilter(UpdatedEndDateFilter::class);
        $lens->assertHasFilter(DeletedStartDateFilter::class);
        $lens->assertHasFilter(DeletedEndDateFilter::class);
    }

    /**
     * The Artist Ann Resource Lens shall use the 'withFilters' request.
     *
     * @return void
     * @throws InvalidModelException
     * @throws InvalidNovaLensException
     */
    public function testWithFilters()
    {
        $lens = static::novaLens(ArtistAnnResourceLens::class);

        $query = $lens->query(Artist::class);

        $query->assertWithFilters();
    }

    /**
     * The Artist Ann Resource Lens shall use the 'withOrdering' request.
     *
     * @return void
     * @throws InvalidModelException
     * @throws InvalidNovaLensException
     */
    public function testWithOrdering()
    {
        $lens = static::novaLens(ArtistAnnResourceLens::class);

        $query = $lens->query(Artist::class);

        $query->assertWithOrdering();
    }

    /**
     * The Artist Ann Resource Lens shall filter Artist without an ANN Resource.
     *
     * @return void
     * @throws InvalidModelException
     * @throws InvalidNovaLensException
     */
    public function testQuery()
    {
        Artist::factory()
            ->has(ExternalResource::factory()->count($this->faker->randomDigitNotNull()), 'resources')
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $filteredArtists = Artist::query()
            ->whereDoesntHave('resources', function (Builder $resourceQuery) {
                $resourceQuery->where('site', ResourceSite::ANN);
            })
            ->get();

        $lens = static::novaLens(ArtistAnnResourceLens::class);

        $query = $lens->query(Artist::class);

        foreach ($filteredArtists as $filteredArtist) {
            $query->assertContains($filteredArtist);
        }
        $query->assertCount($filteredArtists->count());
    }
}