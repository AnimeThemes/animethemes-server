<?php

declare(strict_types=1);

namespace Tests\Unit\Pivots;

use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Pivots\ArtistResource;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class ArtistResourceTest.
 */
class ArtistResourceTest extends TestCase
{
    use WithoutEvents;

    /**
     * An ArtistResource shall belong to an Artist.
     *
     * @return void
     */
    public function testArtist(): void
    {
        $artistResource = ArtistResource::factory()
            ->for(Artist::factory())
            ->for(ExternalResource::factory(), 'resource')
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $artistResource->artist());
        static::assertInstanceOf(Artist::class, $artistResource->artist()->first());
    }

    /**
     * An ArtistResource shall belong to an ExternalResource.
     *
     * @return void
     */
    public function testResource(): void
    {
        $artistResource = ArtistResource::factory()
            ->for(Artist::factory())
            ->for(ExternalResource::factory(), 'resource')
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $artistResource->resource());
        static::assertInstanceOf(ExternalResource::class, $artistResource->resource()->first());
    }
}
