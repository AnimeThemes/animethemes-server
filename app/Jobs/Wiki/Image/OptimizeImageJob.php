<?php

declare(strict_types=1);

namespace App\Jobs\Wiki\Image;

use App\Actions\Models\Wiki\Image\OptimizeImageAction;
use App\Models\Wiki\Image;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\DeleteWhenMissingModels;
use Illuminate\Queue\Attributes\WithoutRelations;

#[DeleteWhenMissingModels]
#[WithoutRelations]
class OptimizeImageJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Image $image,
        protected string $extension = 'avif',
        protected ?int $width = null,
        protected ?int $height = null,
    ) {}

    public function handle(): void
    {
        $action = new OptimizeImageAction($this->image, $this->extension, $this->width, $this->height);

        $action->handle();
    }
}
