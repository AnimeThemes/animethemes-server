<?php

declare(strict_types=1);

namespace App\Concerns\Models;

use App\Models\User\Like;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Auth;

trait InteractsWithLikes
{
    /**
     * Mark the model as liked for the current authenticated user.
     */
    public function like(): Like
    {
        return Like::query()
            ->create([
                Like::ATTRIBUTE_USER => Auth::id(),
                Like::ATTRIBUTE_LIKEABLE_TYPE => Relation::getMorphAlias($this->getMorphClass()),
                Like::ATTRIBUTE_LIKEABLE_ID => $this->getKey(),
            ]);
    }

    /**
     * Remove the like from the model for the current authenticated user.
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
