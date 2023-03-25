<?php

declare(strict_types=1);

namespace App\Http\Api\Field\List\Playlist;

use App\Contracts\Models\HasHashids;
use App\Http\Api\Field\Field;
use App\Http\Api\Schema\Schema;

/**
 * Class PlaylistHashidsField.
 *
 * TODO extend StringField
 */
class PlaylistHashidsField extends Field
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, HasHashids::ATTRIBUTE_HASHID);
    }
}
