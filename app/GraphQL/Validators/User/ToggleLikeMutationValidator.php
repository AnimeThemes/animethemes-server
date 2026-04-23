<?php

declare(strict_types=1);

namespace App\GraphQL\Validators\User;

use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
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
            'entryId' => [
                Str::of('prohibits:')->append('playlistId')->__toString(),
                'required_without_all:'.implode(',', [
                    'playlistId',
                ]),
                Rule::exists(AnimeThemeEntry::TABLE, AnimeThemeEntry::ATTRIBUTE_ID),
            ],
            'playlistId' => [
                Str::of('prohibits:')->append('entryId')->__toString(),
                'required_without_all:'.implode(',', [
                    'entryId',
                ]),
            ],
        ];
    }
}
