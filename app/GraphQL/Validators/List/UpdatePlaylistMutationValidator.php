<?php

declare(strict_types=1);

namespace App\GraphQL\Validators\List;

use App\Enums\Models\List\PlaylistVisibility;
use Illuminate\Validation\Rules\Enum;
use Nuwave\Lighthouse\Validation\Validator;

class UpdatePlaylistMutationValidator extends Validator
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
                'sometimes',
                'required',
                'string',
                'max:192',
            ],
            'visibility' => [
                'sometimes',
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
