<?php

declare(strict_types=1);

namespace App\GraphQL\Validators\Mutation\User\Like;

use App\GraphQL\Controllers\User\LikeController;
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
            LikeController::ATTRIBUTE_PLAYLIST => [
                Str::of('prohibits:')->append(LikeController::ATTRIBUTE_VIDEO)->__toString(),
            ],
            LikeController::ATTRIBUTE_VIDEO => [
                Str::of('prohibits:')->append(LikeController::ATTRIBUTE_PLAYLIST)->__toString(),
            ],
        ];
    }
}
