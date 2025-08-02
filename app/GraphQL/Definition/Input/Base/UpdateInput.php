<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Input\Base;

use App\Concerns\GraphQL\ResolvesArguments;
use App\Contracts\GraphQL\Fields\BindableField;
use App\Contracts\GraphQL\Fields\RequiredOnUpdate;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Input\Input;
use App\GraphQL\Definition\Input\Relations\UpdateBelongsToInput;
use App\GraphQL\Definition\Input\Relations\UpdateBelongsToManyInput;
use App\GraphQL\Definition\Input\Relations\UpdateHasManyInput;
use App\GraphQL\Definition\Types\EloquentType;
use App\GraphQL\Support\InputField;
use App\GraphQL\Support\Relations\BelongsToManyRelation;
use App\GraphQL\Support\Relations\BelongsToRelation;
use App\GraphQL\Support\Relations\HasManyRelation;
use App\GraphQL\Support\Relations\Relation;
use Illuminate\Support\Arr;

class UpdateInput extends Input
{
    use ResolvesArguments;

    public function __construct(
        protected EloquentType $type,
    ) {
        parent::__construct("Update{$type->getName()}");
    }

    /**
     * The input fields.
     *
     * @return InputField[]
     */
    public function fields(): array
    {
        $fields = [];

        $baseType = $this->type;

        $fields[] = collect($baseType->fields())
            ->filter(fn (Field $field) => $field instanceof BindableField)
            ->map(fn (Field&BindableField $field) => new InputField($field->getName(), $field->type()->__toString().'!'))
            ->toArray();

        $fields[] = collect($baseType->fields())
            ->filter(fn (Field $field) => $field instanceof UpdatableField) // and reportable field?
            ->map(
                fn (Field&UpdatableField $field) => new InputField($field->getName(), $field->type().($field instanceof RequiredOnUpdate ? '!' : ''))
            )
            ->toArray();

        $fields[] = collect($baseType->relations())
            ->mapWithKeys(function (Relation $relation) {
                $baseType = $relation->getBaseType();
                if (! $baseType instanceof EloquentType) {
                    return [];
                }

                return match (true) {
                    $relation instanceof BelongsToRelation => [$relation->getName() => new UpdateBelongsToInput($baseType)],
                    $relation instanceof HasManyRelation => [$relation->getName() => new UpdateHasManyInput($baseType)],
                    $relation instanceof BelongsToManyRelation => [$relation->getName() => new UpdateBelongsToManyInput($relation->getEdgeType()->getPivotType())],
                    default => [],
                };
            })
            ->map(fn (Input $input, string $name) => new InputField($name, $input->getName()))
            ->toArray();

        return Arr::flatten($fields);
    }
}
