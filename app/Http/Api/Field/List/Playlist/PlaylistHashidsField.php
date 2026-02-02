<?php

declare(strict_types=1);

namespace App\Http\Api\Field\List\Playlist;

use App\Contracts\Models\HasHashids;
use App\Http\Api\Field\StringField;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseJsonResource;

class PlaylistHashidsField extends StringField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, BaseJsonResource::ATTRIBUTE_ID, HasHashids::ATTRIBUTE_HASHID);
    }
}
