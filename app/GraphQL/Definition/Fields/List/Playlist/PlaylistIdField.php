<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\List\Playlist;

use App\Contracts\GraphQL\Fields\BindableField;
use App\GraphQL\Definition\Fields\StringField;
use App\Models\List\Playlist;

/**
 * Class PlaylistIdField.
 */
class PlaylistIdField extends StringField implements BindableField
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

    /**
     * Get the model that the field should bind to.
     *
     * @return class-string<Playlist>
     */
    public function bindTo(): string
    {
        return Playlist::class;
    }

    /**
     * Get the column that the field should use to bind.
     *
     * @return string
     */
    public function bindUsingColumn(): string
    {
        return Playlist::ATTRIBUTE_HASHID;
    }
}
