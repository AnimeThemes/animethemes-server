<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Models\Wiki;

use App\Concerns\Filament\Actions\Models\Wiki\AttachImageActionTrait;
use App\Filament\HeaderActions\BaseHeaderAction;

/**
 * Class AttachImageHeaderAction.
 */
class AttachImageHeaderAction extends BaseHeaderAction
{
    use AttachImageActionTrait;
}
