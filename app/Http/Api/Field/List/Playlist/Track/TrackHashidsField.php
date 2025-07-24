<?php

declare(strict_types=1);

namespace App\Http\Api\Field\List\Playlist\Track;

use App\Contracts\Models\HasHashids;
use App\Http\Api\Field\StringField;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;

class TrackHashidsField extends StringField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, BaseResource::ATTRIBUTE_ID, HasHashids::ATTRIBUTE_HASHID);
    }
}
