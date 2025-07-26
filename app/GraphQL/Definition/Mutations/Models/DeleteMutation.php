<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Mutations\Models;

use App\Contracts\GraphQL\HasFields;
use App\GraphQL\Definition\Argument\Argument;
use App\GraphQL\Definition\Mutations\BaseMutation;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

abstract class DeleteMutation extends BaseMutation
{
    /**
     * @param  class-string<Model>  $model
     */
    public function __construct(protected string $model)
    {
        parent::__construct('Delete'.Str::pascal(class_basename($model)));
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
                'ability' => 'delete',
                'injectArgs' => true,
                'model' => $this->model,
            ],

            ...parent::directives(),
        ];
    }

    /**
     * The type returned by the field.
     */
    public function getType(): Type
    {
        return Type::nonNull($this->baseType());
    }
}
