<?php

declare(strict_types=1);

namespace App\Concerns\Models;

use App\Models\Auth\User;
use App\Models\User\Like;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\Relation;

trait InteractsWithLikes
{
    public function like(User $user): Like
    {
        return Like::query()
            ->create([
                Like::ATTRIBUTE_USER => $user->getKey(),
                Like::ATTRIBUTE_LIKEABLE_TYPE => Relation::getMorphAlias($this->getMorphClass()),
                Like::ATTRIBUTE_LIKEABLE_ID => $this->getKey(),
            ]);
    }

    public function unlike(User $user): mixed
    {
        return Like::query()
            ->whereBelongsTo($user, Like::RELATION_USER)
            ->whereMorphedTo(Like::RELATION_LIKEABLE, $this)
            ->delete();
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, Like::RELATION_LIKEABLE);
    }
}
