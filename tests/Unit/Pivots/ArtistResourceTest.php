<?php

namespace Tests\Unit\Pivots;

use App\Models\Artist;
use App\Models\ExternalResource;
use App\Pivots\ArtistResource;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArtistResourceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * An ArtistResource shall belong to an Artist.
     *
     * @return void
     */
    public function testArtist()
    {
        $artist_resource = ArtistResource::factory()
            ->for(Artist::factory())
            ->for(ExternalResource::factory(), 'resource')
            ->create();

        $this->assertInstanceOf(BelongsTo::class, $artist_resource->artist());
        $this->assertInstanceOf(Artist::class, $artist_resource->artist()->first());
    }

    /**
     * An ArtistResource shall belong to an ExternalResource.
     *
     * @return void
     */
    public function testResource()
    {
        $artist_resource = ArtistResource::factory()
            ->for(Artist::factory())
            ->for(ExternalResource::factory(), 'resource')
            ->create();

        $this->assertInstanceOf(BelongsTo::class, $artist_resource->resource());
        $this->assertInstanceOf(ExternalResource::class, $artist_resource->resource()->first());
    }
}
