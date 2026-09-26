<?php

declare(strict_types=1);

namespace App\Events\Wiki\ThemeStaff;

use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\ThemeStaffResource;
use App\Models\Wiki\ThemeStaff;

/**
 * @extends WikiDeletedEvent<ThemeStaff>
 */
class ThemeStaffDeleted extends WikiDeletedEvent
{
    protected function getDiscordMessageDescription(): string
    {
        $staff = $this->getModel();

        $artistName = $staff->alias ?? $staff->artist->getName();

        return "Theme '**{$staff->theme->getName()}**' has been detached from Artist '**{$artistName}**' as '**{$staff->role}**'.";
    }

    protected function getFilamentNotificationUrl(): string
    {
        return ThemeStaffResource::getUrl('view', ['record' => $this->getModel()]);
    }
}
