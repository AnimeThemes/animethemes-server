<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Input\Base;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\RequiredOnCreation;
use App\Contracts\GraphQL\HasRelations;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Input\Input;
use App\GraphQL\Definition\Input\Relations\CreateBelongsToInput;
use App\GraphQL\Definition\Input\Relations\CreateBelongsToManyInput;
use App\GraphQL\Definition\Input\Relations\CreateHasManyInput;
use App\GraphQL\Definition\Types\EloquentType;
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
    ) {
        parent::__construct("Create{$type->getName()}");
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
            ->filter(fn (Field $field) => $field instanceof CreatableField) // and reportable field?
            ->map(
                fn (Field&CreatableField $field) => new InputField($field->getName(), $field->type().($field instanceof RequiredOnCreation ? '!' : ''))
            )
            ->toArray();

        if ($baseType instanceof HasRelations) {
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
        }

        return Arr::flatten($fields);
    }
}
