<?php

declare(strict_types=1);

namespace App\Http\Middleware\Models\Pivot;

use App\Models\Auth\User;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class AuthorizesPivot
{
    /**
     * @param  Closure(Request): mixed  $next
     * @param  class-string  $foreignClass
     * @param  class-string  $relatedClass
     */
    public function handle(Request $request, Closure $next, string $foreignClass, string $foreignParameter, string $relatedClass, string $relatedParameter): mixed
    {
        /** @var Model $foreignModel */
        $foreignModel = $request->route($foreignParameter);

        /** @var Model $relatedModel */
        $relatedModel = $request->route($relatedParameter);

        $isAuthorized = match ($request->route()->getActionMethod()) {
            'index' => $this->forIndex($request->user(), $foreignClass, $relatedClass),
            'show' => $this->forShow($request->user(), $foreignModel, $relatedModel),
            'create', 'store' => $this->forStore($request->user(), $foreignModel, $relatedModel),
            'destroy' => $this->forDestroy($request->user(), $foreignModel, $relatedModel),
            'edit', 'update' => $this->forUpdate($request->user(), $foreignModel, $relatedModel),
            default => false,
        };

        if (! $isAuthorized) {
            abort(403);
        }

        return $next($request);
    }

    /**
     * Get the authorization to index.
     *
     * @param  class-string  $foreignClass
     * @param  class-string  $relatedClass
     */
    protected function forIndex(?User $user, string $foreignClass, string $relatedClass): bool
    {
        return Gate::forUser($user)->check('viewAny', $foreignClass)
            && Gate::forUser($user)->check('viewAny', $relatedClass);
    }

    /**
     * Get the authorization to show a pivot.
     */
    protected function forShow(?User $user, Model $foreignModel, Model $relatedModel): bool
    {
        return Gate::forUser($user)->check('view', $foreignModel)
            && Gate::forUser($user)->check('view', $relatedModel);
    }

    /**
     * Get the authorization to store a pivot.
     */
    protected function forStore(User $user, Model $foreignModel, Model $relatedModel): bool
    {
        $attachAny = Str::of('attachAny')
            ->append(Str::studly(class_basename($relatedModel)))
            ->__toString();

        $attach = Str::of('attach')
            ->append(Str::studly(class_basename($relatedModel)))
            ->__toString();

        return Gate::forUser($user)->any([$attach, $attachAny], [$foreignModel, $relatedModel]);
    }

    /**
     * Get the authorization to destroy a pivot.
     */
    protected function forDestroy(User $user, Model $foreignModel, Model $relatedModel): bool
    {
        $detachAny = Str::of('detachAny')
            ->append(Str::studly(class_basename($relatedModel)))
            ->__toString();

        $detach = Str::of('detach')
            ->append(Str::studly(class_basename($relatedModel)))
            ->__toString();

        return Gate::forUser($user)->any([$detach, $detachAny], [$foreignModel, $relatedModel]);
    }

    /**
     * Get the authorization to update a pivot.
     */
    protected function forUpdate(User $user, Model $foreignModel, Model $relatedModel): bool
    {
        return Gate::forUser($user)->check('update', $foreignModel)
            && Gate::forUser($user)->check('update', $relatedModel);
    }
}
