<?php

declare(strict_types=1);

namespace App\Http\Api\Field\List\Playlist;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Enums\Models\List\PlaylistVisibility;
use App\Http\Api\Field\EnumField;
use App\Http\Api\Schema\Schema;
use App\Models\List\Playlist;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class PlaylistVisibilityField extends EnumField implements CreatableField, UpdatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Playlist::ATTRIBUTE_VISIBILITY, PlaylistVisibility::class);
    }

    /**
     * @return array
     */
    public function getCreationRules(Request $request): array
    {
        return [
            'required',
            new Enum(PlaylistVisibility::class),
        ];
    }

    /**
     * @return array
     */
    public function getUpdateRules(Request $request): array
    {
        return [
            'sometimes',
            'required',
            new Enum(PlaylistVisibility::class),
        ];
    }
}
