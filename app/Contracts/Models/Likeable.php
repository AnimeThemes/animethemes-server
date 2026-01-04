<?php

declare(strict_types=1);

namespace App\Contracts\Models;

use App\Models\Auth\User;
use App\Models\User\Like;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Likeable
{
    public function like(User $user): Like;

    public function unlike(User $user): mixed;

    public function likes(): MorphMany;
}
