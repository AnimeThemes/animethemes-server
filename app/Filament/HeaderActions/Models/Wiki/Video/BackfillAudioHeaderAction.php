<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Models\Wiki\Video;

use App\Concerns\Filament\Actions\Models\Wiki\Video\BackfillAudioActionTrait;
use App\Filament\HeaderActions\BaseHeaderAction;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class BackfillAudioHeaderAction.
 */
class BackfillAudioHeaderAction extends BaseHeaderAction implements ShouldQueue
{
    use BackfillAudioActionTrait;
}
