<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Models\Wiki\Audio;

use App\Concerns\Filament\Actions\Models\Wiki\Audio\AttachAudioToRelatedVideosActionTrait;
use App\Filament\HeaderActions\BaseHeaderAction;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class AttachAudioToRelatedVideosHeaderAction.
 */
class AttachAudioToRelatedVideosHeaderAction extends BaseHeaderAction implements ShouldQueue
{
    use AttachAudioToRelatedVideosActionTrait;
}
