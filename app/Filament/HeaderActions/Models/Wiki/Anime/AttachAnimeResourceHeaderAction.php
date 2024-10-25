<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Models\Wiki\Anime;

use App\Concerns\Filament\Actions\Models\Wiki\Anime\AttachAnimeResourceActionTrait;
use App\Filament\HeaderActions\Models\Wiki\AttachResourceHeaderAction;

/**
 * Class AttachAnimeResourceHeaderAction.
 */
class AttachAnimeResourceHeaderAction extends AttachResourceHeaderAction
{
    use AttachAnimeResourceActionTrait;
}
