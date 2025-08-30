<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Search;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Types\List\PlaylistType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

class SearchPlaylistsField extends Field implements DisplayableField
{
    public function __construct()
    {
        parent::__construct('playlists', nullable: false);
    }

    public function description(): string
    {
        return 'The playlist results of the search';
    }

    /**
     * The type returned by the field.
     */
    public function baseType(): Type
    {
        return Type::listOf(Type::nonNull(GraphQL::type(new PlaylistType()->getName())));
    }

    public function canBeDisplayed(): bool
    {
        return true;
    }
}
