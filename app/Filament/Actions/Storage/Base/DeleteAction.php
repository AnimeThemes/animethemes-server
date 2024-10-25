<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Base;

use App\Concerns\Filament\Actions\Storage\Base\DeleteActionTrait;
use App\Filament\Actions\Storage\StorageAction;

/**
 * Class DeleteAction.
 */
abstract class DeleteAction extends StorageAction
{
    use DeleteActionTrait;
}
