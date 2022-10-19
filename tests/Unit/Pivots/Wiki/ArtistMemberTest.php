<?php

declare(strict_types=1);

namespace Tests\Unit\Pivots\Wiki;

use App\Models\Wiki\Artist;
use App\Pivots\Wiki\ArtistMember;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class ArtistMemberTest.
 */
class ArtistMemberTest extends TestCase
{
    use WithoutEvents;

    /**
     * An ArtistMember shall belong to an Artist.
     *
     * @return void
     */
    public function testArtist(): void
    {
        $artistMember = ArtistMember::factory()
            ->for(Artist::factory(), 'artist')
            ->for(Artist::factory(), 'member')
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $artistMember->artist());
        static::assertInstanceOf(Artist::class, $artistMember->artist()->first());
    }

    /**
     * An ArtistMember shall belong to a Member.
     *
     * @return void
     */
    public function testMember(): void
    {
        $artistMember = ArtistMember::factory()
            ->for(Artist::factory(), 'artist')
            ->for(Artist::factory(), 'member')
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $artistMember->member());
        static::assertInstanceOf(Artist::class, $artistMember->member()->first());
    }
}
