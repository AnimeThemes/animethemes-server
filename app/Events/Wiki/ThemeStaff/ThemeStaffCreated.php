<?php

declare(strict_types=1);

namespace App\Events\Wiki\ThemeStaff;

use App\Contracts\Events\CreateSynonymEvent;
use App\Events\Base\Wiki\WikiCreatedEvent;
use App\Models\Wiki\Synonym;
use App\Models\Wiki\ThemeStaff;

/**
 * @extends WikiCreatedEvent<ThemeStaff>
 */
class ThemeStaffCreated extends WikiCreatedEvent implements CreateSynonymEvent
{
    protected function getDiscordMessageDescription(): string
    {
        $staff = $this->getModel();

        $artistName = $staff->alias ?? $staff->artist->getName();

        return "Theme '**{$staff->theme->getName()}**' has been attached to Artist '**{$artistName}**' as '**{$staff->role}**'.";
    }

    public function createSynonym(): void
    {
        $staff = $this->getModel();

        if (filled($staff->alias)) {
            $staff->artist->synonyms()->firstOrCreate([
                Synonym::ATTRIBUTE_TEXT => $staff->alias,
            ]);
        }
    }
}
