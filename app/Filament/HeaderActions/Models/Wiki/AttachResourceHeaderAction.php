<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Models\Wiki;

use App\Concerns\Filament\Actions\Models\Wiki\AttachResourceActionTrait;
use App\Filament\HeaderActions\BaseHeaderAction;

/**
 * Class AttachResourceHeaderAction.
 */
abstract class AttachResourceHeaderAction extends BaseHeaderAction
{
    use AttachResourceActionTrait;
}
