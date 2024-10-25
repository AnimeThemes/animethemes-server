<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Storage\Base;

use App\Concerns\Filament\Actions\Storage\Base\MoveActionTrait;
use App\Contracts\Storage\InteractsWithDisk;
use App\Filament\HeaderActions\Storage\StorageHeaderAction;

/**
 * Class MoveHeaderAction.
 */
abstract class MoveHeaderAction extends StorageHeaderAction implements InteractsWithDisk
{
    use MoveActionTrait;
}
