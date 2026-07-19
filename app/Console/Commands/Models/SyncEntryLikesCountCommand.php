<?php

declare(strict_types=1);

namespace App\Console\Commands\Models;

use App\Actions\Models\Aggregate\SyncLikesCountAction;
use App\Console\Commands\BaseCommand;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

#[Signature('entry:sync-likes-count')]
#[Description('Synchronizes likes in the anime_theme_entries table')]
class SyncEntryLikesCountCommand extends BaseCommand
{
    public function handle(): int
    {
        $action = new SyncLikesCountAction();

        $result = $action->handle(AnimeThemeEntry::class);

        $result->toLog();
        $result->toConsole($this);

        return $result->hasFailed() ? 1 : 0;
    }

    protected function validator(): Validator
    {
        return ValidatorFacade::make($this->options(), []);
    }
}
