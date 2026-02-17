<?php

declare(strict_types=1);

namespace App\Concerns\Models;

use App\Models\Auth\User;
use App\Models\User\Like;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait InteractsWithLikes
{
    public function toggleLike(User $user): ?Like
    {
        $builder = Like::query()
            ->whereBelongsTo($user, Like::RELATION_USER)
            ->whereMorphedTo(Like::RELATION_LIKEABLE, $this);

        if ($builder->delete() > 0) {
            return null;
        }

        return $this->likes()->create([
            Like::ATTRIBUTE_USER => $user->getKey(),
        ]);
    }

    /**
     * @return MorphMany<Like, $this>
     */
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, Like::RELATION_LIKEABLE);
    }
}
