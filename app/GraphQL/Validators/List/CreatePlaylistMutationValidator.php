<?php

declare(strict_types=1);

namespace App\GraphQL\Validators\List;

use App\Enums\Models\List\PlaylistVisibility;
use App\Rules\ModerationRule;
use Illuminate\Validation\Rules\Enum;
use Nuwave\Lighthouse\Validation\Validator;

class CreatePlaylistMutationValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:192',
                new ModerationRule(),
            ],
            'visibility' => [
                'required',
                new Enum(PlaylistVisibility::class),
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }
}
