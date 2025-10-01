<?php

declare(strict_types=1);

namespace App\Contracts\Models;

use App\Models\User\Like;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Likeable
{
    public function like(): Like;

    public function unlike(): mixed;

    public function likes(): MorphMany;
}
