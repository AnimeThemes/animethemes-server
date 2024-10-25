<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Wiki\Song;

use App\Concerns\Filament\Actions\Models\Wiki\Song\AttachSongResourceActionTrait;
use App\Filament\Actions\Models\Wiki\AttachResourceAction;

/**
 * Class AttachSongResourceAction.
 */
class AttachSongResourceAction extends AttachResourceAction
{
    use AttachSongResourceActionTrait;
}
