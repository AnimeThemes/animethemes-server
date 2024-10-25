<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Discord;

use App\Concerns\Filament\Actions\Discord\DiscordThreadActionTrait;
use App\Filament\HeaderActions\BaseHeaderAction;

/**
 * Class DiscordThreadAction.
 */
class DiscordThreadHeaderAction extends BaseHeaderAction
{
    use DiscordThreadActionTrait;
}
