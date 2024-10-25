<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Wiki\Studio;

use App\Concerns\Filament\Actions\Models\Wiki\Studio\BackfillStudioActionTrait;
use App\Filament\Actions\BaseAction;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class BackfillStudioAction.
 */
class BackfillStudioAction extends BaseAction implements ShouldQueue
{
    use BackfillStudioActionTrait;
}
