<?php

declare(strict_types=1);

namespace App\Events\Wiki\Artist;

use App\Contracts\Events\CascadesDeletesEvent;
use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\ArtistResource as ArtistFilament;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song\Membership;
use App\Models\Wiki\Song\Performance;

/**
 * @extends WikiDeletedEvent<Artist>
 */
class ArtistDeleted extends WikiDeletedEvent implements CascadesDeletesEvent
{
    protected function getDiscordMessageDescription(): string
    {
        return "Artist '**{$this->getModel()->getName()}**' has been deleted.";
    }

    protected function getNotificationMessage(): string
    {
        return "Artist '{$this->getModel()->getName()}' has been deleted. It will be automatically pruned in one week. Please review.";
    }

    protected function getFilamentNotificationUrl(): string
    {
        return ArtistFilament::getUrl('view', ['record' => $this->getModel()]);
    }

    public function cascadeDeletes(): void
    {
        $this->getModel()->load([
            Artist::RELATION_PERFORMANCES,
            Artist::RELATION_MEMBERSHIPS_PERFORMANCES,
            Artist::RELATION_GROUPSHIPS_PERFORMANCES,
        ]);

        $this->getModel()->performances->each(fn (Performance $performance) => $performance->delete());

        $this->getModel()->memberships->each(function (Membership $membership): void {
            $membership->performances->each(fn (Performance $performance) => $performance->delete());

            $membership->delete();
        });

        $this->getModel()->groupships->each(function (Membership $membership): void {
            $membership->performances->each(fn (Performance $performance) => $performance->delete());

            $membership->delete();
        });
    }
}
