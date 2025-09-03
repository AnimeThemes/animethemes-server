<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Inputs\Base;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\RequiredOnCreation;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Inputs\Input;
use App\GraphQL\Schema\Inputs\Relations\CreateBelongsToInput;
use App\GraphQL\Schema\Inputs\Relations\CreateBelongsToManyInput;
use App\GraphQL\Schema\Inputs\Relations\CreateHasManyInput;
use App\GraphQL\Schema\Types\EloquentType;
use App\GraphQL\Support\InputField;
use App\GraphQL\Support\Relations\BelongsToManyRelation;
use App\GraphQL\Support\Relations\BelongsToRelation;
use App\GraphQL\Support\Relations\HasManyRelation;
use App\GraphQL\Support\Relations\Relation;
use Illuminate\Support\Arr;

class CreateInput extends Input
{
    public function __construct(
        protected EloquentType $type,
    ) {}

    public function getName(): string
    {
        return "Create{$this->type->getName()}Input";
    }

    /**
     * @return InputField[]
     */
    public function fieldClasses(): array
    {
        $fields = [];

        $baseType = $this->type;

        $fields[] = collect($baseType->fieldClasses())
            ->filter(fn (Field $field) => $field instanceof CreatableField) // and reportable field?
            ->map(
                fn (Field&CreatableField $field) => new InputField($field->getName(), $field->type().($field instanceof RequiredOnCreation ? '!' : ''))
            )
            ->toArray();

        $fields[] = collect($baseType->relations())
            ->mapWithKeys(function (Relation $relation) {
                $baseType = $relation->getBaseType();
                if (! $baseType instanceof EloquentType) {
                    return [];
                }

                return match (true) {
                    $relation instanceof BelongsToRelation => [$relation->getName() => new CreateBelongsToInput($baseType)],
                    $relation instanceof HasManyRelation => [$relation->getName() => new CreateHasManyInput($baseType)],
                    $relation instanceof BelongsToManyRelation => [$relation->getName() => new CreateBelongsToManyInput($relation->getEdgeType()->getPivotType())],
                    default => [],
                };
            })
            ->map(fn (Input $input, string $name) => new InputField($name, $input->getName()))
            ->toArray();

        return Arr::flatten($fields);
    }
}
