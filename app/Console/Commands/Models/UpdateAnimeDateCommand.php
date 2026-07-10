<?php

declare(strict_types=1);

namespace App\Console\Commands\Models;

use App\Actions\ActionResult;
use App\Actions\Models\Wiki\Anime\AnimeDateAction;
use App\Console\Commands\BaseCommand;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Support\Sleep;

#[Signature('anime:update-dates')]
#[Description('Updates anime dates')]
class UpdateAnimeDateCommand extends BaseCommand
{
    public function handle(): int
    {
        $failed = false;

        Anime::query()
            ->where(function (Builder $query): void {
                $query
                    ->whereNull(Anime::ATTRIBUTE_START_DATE)
                    ->orWhereRaw('RIGHT('.Anime::ATTRIBUTE_START_DATE.', 2) = ?', ['00'])
                    ->orWhereNull(Anime::ATTRIBUTE_END_DATE)
                    ->orWhereRaw('RIGHT('.Anime::ATTRIBUTE_END_DATE.', 2) = ?', ['00']);
            })
            ->with([
                Anime::RELATION_RESOURCES => fn (Relation $query) => $query->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANILIST->value),
            ])
            ->chunkById(20, function (Collection $anime) use (&$failed) {
                $ids = $anime->pluck(Anime::ATTRIBUTE_ID)->values()->implode(', ');

                $this->info('Anime IDs: '.$ids);

                $action = new AnimeDateAction();

                $result = Anime::withoutEvents(fn () => Anime::withoutTimestamps(fn (): ActionResult => $action->handle($anime)));

                if ($result->hasFailed()) {
                    $this->error('Action failed: '.$result->getMessage());

                    $failed = true;

                    return false;
                }

                Sleep::sleep(5);
            });

        return $failed ? 1 : 0;
    }

    protected function validator(): Validator
    {
        return ValidatorFacade::make($this->options(), []);
    }
}
