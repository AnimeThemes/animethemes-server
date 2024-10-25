<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Wiki\Anime;

use App\Concerns\Filament\Actions\Models\Wiki\Anime\BackfillAnimeActionTrait;
use App\Filament\Actions\BaseAction;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class BackfillAnimeAction.
 */
class BackfillAnimeAction extends BaseAction implements ShouldQueue
{
    use BackfillAnimeActionTrait;
}
