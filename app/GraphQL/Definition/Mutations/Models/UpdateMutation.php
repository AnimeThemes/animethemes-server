<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Mutations\Models;

use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Mutations\BaseMutation;
use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Support\Argument\Argument;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
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
            $arguments[] = $this->resolveBindArguments($baseType->fieldClasses());
            $arguments[] = $this->resolveUpdateMutationArguments($baseType->fieldClasses());
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
        $baseType = $this->baseRebingType();

        if ($baseType instanceof BaseType) {
            return collect($baseType->fieldClasses())
                ->filter(fn (Field $field) => $field instanceof UpdatableField)
                ->mapWithKeys(fn (Field&UpdatableField $field) => [$field->getColumn() => $field->getUpdateRules($args)])
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
