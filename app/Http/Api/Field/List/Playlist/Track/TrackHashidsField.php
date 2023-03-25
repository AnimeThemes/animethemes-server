<?php

declare(strict_types=1);

namespace App\Http\Api\Field\List\Playlist\Track;

use App\Contracts\Models\HasHashids;
use App\Http\Api\Field\Field;
use App\Http\Api\Schema\Schema;

/**
 * Class TrackHashidsField.
 *
 * TODO extend StringField
 */
class TrackHashidsField extends Field
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
