<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage;

use App\Concerns\Filament\Actions\Storage\StorageActionTrait;
use App\Filament\Actions\BaseAction;

/**
 * Class StorageAction.
 */
abstract class StorageAction extends BaseAction
{
    use StorageActionTrait;
}
