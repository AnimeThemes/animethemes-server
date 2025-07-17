<?php

declare(strict_types=1);

namespace App\GraphQL\Controllers;

use App\GraphQL\Definition\Mutations\BaseMutation;
use Dotenv\Exception\ValidationException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

/**
 * Class BaseController.
 */
abstract class BaseController
{
    /**
     * @param  array  $args
     * @param  class-string<BaseMutation>  $mutation
     * @return array
     *
     * @throws ValidationException
     */
    public function validated(array $args, string $mutation): array
    {
        $mutationInstance = App::make($mutation);

        return Validator::make($args, $mutationInstance->rules($args))->validated();
    }
}
