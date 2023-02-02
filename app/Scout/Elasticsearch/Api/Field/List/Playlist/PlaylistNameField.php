<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\List\Playlist;

use App\Models\List\Playlist;
use App\Scout\Elasticsearch\Api\Field\StringField;
use App\Scout\Elasticsearch\Api\Schema\Schema;

/**
 * Class PlaylistNameField.
 */
class PlaylistNameField extends StringField
{
    /**
     * Create a new field instance.
	 *
	 * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Playlist::ATTRIBUTE_NAME);
    }
}
