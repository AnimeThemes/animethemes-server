<?php

declare(strict_types=1);

namespace App\Concerns\Http\Requests\Api;

use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Schema\Schema;
use App\Rules\Api\DistinctIgnoringDirectionRule;
use App\Rules\Api\RandomSoleRule;
use Illuminate\Support\Collection;

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
     * @return Collection
     */
    protected function formatAllowedSortValues(Schema $schema): Collection
    {
        $allowedSorts = collect();

        foreach ($schema->sorts() as $sort) {
            foreach (Direction::getInstances() as $direction) {
                $formattedSort = $sort->format($direction);
                if (! $allowedSorts->contains($formattedSort)) {
                    $allowedSorts->push($formattedSort);
                }
            }
        }

        return $allowedSorts;
    }

    /**
     * Restrict allowed sorts for schema.
     *
     * @param  string  $param
     * @param  Schema  $schema
     * @return array[]
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
