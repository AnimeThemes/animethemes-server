<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Storage\Wiki\Audio;

use App\Concerns\Filament\Actions\Storage\Wiki\Audio\DeleteAudioActionTrait;
use App\Filament\HeaderActions\Storage\Base\DeleteHeaderAction;

/**
 * Class DeleteAudioHeaderAction.
 */
class DeleteAudioHeaderAction extends DeleteHeaderAction
{
    use DeleteAudioActionTrait;
}
