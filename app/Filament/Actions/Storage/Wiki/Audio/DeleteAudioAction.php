<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Wiki\Audio;

use App\Concerns\Filament\Actions\Storage\Wiki\Audio\DeleteAudioActionTrait;
use App\Filament\Actions\Storage\Base\DeleteAction;

/**
 * Class DeleteAudioAction.
 */
class DeleteAudioAction extends DeleteAction
{
    use DeleteAudioActionTrait;
}
