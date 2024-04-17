<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Video\Pages;

use App\Filament\Resources\Base\BaseCreateResource;
use App\Filament\Resources\Wiki\Video;

/**
 * Class CreateVideo.
 */
class CreateVideo extends BaseCreateResource
{
    protected static string $resource = Video::class;
}
