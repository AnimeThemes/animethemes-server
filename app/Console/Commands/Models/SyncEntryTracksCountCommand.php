<?php

declare(strict_types=1);

namespace App\Console\Commands\Models;

use App\Actions\Models\Aggregate\SyncEntryTracksCountAction;
use App\Console\Commands\BaseCommand;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

#[Signature('entry:sync-tracks-count')]
#[Description('Synchronizes tracks in the anime_theme_entries table')]
class SyncEntryTracksCountCommand extends BaseCommand
{
    public function handle(): int
    {
        $action = new SyncEntryTracksCountAction();

        $result = $action->handle();

        $result->toLog();
        $result->toConsole($this);

        return $result->hasFailed() ? 1 : 0;
    }

    protected function validator(): Validator
    {
        return ValidatorFacade::make($this->options(), []);
    }
}
