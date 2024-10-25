<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Models\Wiki\Studio;

use App\Concerns\Filament\Actions\Models\Wiki\Studio\BackfillStudioActionTrait;
use App\Filament\HeaderActions\BaseHeaderAction;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class BackfillStudioHeaderAction.
 */
class BackfillStudioHeaderAction extends BaseHeaderAction implements ShouldQueue
{
    use BackfillStudioActionTrait;
}
