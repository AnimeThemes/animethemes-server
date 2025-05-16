<?php

declare(strict_types=1);

namespace App\GraphQL\Validators\Mutation\User\Like;

use App\GraphQL\Mutations\User\LikeMutator;
use Illuminate\Support\Str;
use Nuwave\Lighthouse\Validation\Validator;

/**
 * Class CreateLikeValidator.
 */
class CreateLikeValidator extends Validator
{
    /**
     * Specify validation rules for the arguments.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            LikeMutator::ATTRIBUTE_PLAYLIST => [
                Str::of('prohibits:')->append(LikeMutator::ATTRIBUTE_VIDEO)->__toString(),
            ],
            LikeMutator::ATTRIBUTE_VIDEO => [
                Str::of('prohibits:')->append(LikeMutator::ATTRIBUTE_PLAYLIST)->__toString(),
            ],
        ];
    }
}
