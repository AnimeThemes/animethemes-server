<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field;

use App\Contracts\Http\Api\Field\FieldInterface;

/**
 * Class Field.
 */
abstract class Field implements FieldInterface
{
    /**
     * Create a new field instance.
     *
     * @param  string  $key
     * @param  string|null  $searchField
     * @param  string|null  $sortField
     */
    public function __construct(
        protected string $key,
        protected ?string $searchField = null,
        protected ?string $sortField = null
    ) {
    }

    /**
     * Get the field key.
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Get the search field.
     *
     * @return string|null
     */
    public function getSearchField(): ?string
    {
        return $this->searchField ?? $this->key;
    }

    /**
     * Get the sort field.
     *
     * @return string|null
     */
    public function getSortField(): ?string
    {
        return $this->sortField ?? $this->getSearchField();
    }
}
