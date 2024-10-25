<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Storage;

use App\Concerns\Filament\Actions\Storage\StorageActionTrait;
use App\Filament\HeaderActions\BaseHeaderAction;

/**
 * Class StorageHeaderAction.
 */
abstract class StorageHeaderAction extends BaseHeaderAction
{
    use StorageActionTrait;
}
