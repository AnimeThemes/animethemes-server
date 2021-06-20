<?php

declare(strict_types=1);

namespace App\Events\Pivot\ArtistSong;

use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Pivots\ArtistSong;

/**
 * Class ArtistSongEvent.
 */
abstract class ArtistSongEvent
{
    /**
     * The artist that this artist song belongs to.
     *
     * @var Artist
     */
    protected Artist $artist;

    /**
     * The song that this artist song belongs to.
     *
     * @var Song
     */
    protected Song $song;

    /**
     * Create a new event instance.
     *
     * @param ArtistSong $artistSong
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
     * @return Artist
     */
    public function getArtist(): Artist
    {
        return $this->artist;
    }

    /**
     * Get the song that this artist song belongs to.
     *
     * @return Song
     */
    public function getSong(): Song
    {
        return $this->song;
    }
}
