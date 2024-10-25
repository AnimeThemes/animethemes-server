<?php

declare(strict_types=1);

namespace App\Filament\Actions\Discord;

use App\Concerns\Filament\Actions\Discord\DiscordThreadActionTrait;
use App\Filament\Actions\BaseAction;

/**
 * Class DiscordThreadAction.
 */
class DiscordThreadAction extends BaseAction
{
    use DiscordThreadActionTrait;
}
