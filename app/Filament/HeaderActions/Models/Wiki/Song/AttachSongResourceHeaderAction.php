<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Models\Wiki\Song;

use App\Concerns\Filament\Actions\Models\Wiki\Song\AttachSongResourceActionTrait;
use App\Filament\HeaderActions\Models\Wiki\AttachResourceHeaderAction;

/**
 * Class AttachSongResourceHeaderAction.
 */
class AttachSongResourceHeaderAction extends AttachResourceHeaderAction
{
    use AttachSongResourceActionTrait;
}
