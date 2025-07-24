<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\List\Playlist;

use App\GraphQL\Definition\Fields\Base\CountAggregateField;
use App\Models\List\Playlist;

class PlaylistLikesCountField extends CountAggregateField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Playlist::RELATION_LIKE_AGGREGATE, 'likesCount');
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The number of likes recorded for the resource';
    }
}
