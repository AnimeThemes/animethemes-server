<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Artist\Pages;

use App\Filament\Resources\Base\BaseCreateResource;
use App\Filament\Resources\Wiki\Artist;

/**
 * Class CreateArtist.
 */
class CreateArtist extends BaseCreateResource
{
    protected static string $resource = Artist::class;
}
