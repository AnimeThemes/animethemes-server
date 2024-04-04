<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\Pages;

use App\Filament\Resources\Wiki\Anime;
use Filament\Resources\Pages\CreateRecord;

class CreateAnime extends CreateRecord
{
    protected static string $resource = Anime::class;
}
