<?php

declare(strict_types=1);

namespace App\Events\Wiki\Artist;

use App\Contracts\Events\CascadesRestoresEvent;
use App\Events\Base\Wiki\WikiRestoredEvent;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song\Membership;
use App\Models\Wiki\Song\Performance;
use Illuminate\Database\Eloquent\SoftDeletingScope;

/**
 * @extends WikiRestoredEvent<Artist>
 */
class ArtistRestored extends WikiRestoredEvent implements CascadesRestoresEvent
{
    protected function getDiscordMessageDescription(): string
    {
        return "Artist '**{$this->getModel()->getName()}**' has been restored.";
    }

    public function cascadeRestores(): void
    {
        $this->getModel()->withoutGlobalScope(SoftDeletingScope::class)->with([
            Artist::RELATION_PERFORMANCES,
            Artist::RELATION_MEMBERSHIPS_PERFORMANCES,
            Artist::RELATION_GROUPSHIPS_PERFORMANCES,
        ]);

        $this->getModel()
            ->performances()
            ->withoutGlobalScope(SoftDeletingScope::class)
            ->get()
            ->each(fn (Performance $performance): bool => $performance->restore());

        $this->getModel()
            ->memberships()
            ->withoutGlobalScope(SoftDeletingScope::class)
            ->get()
            ->each(function (Membership $membership): void {
                $membership->performances()
                    ->withoutGlobalScope(SoftDeletingScope::class)
                    ->get()
                    ->each(fn (Performance $performance): bool => $performance->restore());

                $membership->restore();
            });

        $this->getModel()
            ->groupships()
            ->withoutGlobalScope(SoftDeletingScope::class)
            ->get()
            ->each(function (Membership $membership): void {
                $membership->performances()
                    ->withoutGlobalScope(SoftDeletingScope::class)
                    ->get()
                    ->each(fn (Performance $performance): bool => $performance->restore());

                $membership->restore();
            });
    }
}
