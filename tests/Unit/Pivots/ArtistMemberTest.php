<?php declare(strict_types=1);

namespace Pivots;

use App\Models\Artist;
use App\Pivots\ArtistMember;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class ArtistMemberTest
 * @package Pivots
 */
class ArtistMemberTest extends TestCase
{
    use RefreshDatabase;
    use WithoutEvents;

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

        static::assertInstanceOf(BelongsTo::class, $artistMember->artist());
        static::assertInstanceOf(Artist::class, $artistMember->artist()->first());
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

        static::assertInstanceOf(BelongsTo::class, $artistMember->member());
        static::assertInstanceOf(Artist::class, $artistMember->member()->first());
    }
}
