<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Storage\Wiki\Audio;

use App\Concerns\Filament\Actions\Storage\Wiki\Audio\MoveAudioActionTrait;
use App\Filament\HeaderActions\Storage\Base\MoveHeaderAction;

/**
 * Class MoveAudioHeaderAction.
 */
class MoveAudioHeaderAction extends MoveHeaderAction
{
    use MoveAudioActionTrait;
}
