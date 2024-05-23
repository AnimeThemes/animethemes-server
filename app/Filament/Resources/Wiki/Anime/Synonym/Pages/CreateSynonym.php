<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\Synonym\Pages;

use App\Filament\Resources\Base\BaseCreateResource;
use App\Filament\Resources\Wiki\Anime\Synonym;

/**
 * Class CreateSynonym.
 */
class CreateSynonym extends BaseCreateResource
{
    protected static string $resource = Synonym::class;
}
