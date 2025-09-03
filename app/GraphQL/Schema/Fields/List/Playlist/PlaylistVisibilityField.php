<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\List\Playlist;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\RequiredOnCreation;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\Enums\Models\List\PlaylistVisibility;
use App\GraphQL\Schema\Fields\EnumField;
use App\Models\List\Playlist;
use Illuminate\Validation\Rules\Enum;

class PlaylistVisibilityField extends EnumField implements CreatableField, RequiredOnCreation, UpdatableField
{
    public function __construct()
    {
        parent::__construct(Playlist::ATTRIBUTE_VISIBILITY, PlaylistVisibility::class, nullable: false);
    }

    public function description(): string
    {
        return 'The state of who can see the playlist';
    }

    /**
     * @param  array<string, mixed>  $args
     * @return array
     */
    public function getCreationRules(array $args): array
    {
        return [
            'required',
            new Enum(PlaylistVisibility::class),
        ];
    }

    /**
     * @param  array<string, mixed>  $args
     * @return array
     */
    public function getUpdateRules(array $args): array
    {
        return [
            'sometimes',
            'required',
            new Enum(PlaylistVisibility::class),
        ];
    }
}
