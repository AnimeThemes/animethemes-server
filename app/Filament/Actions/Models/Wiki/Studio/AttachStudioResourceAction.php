<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Wiki\Studio;

use App\Concerns\Filament\Actions\Models\Wiki\Studio\AttachStudioResourceActionTrait;
use App\Filament\Actions\Models\Wiki\AttachResourceAction;

/**
 * Class AttachStudioResourceAction.
 */
class AttachStudioResourceAction extends AttachResourceAction
{
    use AttachStudioResourceActionTrait;
}
