<?php

declare(strict_types=1);

namespace App\Enums\GraphQL;

enum RelationType
{
    case BELONGS_TO;
    case BELONGS_TO_MANY;
    case HAS_MANY;
    case HAS_ONE;
    case MORPH_MANY;
    case MORPH_TO;

    /**
     * Get the directive of the relation type.
     *
     * @param  array<string, mixed>  $parameters
     * @return array<string, array>
     */
    public function getDirective(array $parameters): array
    {
        $parameters = array_filter($parameters, fn ($value) => filled($value));

        return match ($this) {
            RelationType::BELONGS_TO => [
                'belongsTo' => $parameters,
            ],
            RelationType::BELONGS_TO_MANY => [
                'belongsToMany' => [
                    'type' => 'CONNECTION',
                    'defaultCount' => 1000000000,
                    ...$parameters,
                ],
            ],
            RelationType::HAS_MANY => [
                'hasMany' => [
                    'type' => 'PAGINATOR',
                    'defaultCount' => 1000000000,
                    ...$parameters,
                ],
            ],
            RelationType::HAS_ONE => [
                'hasOne' => $parameters,
            ],
            RelationType::MORPH_MANY => [
                'morphMany' => [
                    'type' => 'PAGINATOR',
                    'defaultCount' => 1000000000,
                    ...$parameters,
                ],
            ],
            RelationType::MORPH_TO => [
                'morphTo' => $parameters,
            ],
        };
    }
}
