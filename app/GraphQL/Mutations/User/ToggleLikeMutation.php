<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\User;

use App\Contracts\Models\Likeable;
use App\Models\User\Like;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class ToggleLikeMutation
{
    /**
     * @param  array<string, mixed>  $args
     */
    public function __invoke(null $_, array $args): ?Like
    {
        /** @var Model&Likeable $likeable */
        $likeable = Arr::first($args);

        return $likeable->toggleLike(Auth::user());
    }
}
