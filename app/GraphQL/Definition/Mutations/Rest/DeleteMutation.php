<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Mutations\Rest;

use App\Contracts\GraphQL\Fields\UpdatableField;
use App\Contracts\GraphQL\HasFields;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Mutations\BaseMutation;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DeleteMutation.
 */
abstract class DeleteMutation extends BaseMutation
{
    /**
     * Create a new mutation instance.
     *
     * @param  class-string<Model>  $model
     */
    public function __construct(protected string $model)
    {
        parent::__construct('delete'.ucfirst(class_basename($model)));
    }

    /**
     * Get the arguments for the create mutation.
     *
     * @return array<int, Field&UpdatableField>
     */
    public function arguments(): array
    {
        $arguments = [];

        $baseType = $this->baseType();

        if ($baseType instanceof HasFields) {
            $arguments[] = $this->resolveBindArgument($baseType->fields());
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
                'ability' => 'delete',
                'injectArgs' => true,
                'model' => $this->model,
            ],

            ...parent::directives(),
        ];
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
