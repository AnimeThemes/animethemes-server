<?php

namespace App\Listeners\Image;

use App\Events\Image\ImageEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Storage;

class RemoveImageFromStorage implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  \App\Events\Image\ImageEvent  $event
     * @return void
     */
    public function handle(ImageEvent $event)
    {
        $image = $event->getImage();

        Storage::disk('images')->delete($image->path);
    }
}
