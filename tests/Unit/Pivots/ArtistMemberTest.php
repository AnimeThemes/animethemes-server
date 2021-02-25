<?php

namespace Tests\Unit\Pivots;

use App\Models\Artist;
use App\Pivots\ArtistMember;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ArtistMemberTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * An ArtistMember shall belong to an Artist.
     *
     * @return void
     */
    public function testArtist()
    {
        $artist_member = ArtistMember::factory()
            ->for(Artist::factory(), 'artist')
            ->for(Artist::factory(), 'member')
            ->create();

        $this->assertInstanceOf(BelongsTo::class, $artist_member->artist());
        $this->assertInstanceOf(Artist::class, $artist_member->artist()->first());
    }

    /**
     * An ArtistMember shall belong to an Member.
     *
     * @return void
     */
    public function testMember()
    {
        $artist_member = ArtistMember::factory()
            ->for(Artist::factory(), 'artist')
            ->for(Artist::factory(), 'member')
            ->create();

        $this->assertInstanceOf(BelongsTo::class, $artist_member->member());
        $this->assertInstanceOf(Artist::class, $artist_member->member()->first());
    }
}
