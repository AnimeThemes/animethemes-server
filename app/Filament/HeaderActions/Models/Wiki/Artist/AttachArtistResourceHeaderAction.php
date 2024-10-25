<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Models\Wiki\Artist;

use App\Concerns\Filament\Actions\Models\Wiki\Artist\AttachArtistResourceActionTrait;
use App\Filament\HeaderActions\Models\Wiki\AttachResourceHeaderAction;

/**
 * Class AttachArtistResourceHeaderAction.
 */
class AttachArtistResourceHeaderAction extends AttachResourceHeaderAction
{
    use AttachArtistResourceActionTrait;
}
