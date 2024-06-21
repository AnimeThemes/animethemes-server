<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki;

use App\Actions\ActionResult;
use App\Actions\Models\BackfillAction;
use App\Enums\Actions\ActionStatus;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\StudioResource;
use Exception;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class BackfillStudiosAction.
 *
 * @template TModel of \App\Models\BaseModel
 *
 * @extends BackfillAction<TModel>
 */
abstract class BackfillStudiosAction extends BackfillAction
{
    /**
     * Handle action.
     *
     * @return ActionResult
     *
     * @throws Exception
     */
    public function handle(): ActionResult
    {
        try {
            DB::beginTransaction();

            if ($this->relation()->getQuery()->exists()) {
                DB::rollback();
                Log::info("{$this->label()} '{$this->getModel()->getName()}' already has Studios.");

                return new ActionResult(ActionStatus::SKIPPED);
            }

            $studios = $this->getStudios();

            if (! empty($studios)) {
                $this->attachStudios($studios);
            }

            if ($this->relation()->getQuery()->doesntExist()) {
                DB::rollback();
                return new ActionResult(
                    ActionStatus::FAILED,
                    "{$this->label()} '{$this->getModel()->getName()}' has no Studios after backfilling. Please review."
                );
            }

            DB::commit();
        } catch (Exception $e) {
            Log::error($e->getMessage());

            DB::rollBack();

            throw $e;
        }

        return new ActionResult(ActionStatus::PASSED);
    }

    /**
     * Get or create Studio from name (case-insensitive).
     *
     * @param  string  $name
     * @return Studio
     */
    protected function getOrCreateStudio(string $name): Studio
    {
        $column = Studio::ATTRIBUTE_NAME;
        $studio = Studio::query()
            ->whereRaw("lower($column) = ?", Str::lower($name))
            ->first();

        if (! $studio instanceof Studio) {
            Log::info("Creating studio '$name'");

            $studio = Studio::query()->create([
                Studio::ATTRIBUTE_NAME => $name,
                Studio::ATTRIBUTE_SLUG => Str::slug($name, '_'),
            ]);
        }

        return $studio;
    }

    /**
     * Ensure Studio has Resource.
     *
     * @param  Studio  $studio
     * @param  ResourceSite  $site
     * @param  int  $id
     * @return void
     */
    protected function ensureStudioHasResource(Studio $studio, ResourceSite $site, int $id): void
    {
        $studioResource = ExternalResource::query()
            ->where(ExternalResource::ATTRIBUTE_SITE, $site->value)
            ->where(ExternalResource::ATTRIBUTE_EXTERNAL_ID, $id)
            ->where(ExternalResource::ATTRIBUTE_LINK, $site->formatResourceLink(Studio::class, $id))
            ->first();

        if (! $studioResource instanceof ExternalResource) {
            Log::info("Creating studio resource with site '$site->value' and id '$id'");

            $studioResource = ExternalResource::query()->create([
                ExternalResource::ATTRIBUTE_EXTERNAL_ID => $id,
                ExternalResource::ATTRIBUTE_LINK =>$site->formatResourceLink(Studio::class, $id),
                ExternalResource::ATTRIBUTE_SITE => $site->value,
            ]);
        }

        if (StudioResource::query()
            ->where($studio->getKeyName(), $studio->getKey())
            ->where($studioResource->getKeyName(), $studioResource->getKey())
            ->doesntExist()
        ) {
            Log::info("Attaching resource '$studioResource->link' to studio '{$studio->getName()}'");
            $studioResource->studios()->attach($studio);
        }
    }

    /**
     * Attach Studios.
     *
     * @param  Studio[]  $studios
     * @return void
     */
    abstract protected function attachStudios(array $studios): void;

    /**
     * Query third-party API for Studios.
     *
     * @return Studio[]
     *
     * @throws RequestException
     */
    abstract protected function getStudios(): array;
}
