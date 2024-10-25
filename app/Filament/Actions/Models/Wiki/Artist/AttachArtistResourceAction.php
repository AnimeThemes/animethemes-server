<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Wiki\Artist;

use App\Concerns\Filament\Actions\Models\Wiki\Artist\AttachArtistResourceActionTrait;
use App\Filament\Actions\Models\Wiki\AttachResourceAction;

/**
 * Class AttachArtistResourceAction.
 */
class AttachArtistResourceAction extends AttachResourceAction
{
    use AttachArtistResourceActionTrait;
}
