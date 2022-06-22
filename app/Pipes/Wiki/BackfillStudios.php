<?php

declare(strict_types=1);

namespace App\Pipes\Wiki;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Auth\User;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use App\Pipes\BasePipe;
use App\Pivots\StudioResource;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class BackfillStudios.
 *
 * @template TModel of \App\Models\BaseModel
 * @extends BasePipe<TModel>
 */
abstract class BackfillStudios extends BasePipe
{
    /**
     * Handle an incoming request.
     *
     * @param  User  $user
     * @param  Closure(User): mixed  $next
     * @return mixed
     *
     * @throws RequestException
     */
    public function handle(User $user, Closure $next): mixed
    {
        if ($this->relation()->getQuery()->exists()) {
            Log::info("{$this->label()} '{$this->getModel()->getName()}' already has Studios.");

            return $next($user);
        }

        $studios = $this->getStudios();

        if (! empty($studios)) {
            $this->attachStudios($studios);
        }

        if ($this->relation()->getQuery()->doesntExist()) {
            $this->sendNotification($user, "{$this->label()} '{$this->getModel()->getName()}' has no Studios after backfilling. Please review.");
        }

        return $next($user);
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
            ->where(DB::raw("lower($column)"), Str::lower($name))
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
            ->whereHas(ExternalResource::RELATION_STUDIOS, fn (Builder $studioQuery) => $studioQuery->whereKey($studio))
            ->first();

        if (! $studioResource instanceof ExternalResource) {
            Log::info("Creating studio resource with site '$site->value' and id '$id'");

            $studioResource = ExternalResource::query()->create([
                ExternalResource::ATTRIBUTE_EXTERNAL_ID => $id,
                ExternalResource::ATTRIBUTE_LINK => ResourceSite::formatStudioResourceLink($site, $id),
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
