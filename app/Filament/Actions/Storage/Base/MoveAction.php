<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Base;

use App\Concerns\Filament\Actions\Storage\Base\MoveActionTrait;
use App\Contracts\Storage\InteractsWithDisk;
use App\Filament\Actions\Storage\StorageAction;

/**
 * Class MoveAction.
 */
abstract class MoveAction extends StorageAction implements InteractsWithDisk
{
    use MoveActionTrait;
}
