<?php

declare(strict_types=1);

namespace App\Console\Commands\Models;

use App\Actions\Models\List\Playlist\FixPlaylistAction;
use App\Console\Commands\BaseCommand;
use App\Models\List\Playlist;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

#[Signature('playlist:fix {playlistId}')]
#[Description('Fix playlist tracks to remove cycles')]
class PlaylistFixCommand extends BaseCommand
{
    public function handle(): int
    {
        $playlistId = $this->argument('playlistId');

        /** @var Playlist|null $playlist */
        $playlist = Playlist::query()->find($playlistId);

        if (! $playlist) {
            $this->error("Playlist with id $playlistId not found.");

            return 0;
        }

        return new FixPlaylistAction()->handle($playlist, $this);
    }

    protected function validator(): Validator
    {
        return ValidatorFacade::make($this->options(), [
            'playlistId' => ['int', 'required'],
        ]);
    }
}
