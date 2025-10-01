<?php

declare(strict_types=1);

namespace App\Http\Api\Field\List\Playlist;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\StringField;
use App\Http\Api\Schema\Schema;
use App\Models\List\Playlist;
use App\Rules\ModerationRule;
use Illuminate\Http\Request;

class PlaylistDescriptionField extends StringField implements CreatableField, UpdatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Playlist::ATTRIBUTE_DESCRIPTION);
    }

    public function getCreationRules(Request $request): array
    {
        return [
            'nullable',
            'string',
            'max:1000',
            new ModerationRule(),
        ];
    }

    public function getUpdateRules(Request $request): array
    {
        return [
            'nullable',
            'string',
            'max:1000',
            new ModerationRule(),
        ];
    }
}
