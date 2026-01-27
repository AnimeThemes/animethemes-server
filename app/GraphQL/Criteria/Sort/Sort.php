<?php

declare(strict_types=1);

namespace App\GraphQL\Criteria\Sort;

use App\Enums\GraphQL\QualifyColumn;

class Sort
{
    public function __construct(
        protected readonly string $key,
        protected readonly ?string $column = null,
        protected readonly QualifyColumn $qualifyColumn = QualifyColumn::YES
    ) {}

    /**
     * Get sort key value.
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Get sort column.
     */
    public function getColumn(): string
    {
        return $this->column ?? $this->key;
    }

    /**
     * Determine if the column should be qualified for the sort.
     */
    public function shouldQualifyColumn(): bool
    {
        return $this->qualifyColumn === QualifyColumn::YES;
    }
}
