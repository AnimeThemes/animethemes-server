<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\List\Playlist;

use App\Enums\Models\List\PlaylistVisibility;
use App\GraphQL\Definition\Fields\EnumField;
use App\Models\List\Playlist;

/**
 * Class PlaylistVisibilityField.
 */
class PlaylistVisibilityField extends EnumField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Playlist::ATTRIBUTE_VISIBILITY, PlaylistVisibility::class, nullable: false);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The state of who can see the playlist';
    }
}
