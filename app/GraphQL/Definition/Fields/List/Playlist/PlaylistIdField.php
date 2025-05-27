<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\List\Playlist;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\List\Playlist;

/**
 * Class PlaylistIdField.
 */
class PlaylistIdField extends StringField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Playlist::ATTRIBUTE_HASHID, 'id', false);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The primary key of the resource';
    }
}
