<?php

declare(strict_types=1);

namespace App\GraphQL\Controllers;

use App\Actions\Http\Api\DestroyAction;
use App\Actions\Http\Api\StoreAction;
use App\Actions\Http\Api\UpdateAction;
use App\GraphQL\Schema\Mutations\BaseMutation;
use Dotenv\Exception\ValidationException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
abstract class BaseController
{
    final public const MODEL = 'model';

    /**
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
     * @param  class-string<BaseMutation>  $mutation
     *
     * @throws ValidationException
     */
    public function validated(array $args, string $mutation): array
    {
        $mutationInstance = App::make($mutation);

        $validated = Validator::make($args, $mutationInstance->rulesForValidation($args))->validated();

        return [
            ...$validated,

            'model' => Arr::get($args, self::MODEL),
        ];
    }
}
