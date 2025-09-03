<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Inputs\Base;

use App\Contracts\GraphQL\Fields\BindableField;
use App\Contracts\GraphQL\Fields\RequiredOnUpdate;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Inputs\Input;
use App\GraphQL\Schema\Inputs\Relations\UpdateBelongsToInput;
use App\GraphQL\Schema\Inputs\Relations\UpdateBelongsToManyInput;
use App\GraphQL\Schema\Inputs\Relations\UpdateHasManyInput;
use App\GraphQL\Schema\Types\EloquentType;
use App\GraphQL\Support\InputField;
use App\GraphQL\Support\Relations\BelongsToManyRelation;
use App\GraphQL\Support\Relations\BelongsToRelation;
use App\GraphQL\Support\Relations\HasManyRelation;
use App\GraphQL\Support\Relations\Relation;
use Illuminate\Support\Arr;

class UpdateInput extends Input
{
    public function __construct(
        protected EloquentType $type,
    ) {}

    public function getName(): string
    {
        return "Update{$this->type->getName()}Input";
    }

    /**
     * @return InputField[]
     */
    public function fieldClasses(): array
    {
        $fields = [];

        $baseType = $this->type;

        $fields[] = collect($baseType->fieldClasses())
            ->filter(fn (Field $field) => $field instanceof BindableField)
            ->map(fn (Field&BindableField $field) => new InputField($field->getName(), $field->type()->__toString().'!'))
            ->toArray();

        $fields[] = collect($baseType->fieldClasses())
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
