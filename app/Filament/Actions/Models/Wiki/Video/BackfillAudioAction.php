<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Wiki\Video;

use App\Concerns\Filament\Actions\Models\Wiki\Video\BackfillAudioActionTrait;
use App\Filament\Actions\BaseAction;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class BackfillAudioAction.
 */
class BackfillAudioAction extends BaseAction implements ShouldQueue
{
    use BackfillAudioActionTrait;
}
