<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Storage\Base;

use App\Concerns\Filament\Actions\Storage\Base\DeleteActionTrait;
use App\Filament\HeaderActions\Storage\StorageHeaderAction;

/**
 * Class DeleteHeaderAction.
 */
abstract class DeleteHeaderAction extends StorageHeaderAction
{
    use DeleteActionTrait;
}
