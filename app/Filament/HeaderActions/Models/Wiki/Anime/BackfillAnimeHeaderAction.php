<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Models\Wiki\Anime;

use App\Concerns\Filament\Actions\Models\Wiki\Anime\BackfillAnimeActionTrait;
use App\Filament\HeaderActions\BaseHeaderAction;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class BackfillAnimeHeaderAction.
 */
class BackfillAnimeHeaderAction extends BaseHeaderAction implements ShouldQueue
{
    use BackfillAnimeActionTrait;
}
