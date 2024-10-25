<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Wiki\Video;

use App\Concerns\Filament\Actions\Storage\Wiki\Video\DeleteVideoActionTrait;
use App\Filament\Actions\Storage\Base\DeleteAction;

/**
 * Class DeleteVideoAction.
 */
class DeleteVideoAction extends DeleteAction
{
    use DeleteVideoActionTrait;
}
