<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Mutations\Models;

use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Mutations\BaseMutation;
use App\GraphQL\Schema\Types\BaseType;
use App\GraphQL\Support\Argument\Argument;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

abstract class UpdateMutation extends BaseMutation
{
    /**
     * @param  class-string<Model>  $model
     */
    public function __construct(protected string $model)
    {
        parent::__construct('Update'.Str::pascal(class_basename($model)));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        $model = Arr::pull($args, 'model');

        $args = collect($args)
            ->filter(fn ($value) => $value instanceof Model)
            ->prepend($model)
            ->values()
            ->all();

        return Gate::allows('update', $args);
    }

    /**
     * Get the arguments for the create mutation.
     *
     * @return Argument[]
     */
    public function arguments(): array
    {
        $arguments = [];

        $baseType = $this->baseType();

        if ($baseType instanceof BaseType) {
            $arguments[] = $this->resolveBindArguments($baseType->fieldClasses());
            $arguments[] = $this->resolveUpdateMutationArguments($baseType->fieldClasses());
        }

        return Arr::flatten($arguments);
    }

    /**
     * @param  array<string, mixed>  $args
     * @return array<string, array>
     */
    public function rulesForValidation(array $args = []): array
    {
        $baseType = $this->baseType();

        if ($baseType instanceof BaseType) {
            return collect($baseType->fieldClasses())
                ->filter(fn (Field $field) => $field instanceof UpdatableField)
                ->mapWithKeys(fn (Field&UpdatableField $field) => [$field->getColumn() => $field->getUpdateRules($args)])
                ->toArray();
        }

        return [];
    }

    public function type(): Type
    {
        return Type::nonNull($this->toType());
    }
}
