<?php

declare(strict_types=1);

namespace App\GraphQL\Validators\User;

use Illuminate\Support\Str;
use Nuwave\Lighthouse\Validation\Validator;

class ToggleLikeMutationValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'entry' => [
                Str::of('prohibits:')->append('playlist')->__toString(),
                'required_without_all:'.implode(',', [
                    'playlist',
                ]),
            ],
            'playlist' => [
                Str::of('prohibits:')->append('entry')->__toString(),
                'required_without_all:'.implode(',', [
                    'entry',
                ]),
            ],
        ];
    }
}
