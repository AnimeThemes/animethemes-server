<?php

declare(strict_types=1);

namespace App\GraphQL\Controllers\User;

use App\Contracts\Models\Likeable;
use App\Exceptions\GraphQL\ClientValidationException;
use App\GraphQL\Controllers\BaseController;
use App\GraphQL\Schema\Mutations\Models\User\LikeMutation;
use App\GraphQL\Schema\Mutations\Models\User\UnlikeMutation;
use App\Models\User\Like;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * @extends BaseController<Like>
 */
class LikeController extends BaseController
{
    final public const string ATTRIBUTE_ENTRY = 'entry';
    final public const string ATTRIBUTE_PLAYLIST = 'playlist';

    /**
     * @param  array<string, mixed>  $args
     *
     * @throws ClientValidationException
     */
    public function store($root, array $args): Model&Likeable
    {
        $validated = $this->validated($args, LikeMutation::class);

        foreach ($validated as $likeable) {
            if ($likeable instanceof Model && $likeable instanceof Likeable) {
                $likeable->like(Auth::user());

                return $likeable;
            }
        }

        throw new ClientValidationException('One resource is required to like.');
    }

    /**
     * @param  array<string, mixed>  $args
     *
     * @throws ClientValidationException
     */
    public function destroy($root, array $args): Model&Likeable
    {
        $validated = $this->validated($args, UnlikeMutation::class);

        foreach ($validated as $likeable) {
            if ($likeable instanceof Model && $likeable instanceof Likeable) {
                $likeable->unlike(Auth::user());

                return $likeable;
            }
        }

        throw new ClientValidationException('One resource is required to unlike.');
    }
}
