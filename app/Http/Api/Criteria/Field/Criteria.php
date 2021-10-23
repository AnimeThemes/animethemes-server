<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Field;

use Illuminate\Support\Collection;

/**
 * Class Criteria.
 */
class Criteria
{
    /**
     * The type that this sparse fieldsets mapping belongs to.
     *
     * @var string
     */
    protected string $type;

    /**
     * The fields to include for the type.
     *
     * @var Collection<string>
     */
    protected Collection $fields;

    /**
     * Create a new criteria instance.
     *
     * @param  string  $type
     * @param  Collection<string>  $fields
     */
    public function __construct(string $type, Collection $fields)
    {
        $this->type = $type;
        $this->fields = $fields;
    }

    /**
     * Get the type that this sparse fieldsets mapping belongs to.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get the fields to include for the type.
     *
     * @return Collection<string>
     */
    public function getFields(): Collection
    {
        return $this->fields;
    }

    /**
     * Is the field allowed?
     *
     * @param  string  $field
     * @return bool
     */
    public function isAllowedField(string $field): bool
    {
        return $this->fields->contains($field);
    }
}
