<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Mutations\User;

use App\Contracts\GraphQL\Fields\DeletableField;
use App\GraphQL\Attributes\UseField;
use App\GraphQL\Controllers\User\LikeController;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Mutations\BaseMutation;
use App\GraphQL\Definition\Types\User\LikeType;
use App\GraphQL\Definition\Unions\LikedUnion;
use App\Models\User\Like;
use GraphQL\Type\Definition\Type;

#[UseField(LikeController::class, 'destroy')]
class UnlikeMutation extends BaseMutation
{
    public function __construct()
    {
        parent::__construct('unlike');
    }

    /**
     * The description of the mutation.
     */
    public function description(): string
    {
        return 'Unlike a model';
    }

    /**
     * Get the arguments for the unlike mutation.
     *
     * @return string[]
     */
    public function arguments(): array
    {
        $type = new LikeType();

        return $this->resolveBindArgument($type->fields(), false);
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
                'model' => Like::class,
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
        $type = new LikeType();

        return collect($type->fields())
            ->filter(fn (Field $field) => $field instanceof DeletableField)
            ->mapWithKeys(fn (Field&DeletableField $field) => [$field->getColumn() => $field->getDeleteRules($args)])
            ->toArray();
    }

    /**
     * The base return type of the mutation.
     */
    public function baseType(): Type
    {
        return new LikedUnion();
    }

    /**
     * The type returned by the field.
     */
    public function getType(): Type
    {
        return Type::nonNull($this->baseType());
    }
}
