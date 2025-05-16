<?php

declare(strict_types=1);

namespace App\GraphQL\Validators\Mutation\List\Playlist;

use App\Enums\Models\List\PlaylistVisibility;
use App\Models\List\Playlist;
use App\Rules\ModerationRule;
use Illuminate\Validation\Rules\Enum;
use Nuwave\Lighthouse\Validation\Validator;

/**
 * Class UpdatePlaylistValidator.
 */
class UpdatePlaylistValidator extends Validator
{
    /**
     * Specify validation rules for the arguments.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            Playlist::ATTRIBUTE_NAME => [
                'sometimes',
                'required',
                'string',
                'max:192',
                new ModerationRule(),
            ],
            Playlist::ATTRIBUTE_DESCRIPTION => [
                'nullable',
                'string',
                'max:1000',
                new ModerationRule(),
            ],
            Playlist::ATTRIBUTE_VISIBILITY => [
                'sometimes',
                'required',
                new Enum(PlaylistVisibility::class),
            ],
        ];
    }
}
