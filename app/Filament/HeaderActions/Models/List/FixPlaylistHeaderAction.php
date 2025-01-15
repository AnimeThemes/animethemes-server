<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Models\List;

use App\Actions\Models\List\Playlist\FixPlaylistAction;
use App\Filament\HeaderActions\BaseHeaderAction;
use App\Models\List\Playlist;

/**
 * Class FixPlaylistHeaderAction.
 */
class FixPlaylistHeaderAction extends BaseHeaderAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.models.list.fix_playlist.name'));

        $this->authorize('update', $this->getRecord());

        $this->action(fn (Playlist $record, FixPlaylistAction $action) => $action->handle($record));
    }
}