<?php

declare(strict_types=1);

namespace App\Pipes\Wiki;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Auth\User;
use App\Models\Wiki\ExternalResource;
use App\Pipes\BasePipe;
use Closure;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;

/**
 * Class BackfillResource.
 *
 * @template TModel of \App\Models\BaseModel
 * @extends BasePipe<TModel>
 */
abstract class BackfillResource extends BasePipe
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
        if ($this->relation()->getQuery()->where(ExternalResource::ATTRIBUTE_SITE, $this->getSite()->value)->exists()) {
            Log::info("{$this->label()} '{$this->getModel()->getName()}' already has Resource of Site '{$this->getSite()->value}'.");

            return $next($user);
        }

        $resource = $this->getResource();

        if ($resource !== null) {
            $this->attachResource($resource);
        }

        if ($this->relation()->getQuery()->where(ExternalResource::ATTRIBUTE_SITE, $this->getSite()->value)->doesntExist()) {
            $this->sendNotification(
                $user,
                "{$this->label()} '{$this->getModel()->getName()}' has no {$this->getSite()->description} Resource after backfilling. Please review."
            );
        }

        return $next($user);
    }

    /**
     * Get or Create Resource from response.
     *
     * @param  int  $id
     * @param  string|null  $slug
     * @return ExternalResource
     */
    abstract protected function getOrCreateResource(int $id, string $slug = null): ExternalResource;

    /**
     * Attach External Resource to model.
     *
     * @param  ExternalResource  $resource
     * @return void
     */
    abstract protected function attachResource(ExternalResource $resource): void;

    /**
     * Get the site to backfill.
     *
     * @return ResourceSite
     */
    abstract protected function getSite(): ResourceSite;

    /**
     * Query third-party APIs to find Resource mapping.
     *
     * @return ExternalResource|null
     *
     * @throws RequestException
     */
    abstract protected function getResource(): ?ExternalResource;
}
