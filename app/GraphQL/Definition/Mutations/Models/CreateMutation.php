<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Mutations\Models;

use App\Contracts\GraphQL\Fields\BindableField;
use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\HasFields;
use App\GraphQL\Definition\Argument\Argument;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Mutations\BaseMutation;
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

        if ($baseType instanceof HasFields) {
            $bindableFields = Arr::where($baseType->fields(), fn (Field $field) => $field instanceof BindableField && $field instanceof CreatableField);
            $notBindableFields = Arr::where($baseType->fields(), fn (Field $field) => ! $field instanceof BindableField);
            $arguments[] = $this->resolveBindArguments($bindableFields);
            $arguments[] = $this->resolveCreateMutationArguments($notBindableFields);
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
                'ability' => 'create',
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
                ->filter(fn (Field $field) => $field instanceof CreatableField)
                ->mapWithKeys(fn (Field&CreatableField $field) => [$field->getColumn() => $field->getCreationRules($args)])
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
