<?php

declare(strict_types=1);

namespace App\Concerns\Http\Requests\Api;

use App\Contracts\Http\Api\Schema\SchemaInterface;
use App\Enums\Http\Api\Sort\Direction;
use App\Rules\Api\DistinctIgnoringDirectionRule;
use App\Rules\Api\RandomSoleRule;

trait ValidatesSorts
{
    use ValidatesParameters;

    /**
     * Get allowed sorts for schema.
     *
     * @return string[]
     */
    protected function formatAllowedSortValues(SchemaInterface $schema): array
    {
        $allowedSorts = [];

        foreach ($schema->sorts() as $sort) {
            foreach (Direction::cases() as $direction) {
                $allowedSorts[] = $sort->format($direction);
            }
        }

        return array_unique($allowedSorts);
    }

    /**
     * Restrict allowed sorts for schema.
     *
     * @return array<string, array>
     */
    protected function restrictAllowedSortValues(string $param, SchemaInterface $schema): array
    {
        return $this->restrictAllowedValues(
            $param,
            $this->formatAllowedSortValues($schema),
            [new DistinctIgnoringDirectionRule(), new RandomSoleRule()]
        );
    }
}
