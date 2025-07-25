<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Mutations\Rest\List\Playlist\Track;

use App\GraphQL\Attributes\UseFieldDirective;
use App\GraphQL\Controllers\List\Playlist\PlaylistTrackController;
use App\GraphQL\Definition\Mutations\Rest\DeleteMutation;
use App\GraphQL\Definition\Types\List\Playlist\PlaylistTrackType;
use App\GraphQL\Definition\Types\MessageResponseType;
use App\Models\List\Playlist\PlaylistTrack;
use GraphQL\Type\Definition\Type;

#[UseFieldDirective(PlaylistTrackController::class, 'destroy')]
class DeletePlaylistTrackMutation extends DeleteMutation
{
    public function __construct()
    {
        parent::__construct(PlaylistTrack::class);
    }

    /**
     * The description of the mutation.
     */
    public function description(): string
    {
        return 'Delete playlist track';
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): PlaylistTrackType
    {
        return new PlaylistTrackType();
    }

    /**
     * The type returned by the field.
     */
    public function getType(): Type
    {
        return Type::nonNull(new MessageResponseType());
    }
}
