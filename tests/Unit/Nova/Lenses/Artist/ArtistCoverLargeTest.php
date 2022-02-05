<?php

declare(strict_types=1);

namespace Tests\Unit\Nova\Lenses\Artist;

use App\Enums\Models\Wiki\ImageFacet;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use App\Nova\Filters\Base\CreatedEndDateFilter;
use App\Nova\Filters\Base\CreatedStartDateFilter;
use App\Nova\Filters\Base\DeletedEndDateFilter;
use App\Nova\Filters\Base\DeletedStartDateFilter;
use App\Nova\Filters\Base\UpdatedEndDateFilter;
use App\Nova\Filters\Base\UpdatedStartDateFilter;
use App\Nova\Lenses\Artist\ArtistCoverLargeLens;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Exceptions\InvalidModelException;
use JoshGaber\NovaUnit\Fields\FieldNotFoundException;
use JoshGaber\NovaUnit\Lenses\InvalidNovaLensException;
use JoshGaber\NovaUnit\Lenses\NovaLensTest;
use Tests\TestCase;

/**
 * Class ArtistCoverLargeTest.
 */
class ArtistCoverLargeTest extends TestCase
{
    use NovaLensTest;
    use WithFaker;
    use WithoutEvents;

    /**
     * The Artist Large Cover Lens shall contain Artist Fields.
     *
     * @return void
     *
     * @throws InvalidNovaLensException
     */
    public function testFields(): void
    {
        $lens = static::novaLens(ArtistCoverLargeLens::class);

        $lens->assertHasField(__('nova.id'));
        $lens->assertHasField(__('nova.name'));
        $lens->assertHasField(__('nova.slug'));
    }

    /**
     * The Artist Large Cover Lens fields shall be sortable.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaLensException
     */
    public function testSortable(): void
    {
        $lens = static::novaLens(ArtistCoverLargeLens::class);

        $lens->field(__('nova.id'))->assertSortable();
        $lens->field(__('nova.name'))->assertSortable();
        $lens->field(__('nova.slug'))->assertSortable();
    }

    /**
     * The Artist Large Cover Lens shall contain Artist Filters.
     *
     * @return void
     *
     * @throws InvalidNovaLensException
     */
    public function testFilters(): void
    {
        $lens = static::novaLens(ArtistCoverLargeLens::class);

        $lens->assertHasFilter(CreatedStartDateFilter::class);
        $lens->assertHasFilter(CreatedEndDateFilter::class);
        $lens->assertHasFilter(UpdatedStartDateFilter::class);
        $lens->assertHasFilter(UpdatedEndDateFilter::class);
        $lens->assertHasFilter(DeletedStartDateFilter::class);
        $lens->assertHasFilter(DeletedEndDateFilter::class);
    }

    /**
     * The Artist Large Cover Lens shall contain no Actions.
     *
     * @return void
     *
     * @throws InvalidNovaLensException
     */
    public function testActions(): void
    {
        $lens = static::novaLens(ArtistCoverLargeLens::class);

        $lens->assertHasNoActions();
    }

    /**
     * The Artist Large Cover Lens shall use the 'withFilters' request.
     *
     * @return void
     *
     * @throws InvalidModelException
     * @throws InvalidNovaLensException
     */
    public function testWithFilters(): void
    {
        $lens = static::novaLens(ArtistCoverLargeLens::class);

        $query = $lens->query(Artist::class);

        $query->assertWithFilters();
    }

    /**
     * The Artist Large Cover Lens shall use the 'withOrdering' request.
     *
     * @return void
     *
     * @throws InvalidModelException
     * @throws InvalidNovaLensException
     */
    public function testWithOrdering(): void
    {
        $lens = static::novaLens(ArtistCoverLargeLens::class);

        $query = $lens->query(Artist::class);

        $query->assertWithOrdering();
    }

    /**
     * The Artist Large Cover Lens shall filter Artist without a Large Cover image.
     *
     * @return void
     *
     * @throws InvalidModelException
     * @throws InvalidNovaLensException
     */
    public function testQuery(): void
    {
        Artist::factory()
            ->has(Image::factory()->count($this->faker->randomDigitNotNull()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $filteredArtists = Artist::query()
            ->whereDoesntHave(Artist::RELATION_IMAGES, function (Builder $imageQuery) {
                $imageQuery->where(Image::ATTRIBUTE_FACET, ImageFacet::COVER_LARGE);
            })
            ->get();

        $lens = static::novaLens(ArtistCoverLargeLens::class);

        $query = $lens->query(Artist::class);

        foreach ($filteredArtists as $filteredArtist) {
            $query->assertContains($filteredArtist);
        }
        $query->assertCount($filteredArtists->count());
    }
}
