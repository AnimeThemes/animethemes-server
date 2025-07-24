<?php

declare(strict_types=1);

namespace App\Http\Api\Field\List\Playlist;

use App\Contracts\Models\HasHashids;
use App\Http\Api\Field\StringField;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;

class PlaylistHashidsField extends StringField
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, BaseResource::ATTRIBUTE_ID, HasHashids::ATTRIBUTE_HASHID);
    }
}
