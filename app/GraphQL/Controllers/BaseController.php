<?php

declare(strict_types=1);

namespace App\GraphQL\Controllers;

use App\Actions\Http\Api\DestroyAction;
use App\Actions\Http\Api\StoreAction;
use App\Actions\Http\Api\UpdateAction;
use App\GraphQL\Definition\Mutations\BaseMutation;
use Dotenv\Exception\ValidationException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

/**
 * Class BaseController.
 *
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
abstract class BaseController
{
    /**
     * Create a new controller instance.
     *
     * @param  StoreAction<TModel>  $storeAction
     * @param  UpdateAction<TModel>  $updateAction
     * @param  DestroyAction<TModel>  $destroyAction
     */
    public function __construct(
        protected StoreAction $storeAction,
        protected UpdateAction $updateAction,
        protected DestroyAction $destroyAction,
    ) {}

    /**
     * Get the attributes and values that were validated.
     *
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
