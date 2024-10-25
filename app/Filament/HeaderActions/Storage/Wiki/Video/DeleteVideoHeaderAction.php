<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Storage\Wiki\Video;

use App\Concerns\Filament\Actions\Storage\Wiki\Video\DeleteVideoActionTrait;
use App\Filament\HeaderActions\Storage\Base\DeleteHeaderAction;

/**
 * Class DeleteVideoHeaderAction.
 */
class DeleteVideoHeaderAction extends DeleteHeaderAction
{
    use DeleteVideoActionTrait;
}
