<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\List\Playlist;

use App\Actions\Models\List\Playlist\FixPlaylistAction as FixPlaylist;
use App\Enums\Auth\Role;
use App\Filament\Actions\BaseAction;
use App\Models\List\Playlist;
use Illuminate\Support\Facades\Auth;

class FixPlaylistAction extends BaseAction
{
    public static function getDefaultName(): ?string
    {
        return 'fix-playlist';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.models.list.fix_playlist.name'));

        $this->visible(Auth::user()->hasRole(Role::ADMIN->value));

        $this->action(fn (Playlist $record, FixPlaylist $fix) => $fix->handle($record));
    }
}
