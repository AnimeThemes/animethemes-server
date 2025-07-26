<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Mutations\Models;

use App\Contracts\GraphQL\Fields\UpdatableField;
use App\Contracts\GraphQL\HasFields;
use App\GraphQL\Definition\Argument\Argument;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Mutations\BaseMutation;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

abstract class UpdateMutation extends BaseMutation
{
    /**
     * @param  class-string<Model>  $model
     */
    public function __construct(protected string $model)
    {
        parent::__construct('update'.ucfirst(class_basename($model)));
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

        if ($baseType instanceof HasFields) {
            $arguments[] = $this->resolveBindArguments($baseType->fields());
            $arguments[] = $this->resolveUpdateMutationArguments($baseType->fields());
        }

        return Arr::flatten($arguments);
    }

    /**
     * The directives of the mutation.
     *
     * @return array<string, array>
     */
    public function directives(): array
    {
        return [
            'canModel' => [
                'ability' => 'update',
                'injectArgs' => true,
                'model' => $this->model,
            ],

            ...parent::directives(),
        ];
    }

    /**
     * Get the rules for the create mutation.
     *
     * @param  array<string, mixed>  $args
     * @return array<string, array>
     */
    public function rules(array $args): array
    {
        $baseType = $this->baseType();

        if ($baseType instanceof HasFields) {
            return collect($baseType->fields())
                ->filter(fn (Field $field) => $field instanceof UpdatableField)
                ->mapWithKeys(fn (Field&UpdatableField $field) => [$field->getColumn() => $field->getUpdateRules($args)])
                ->toArray();
        }

        return [];
    }

    /**
     * The type returned by the field.
     */
    public function getType(): Type
    {
        return Type::nonNull($this->baseType());
    }
}
