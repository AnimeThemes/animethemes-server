<?php

declare(strict_types=1);

namespace App\Http\Api\Sort;

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
     */
    public function __construct(protected readonly string $key, protected readonly ?string $column = null)
    {
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
     * Format the sort based on direction.
     *
     * @param  Direction  $direction
     * @return string
     */
    public function format(Direction $direction): string
    {
        return match ($direction->value) {
            Direction::DESCENDING => "-{$this->getKey()}",
            default => $this->getKey(),
        };
    }
}
