<?php

declare(strict_types=1);

namespace Tests\Unit\Pivots\Wiki;

use App\Models\Wiki\Artist;
use App\Pivots\Wiki\ArtistMember;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

/**
 * Class ArtistMemberTest.
 */
class ArtistMemberTest extends TestCase
{
    /**
     * An ArtistMember shall belong to an Artist.
     *
     * @return void
     */
    public function test_artist(): void
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
    public function test_member(): void
    {
        $artistMember = ArtistMember::factory()
            ->for(Artist::factory(), 'artist')
            ->for(Artist::factory(), 'member')
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $artistMember->member());
        static::assertInstanceOf(Artist::class, $artistMember->member()->first());
    }
}
