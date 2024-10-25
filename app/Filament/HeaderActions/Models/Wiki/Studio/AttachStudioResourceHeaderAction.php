<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Models\Wiki\Studio;

use App\Concerns\Filament\Actions\Models\Wiki\Studio\AttachStudioResourceActionTrait;
use App\Filament\HeaderActions\Models\Wiki\AttachResourceHeaderAction;

/**
 * Class AttachStudioResourceHeaderAction.
 */
class AttachStudioResourceHeaderAction extends AttachResourceHeaderAction
{
    use AttachStudioResourceActionTrait;
}
