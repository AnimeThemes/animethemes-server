<?php

declare(strict_types=1);

namespace App\Http\Middleware\Models;

use App\Models\Auth\User;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

/**
 * AuthorizesPivot
 */
class AuthorizesPivot
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure(Request): mixed  $next
     * @param  string  $foreignClass
     * @param  string  $foreignParameter
     * @param  string  $relatedClass
     * @param  string  $relatedParameter
     * @return mixed
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

        if (!$isAuthorized) {
            abort(403);
        }

        return $next($request);
    }

    /**
     * Get the authorization to index.
     *
     * @param  User|null  $user
     * @param  string  $foreignClass
     * @param  string  $relatedClass
     * @return bool
     */
    protected function forIndex(?User $user, string $foreignClass, string $relatedClass): bool
    {
        return Gate::forUser($user)->check('viewAny', $foreignClass)
            && Gate::forUser($user)->check('viewAny', $relatedClass);
    }

    /**
     * Get the authorization to show a pivot.
     *
     * @param  User|null  $user
     * @param  Model  $foreignModel
     * @param  Model  $relatedModel
     * @return bool
     */
    protected function forShow(?User $user, Model $foreignModel, Model $relatedModel): bool
    {
        return Gate::forUser($user)->check('view', $foreignModel)
            && Gate::forUser($user)->check('view', $relatedModel);
    }

    /**
     * Get the authorization to store a pivot.
     *
     * @param  User  $user
     * @param  Model  $foreignModel
     * @param  Model  $relatedModel
     * @return bool
     */
    protected function forStore(User $user, Model $foreignModel, Model $relatedModel): bool
    {
        $attach = Str::of('attach')
            ->append(Str::singular(class_basename($relatedModel)))
            ->__toString();

        return Gate::forUser($user)->check($attach, [$foreignModel, $relatedModel]);
    }

    /**
     * Get the authorization to destroy a pivot.
     *
     * @param  User  $user
     * @param  Model  $foreignModel
     * @param  Model  $relatedModel
     * @return bool
     */
    protected function forDestroy(User $user, Model $foreignModel, Model $relatedModel): bool
    {
        $detachAny = Str::of('detachAny')
            ->append(Str::singular(class_basename($relatedModel)))
            ->__toString();

        $detach = Str::of('detach')
            ->append(Str::singular(class_basename($relatedModel)))
            ->__toString();

        return Gate::forUser($user)->any([$detach, $detachAny], $foreignModel);
    }

    /**
     * Get the authorization to update a pivot.
     *
     * @param  User  $user
     * @param  Model  $foreignModel
     * @param  Model  $relatedModel
     * @return bool
     */
    protected function forUpdate(User $user, Model $foreignModel, Model $relatedModel): bool
    {
        return Gate::forUser($user)->check('update', $foreignModel)
            && Gate::forUser($user)->check('update', $relatedModel);
    }
}
