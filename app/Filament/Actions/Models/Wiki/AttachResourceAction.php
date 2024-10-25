<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Wiki;

use App\Concerns\Filament\Actions\Models\Wiki\AttachResourceActionTrait;
use App\Filament\Actions\BaseAction;

/**
 * Class AttachResourceAction.
 */
abstract class AttachResourceAction extends BaseAction
{
    use AttachResourceActionTrait;
}
