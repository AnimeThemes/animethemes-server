<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\List\Playlist;

use App\Contracts\GraphQL\Fields\BindableField;
use App\GraphQL\Definition\Fields\StringField;
use App\Models\List\Playlist;

class PlaylistIdField extends StringField implements BindableField
{
    public function __construct()
    {
        parent::__construct(Playlist::ATTRIBUTE_HASHID, 'id', false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The primary key of the resource';
    }

    /**
     * The resolver to cast the model.
     */
    public function bindResolver(array $args): null
    {
        return null;
    }
}
