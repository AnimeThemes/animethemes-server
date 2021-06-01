<?php

declare(strict_types=1);

namespace Pivots;

use App\Models\Artist;
use App\Models\ExternalResource;
use App\Pivots\ArtistResource;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class ArtistResourceTest
 * @package Pivots
 */
class ArtistResourceTest extends TestCase
{
    use RefreshDatabase;
    use WithoutEvents;

    /**
     * An ArtistResource shall belong to an Artist.
     *
     * @return void
     */
    public function testArtist()
    {
        $artistResource = ArtistResource::factory()
            ->for(Artist::factory())
            ->for(ExternalResource::factory(), 'resource')
            ->create();

        static::assertInstanceOf(BelongsTo::class, $artistResource->artist());
        static::assertInstanceOf(Artist::class, $artistResource->artist()->first());
    }

    /**
     * An ArtistResource shall belong to an ExternalResource.
     *
     * @return void
     */
    public function testResource()
    {
        $artistResource = ArtistResource::factory()
            ->for(Artist::factory())
            ->for(ExternalResource::factory(), 'resource')
            ->create();

        static::assertInstanceOf(BelongsTo::class, $artistResource->resource());
        static::assertInstanceOf(ExternalResource::class, $artistResource->resource()->first());
    }
}
