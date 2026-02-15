<?php

declare(strict_types=1);

namespace App\Contracts\Models;

use App\Models\Auth\User;
use App\Models\User\Like;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Likeable
{
    public function toggleLike(User $user): ?Like;

    public function likes(): MorphMany;
}
