<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Mutations\Models;

use App\Contracts\GraphQL\Fields\BindableField;
use App\Contracts\GraphQL\Fields\CreatableField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Mutations\BaseMutation;
use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Support\Argument\Argument;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
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
            $bindableFields = Arr::where($baseType->fields(), fn (Field $field) => $field instanceof BindableField && $field instanceof CreatableField);
            $notBindableFields = Arr::where($baseType->fields(), fn (Field $field) => ! $field instanceof BindableField);
            $arguments[] = $this->resolveBindArguments($bindableFields);
            $arguments[] = $this->resolveCreateMutationArguments($notBindableFields);
        }

        return Arr::flatten($arguments);
    }

    /**
     * Get the rules for the create mutation.
     *
     * @param  array<string, mixed>  $args
     * @return array<string, array>
     */
    protected function rules(array $args = []): array
    {
        $baseType = $this->baseType();

        if ($baseType instanceof BaseType) {
            return collect($baseType->fields())
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
