<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Mutations\Rest\List\Playlist;

use App\GraphQL\Attributes\UseField;
use App\GraphQL\Controllers\List\PlaylistController;
use App\GraphQL\Definition\Mutations\Rest\DeleteMutation;
use App\GraphQL\Definition\Types\List\PlaylistType;
use App\GraphQL\Definition\Types\MessageResponseType;
use App\Models\List\Playlist;
use GraphQL\Type\Definition\Type;

#[UseField(PlaylistController::class, 'destroy')]
class DeletePlaylistMutation extends DeleteMutation
{
    /**
     * Create a new mutation instance.
     */
    public function __construct()
    {
        parent::__construct(Playlist::class);
    }

    /**
     * The description of the mutation.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Delete playlist';
    }

    /**
     * The base return type of the query.
     *
     * @return PlaylistType
     */
    public function baseType(): PlaylistType
    {
        return new PlaylistType();
    }

    /**
     * The type returned by the field.
     *
     * @return Type
     */
    public function getType(): Type
    {
        return Type::nonNull(new MessageResponseType());
    }
}
