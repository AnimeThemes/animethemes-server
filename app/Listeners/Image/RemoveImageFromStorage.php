<?php

declare(strict_types=1);

namespace App\Listeners\Image;

use App\Events\Image\ImageEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Storage;

/**
 * Class RemoveImageFromStorage.
 */
class RemoveImageFromStorage implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param ImageEvent $event
     * @return void
     */
    public function handle(ImageEvent $event)
    {
        $image = $event->getImage();

        if ($image->isForceDeleting()) {
            Storage::disk('images')->delete($image->path);
        }
    }
}
