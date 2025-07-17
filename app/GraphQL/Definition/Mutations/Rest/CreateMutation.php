<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Mutations\Rest;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\HasFields;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Mutations\BaseMutation;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CreateMutation.
 */
abstract class CreateMutation extends BaseMutation
{
    /**
     * Create a new mutation instance.
     *
     * @param  class-string<Model>  $model
     */
    public function __construct(string $model)
    {
        parent::__construct('create'.ucfirst(class_basename($model)));
    }

    /**
     * Get the arguments for the create mutation.
     *
     * @return array<int, Field&CreatableField>
     */
    public function arguments(): array
    {
        $arguments = [];

        $baseType = $this->baseType();

        if ($baseType instanceof HasFields) {
            $arguments[] = $this->resolveCreateMutationArguments($baseType->fields());
        }

        return $arguments;
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
                'ability' => 'create',
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
                ->filter(fn (Field $field) => $field instanceof CreatableField)
                ->mapWithKeys(fn (Field&CreatableField $field) => [$field->getColumn() => $field->getCreationRules($args)])
                ->toArray();
        }

        return [];
    }

    /**
     * The type returned by the field.
     *
     * @return Type
     */
    public function getType(): Type
    {
        return Type::nonNull($this->baseType());
    }
}
