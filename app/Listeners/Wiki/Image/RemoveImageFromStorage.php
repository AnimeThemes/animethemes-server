<?php

declare(strict_types=1);

namespace App\Listeners\Wiki\Image;

use App\Events\Wiki\Image\ImageDeleting;
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
     * @param  ImageDeleting  $event
     * @return void
     */
    public function handle(ImageDeleting $event)
    {
        $image = $event->getImage();

        if ($image->isForceDeleting()) {
            Storage::disk('images')->delete($image->path);
        }
    }
}
