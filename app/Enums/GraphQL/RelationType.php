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
     * @param  array  $parameters
     * @return string
     */
    public function getDirective(array $parameters): string
    {
        $args = collect($parameters)
            ->filter(fn ($value) => !is_null($value) && $value !== '')
            ->map(fn ($value, $key) => "$key: $value")
            ->values()
            ->implode(', ');

        return match ($this) {
            RelationType::BELONGS_TO => "@belongsTo($args)",
            RelationType::BELONGS_TO_MANY => "@belongsToMany(type: CONNECTION, $args)",
            RelationType::HAS_MANY => "@hasMany($args)",
            RelationType::HAS_ONE => "@hasOne($args)",
            RelationType::MORPH_MANY => "@morphMany($args)",
            RelationType::MORPH_TO => "@morphTo($args)",
        };
    }
}
