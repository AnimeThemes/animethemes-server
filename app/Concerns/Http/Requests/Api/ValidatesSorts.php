<?php

declare(strict_types=1);

namespace App\Concerns\Http\Requests\Api;

use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Schema\Schema;
use App\Rules\Api\DistinctIgnoringDirectionRule;
use App\Rules\Api\RandomSoleRule;

/**
 * Trait ValidatesSorts.
 */
trait ValidatesSorts
{
    use ValidatesParameters;

    /**
     * Get allowed sorts for schema.
     *
     * @param  Schema  $schema
     * @return string[]
     */
    protected function formatAllowedSortValues(Schema $schema): array
    {
        $allowedSorts = [];

        foreach ($schema->sorts() as $sort) {
            foreach (Direction::getInstances() as $direction) {
                $allowedSorts[] = $sort->format($direction);
            }
        }

        return array_unique($allowedSorts);
    }

    /**
     * Restrict allowed sorts for schema.
     *
     * @param  string  $param
     * @param  Schema  $schema
     * @return array<string, array>
     */
    protected function restrictAllowedSortValues(string $param, Schema $schema): array
    {
        return $this->restrictAllowedValues(
            $param,
            $this->formatAllowedSortValues($schema),
            [new DistinctIgnoringDirectionRule(), new RandomSoleRule()]
        );
    }
}
