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
use Rebing\GraphQL\Error\ValidationError;

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
     * @return array<string, mixed>
     *
     * @throws ValidationError
     */
    public function validated(array $args, string $mutation): array
    {
        $mutationInstance = App::make($mutation);

        $validator = Validator::make($args, $mutationInstance->rulesForValidation($args));

        try {
            $validated = $validator->validated();
        } catch (ValidationException $e) {
            throw new ValidationError($e->getMessage(), $validator);
        }

        return [
            ...$validated,

            'model' => Arr::get($args, self::MODEL),
        ];
    }
}
