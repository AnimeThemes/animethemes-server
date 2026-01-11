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
use Illuminate\Queue\SerializesModels;

#[DeleteWhenMissingModels]
#[WithoutRelations]
class OptimizeImageJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

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
