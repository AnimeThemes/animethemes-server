<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field;

use App\Contracts\Http\Api\Field\FieldInterface;
use App\Scout\Elasticsearch\Api\Schema\Schema;

/**
 * Class Field.
 */
abstract class Field implements FieldInterface
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     * @param  string  $key
     * @param  string|null  $searchField
     * @param  string|null  $sortField
     */
    public function __construct(
        protected readonly Schema $schema,
        protected readonly string $key,
        protected readonly ?string $searchField = null,
        protected readonly ?string $sortField = null
    ) {}

    /**
     * Get the schema.
     *
     * @return Schema
     */
    public function schema(): Schema
    {
        return $this->schema;
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
