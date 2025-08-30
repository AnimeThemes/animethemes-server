<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Mutations\Models;

use App\Contracts\GraphQL\Fields\BindableField;
use App\Contracts\GraphQL\Fields\CreatableField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Mutations\BaseMutation;
use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Support\Argument\Argument;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

abstract class CreateMutation extends BaseMutation
{
    /**
     * @param  class-string<Model>  $model
     */
    public function __construct(protected string $model)
    {
        parent::__construct('Create'.Str::pascal(class_basename($model)));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        $args = collect($args)
            ->filter(fn ($value) => $value instanceof Model)
            ->values()
            ->all();

        return Gate::allows('create', [$this->model, ...$args]);
    }

    /**
     * Get the arguments for the create mutation.
     *
     * @return Argument[]
     */
    public function arguments(): array
    {
        $arguments = [];

        $baseType = $this->baseRebingType();

        if ($baseType instanceof BaseType) {
            $bindableFields = Arr::where($baseType->fieldClasses(), fn (Field $field) => $field instanceof BindableField && $field instanceof CreatableField);
            $notBindableFields = Arr::where($baseType->fieldClasses(), fn (Field $field) => ! $field instanceof BindableField);
            $arguments[] = $this->resolveBindArguments($bindableFields);
            $arguments[] = $this->resolveCreateMutationArguments($notBindableFields);
        }

        return Arr::flatten($arguments);
    }

    /**
     * @param  array<string, mixed>  $args
     * @return array<string, array>
     */
    public function rulesForValidation(array $args = []): array
    {
        $baseType = $this->baseRebingType();

        if ($baseType instanceof BaseType) {
            return collect($baseType->fieldClasses())
                ->filter(fn (Field $field) => $field instanceof CreatableField)
                ->mapWithKeys(fn (Field&CreatableField $field) => [$field->getColumn() => $field->getCreationRules($args)])
                ->toArray();
        }

        return [];
    }

    /**
     * The type returned by the field.
     */
    public function type(): Type
    {
        return Type::nonNull($this->baseType());
    }
}
