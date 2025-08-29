<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field;

use App\Contracts\Http\Api\Field\FieldInterface;
use App\Scout\Elasticsearch\Api\Schema\Schema;

abstract class Field implements FieldInterface
{
    public function __construct(
        protected readonly Schema $schema,
        protected readonly string $key,
        protected readonly ?string $searchField = null,
        protected readonly ?string $sortField = null
    ) {}

    /**
     * Get the schema.
     */
    public function schema(): Schema
    {
        return $this->schema;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Get the search field.
     */
    public function getSearchField(): ?string
    {
        return $this->searchField ?? $this->key;
    }

    /**
     * Get the sort field.
     */
    public function getSortField(): ?string
    {
        return $this->sortField ?? $this->getSearchField();
    }
}
