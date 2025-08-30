<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Wiki\Anime\Theme\Entry;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Actions\Models\Wiki\AttachResourceAction;

class AttachEntryResourceAction extends AttachResourceAction
{
    public static function getDefaultName(): ?string
    {
        return 'attach-entry-resource';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->sites([
            ResourceSite::YOUTUBE,
        ]);
    }
}
