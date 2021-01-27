<?php

namespace App\Events\Pivot\ArtistSong;

use App\Pivots\ArtistSong;

abstract class ArtistSongEvent
{
    /**
     * The artist that this artist song belongs to.
     *
     * @var \App\Models\Artist
     */
    protected $artist;

    /**
     * The song that this artist song belongs to.
     *
     * @var \App\Models\Song
     */
    protected $song;

    /**
     * Create a new event instance.
     *
     * @param \App\Pivots\ArtistSong $artistSong
     * @return void
     */
    public function __construct(ArtistSong $artistSong)
    {
        $this->artist = $artistSong->artist;
        $this->song = $artistSong->song;
    }

    /**
     * Get the artist that this artist song belongs to.
     *
     * @return \App\Models\Artist
     */
    public function getArtist()
    {
        return $this->artist;
    }

    /**
     * Get the song that this artist song belongs to.
     *
     * @return \App\Models\Song
     */
    public function getSong()
    {
        return $this->song;
    }
}
