<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Wiki\Audio;

use App\Concerns\Filament\Actions\Models\Wiki\Audio\AttachAudioToRelatedVideosActionTrait;
use App\Filament\Actions\BaseAction;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class AttachAudioToRelatedVideosAction.
 */
class AttachAudioToRelatedVideosAction extends BaseAction implements ShouldQueue
{
    use AttachAudioToRelatedVideosActionTrait;
}
