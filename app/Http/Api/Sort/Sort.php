<?php

declare(strict_types=1);

namespace App\Http\Api\Sort;

use App\Enums\Http\Api\QualifyColumn;
use App\Enums\Http\Api\Sort\Direction;

/**
 * Class Sort.
 */
class Sort
{
    /**
     * Create a new sort instance.
     *
     * @param  string  $key
     * @param  string|null  $column
     * @param  QualifyColumn  $qualifyColumn
     */
    public function __construct(
        protected readonly string $key,
        protected readonly ?string $column = null,
        protected readonly QualifyColumn $qualifyColumn = QualifyColumn::YES
    ) {
    }

    /**
     * Get sort key value.
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Get sort column.
     *
     * @return string
     */
    public function getColumn(): string
    {
        return $this->column ?? $this->key;
    }

    /**
     * Determine if the column should be qualified for the sort.
     *
     * @return bool
     */
    public function shouldQualifyColumn(): bool
    {
        return QualifyColumn::YES === $this->qualifyColumn;
    }

    /**
     * Format the sort based on direction.
     *
     * @param  Direction  $direction
     * @return string
     */
    public function format(Direction $direction): string
    {
        return match ($direction) {
            Direction::ASCENDING => $this->getKey(),
            Direction::DESCENDING => "-{$this->getKey()}",
        };
    }
}
