<?php

declare(strict_types=1);

namespace App\Concerns\Models;

use App\Models\User\Like;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

/**
 * Trait InteractsWithLikes.
 */
trait InteractsWithLikes
{
    /**
     * Mark the model as liked for the current authenticated user.
     *
     * @return Like
     */
    public function like(): Like
    {
        return Like::query()
            ->create([
                Like::ATTRIBUTE_USER => Auth::id(),
                Like::ATTRIBUTE_LIKEABLE_TYPE => $this->getMorphClass(),
                Like::ATTRIBUTE_LIKEABLE_ID => $this->getKey(),
            ]);
    }

    /**
     * Remove the like from the model for the current authenticated user.
     *
     * @return mixed
     */
    public function unlike(): mixed
    {
        return Like::query()
            ->whereBelongsTo(Auth::user())
            ->whereMorphedTo(Like::RELATION_LIKEABLE, $this)
            ->delete();
    }

    /**
     * Get the likes for the model.
     *
     * @return MorphMany
     */
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, Like::RELATION_LIKEABLE);
    }
}
