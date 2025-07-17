<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Search;

use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Types\List\PlaylistType;
use GraphQL\Type\Definition\Type;

/**
 * Class SearchPlaylistsField.
 */
class SearchPlaylistsField extends Field
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct('playlists', nullable: false);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The playlist results of the search';
    }

    /**
     * The type returned by the field.
     *
     * @return Type
     */
    public function type(): Type
    {
        return Type::listOf(Type::nonNull(new PlaylistType()));
    }
}
