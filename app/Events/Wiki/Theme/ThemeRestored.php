<?php

declare(strict_types=1);

namespace App\Events\Wiki\Theme;

use App\Contracts\Events\CascadesRestoresEvent;
use App\Events\Base\Wiki\WikiRestoredEvent;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Theme;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\SoftDeletingScope;

/**
 * @extends WikiRestoredEvent<Theme>
 */
class ThemeRestored extends WikiRestoredEvent implements CascadesRestoresEvent
{
    public function cascadeRestores(): void
    {
        $theme = $this->getModel();

        $theme->animethemeentries()->withoutGlobalScope(SoftDeletingScope::class)->get()->each(function (Entry $entry): void {
            Entry::withoutEvents(function () use ($entry): void {
                $entry->restore();
                $entry->searchable();
                $entry->videos->each(fn (Video $video) => $video->searchable());
            });
        });
    }
}
