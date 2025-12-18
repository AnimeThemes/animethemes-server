<?php

declare(strict_types=1);

namespace App\Jobs\Wiki\Image;

use App\Actions\Models\Wiki\Image\OptimizeImageAction;
use App\Models\Wiki\Image;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\Attributes\DeleteWhenMissingModels;
use Illuminate\Queue\Attributes\WithoutRelations;
use Illuminate\Queue\InteractsWithQueue;

#[DeleteWhenMissingModels]
#[WithoutRelations]
class OptimizeImageJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public function __construct(
        public readonly Image $image,
        public readonly string $extension = 'avif',
        public readonly ?int $width = null,
        public readonly ?int $height = null,
    ) {}

    public function handle(): void
    {
        $action = new OptimizeImageAction($this->image, $this->extension, $this->width, $this->height);

        $action->handle();
    }
}
