<?php

declare(strict_types=1);

namespace App\Contracts\Models;

use App\Models\User\Like;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Likeable
{
    /**
     * Mark the model as liked for the current authenticated user.
     */
    public function like(): Like;

    /**
     * Remove the like from the model for the current authenticated user.
     */
    public function unlike(): mixed;

    /**
     * Get the likes for the model.
     *
     * @return MorphMany
     */
    public function likes(): MorphMany;
}
