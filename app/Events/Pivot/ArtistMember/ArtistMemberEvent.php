<?php

declare(strict_types=1);

namespace App\Events\Pivot\ArtistMember;

use App\Models\Wiki\Artist;
use App\Pivots\ArtistMember;

/**
 * Class ArtistMemberEvent.
 */
abstract class ArtistMemberEvent
{
    /**
     * The artist that this artist member belongs to.
     *
     * @var Artist
     */
    protected Artist $artist;

    /**
     * The member that this artist member belongs to.
     *
     * @var Artist
     */
    protected Artist $member;

    /**
     * Create a new event instance.
     *
     * @param  ArtistMember  $artistMember
     * @return void
     */
    public function __construct(ArtistMember $artistMember)
    {
        $this->artist = $artistMember->artist;
        $this->member = $artistMember->member;
    }

    /**
     * Get the artist that this artist member belongs to.
     *
     * @return Artist
     */
    public function getArtist(): Artist
    {
        return $this->artist;
    }

    /**
     * Get the member that this artist member belongs to.
     *
     * @return Artist
     */
    public function getMember(): Artist
    {
        return $this->member;
    }
}
