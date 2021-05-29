<?php

namespace Tests\Unit\Pivots;

use App\Models\Artist;
use App\Pivots\ArtistMember;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class ArtistMemberTest extends TestCase
{
    use RefreshDatabase, WithoutEvents;

    /**
     * An ArtistMember shall belong to an Artist.
     *
     * @return void
     */
    public function testArtist()
    {
        $artistMember = ArtistMember::factory()
            ->for(Artist::factory(), 'artist')
            ->for(Artist::factory(), 'member')
            ->create();

        $this->assertInstanceOf(BelongsTo::class, $artistMember->artist());
        $this->assertInstanceOf(Artist::class, $artistMember->artist()->first());
    }

    /**
     * An ArtistMember shall belong to an Member.
     *
     * @return void
     */
    public function testMember()
    {
        $artistMember = ArtistMember::factory()
            ->for(Artist::factory(), 'artist')
            ->for(Artist::factory(), 'member')
            ->create();

        $this->assertInstanceOf(BelongsTo::class, $artistMember->member());
        $this->assertInstanceOf(Artist::class, $artistMember->member()->first());
    }
}
