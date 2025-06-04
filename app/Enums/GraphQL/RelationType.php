<?php

declare(strict_types=1);

namespace App\Enums\GraphQL;

/**
 * Enum RelationType.
 */
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
                    ...$parameters,
                ],
            ],
            RelationType::HAS_MANY => [
                'hasMany' => $parameters,
            ],
            RelationType::HAS_ONE => [
                'hasOne' => $parameters,
            ],
            RelationType::MORPH_MANY => [
                'morphMany' => $parameters,
            ],
            RelationType::MORPH_TO => [
                'morphTo' => $parameters,
            ],
        };
    }
}
