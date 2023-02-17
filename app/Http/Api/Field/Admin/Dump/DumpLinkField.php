<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Admin\Dump;

use App\Contracts\Http\Api\Field\RenderableField;
use App\Http\Api\Field\Field;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\Admin\Resource\DumpResource;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DumpLinkField.
 */
class DumpLinkField extends Field implements RenderableField
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, DumpResource::ATTRIBUTE_LINK);
    }

    /**
     * Determine if the field should be displayed to the user.
     *
     * @param  Query  $query
     * @return bool
     */
    public function shouldRender(Query $query): bool
    {
        $criteria = $query->getFieldCriteria($this->schema->type());

        return $criteria === null || $criteria->isAllowedField($this->getKey());
    }

    /**
     * Get the value to display to the user.
     *
     * @param  Model  $model
     * @return string
     */
    public function render(Model $model): string
    {
        return route('dump.show', $model);
    }
}
