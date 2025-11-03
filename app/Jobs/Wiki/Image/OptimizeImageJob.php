<?php

declare(strict_types=1);

namespace App\Jobs\Wiki\Image;

use App\Actions\Models\Wiki\Image\OptimizeImageAction;
use App\Models\Wiki\Image;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class OptimizeImageJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public function __construct(public readonly Image $image) {}

    public function handle(): void
    {
        $action = new OptimizeImageAction($this->image, 'avif');

        $action->handle();
    }
}
