<?php

declare(strict_types=1);

namespace App\Http\Api\Field\List\Playlist;

use App\Http\Api\Field\CountField;
use App\Http\Api\Schema\Schema;
use App\Models\List\Playlist;

/**
 * Class PlaylistViewCountField.
 */
class PlaylistViewCountField extends CountField
{
    /**
     * Create a new field instance.
	 *
	 * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Playlist::RELATION_VIEWS);
    }
}
