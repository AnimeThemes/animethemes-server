<?php

namespace App\Events\Pivot\ArtistMember;

use App\Pivots\ArtistMember;

abstract class ArtistMemberEvent
{
    /**
     * The artist that this artist member belongs to.
     *
     * @var \App\Models\Artist
     */
    protected $artist;

    /**
     * The member that this artist member belongs to.
     *
     * @var \App\Models\Artist
     */
    protected $member;

    /**
     * Create a new event instance.
     *
     * @param @var \App\Pivots\ArtistMember $artistMember
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
     * @return \App\Models\Artist
     */
    public function getArtist()
    {
        return $this->artist;
    }

    /**
     * Get the member that this artist member belongs to.
     *
     * @return \App\Models\Artist
     */
    public function getMember()
    {
        return $this->member;
    }
}
