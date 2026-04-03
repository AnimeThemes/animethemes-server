<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Contracts\Events\CreateArtistSongEvent;

class CreateArtistSong
{
    public function handle(CreateArtistSongEvent $event): void
    {
        $event->createArtistSong();
    }
}
