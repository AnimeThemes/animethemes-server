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

class PlaylistNameField extends StringField implements CreatableField, UpdatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Playlist::ATTRIBUTE_NAME);
    }

    /**
     * Set the creation validation rules for the field.
     *
     * @return array
     */
    public function getCreationRules(Request $request): array
    {
        return [
            'required',
            'string',
            'max:192',
            new ModerationRule(),
        ];
    }

    /**
     * Set the update validation rules for the field.
     *
     * @return array
     */
    public function getUpdateRules(Request $request): array
    {
        return [
            'sometimes',
            'required',
            'string',
            'max:192',
            new ModerationRule(),
        ];
    }
}
