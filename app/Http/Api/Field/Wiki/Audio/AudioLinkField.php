<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Audio;

use App\Contracts\Http\Api\Field\RenderableField;
use App\Http\Api\Field\Field;
use App\Http\Api\Query\ReadQuery;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\Wiki\Resource\AudioResource;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AudioLinkField.
 */
class AudioLinkField extends Field implements RenderableField
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, AudioResource::ATTRIBUTE_LINK);
    }

    /**
     * Determine if the field should be displayed to the user.
     *
     * @param  ReadQuery  $query
     * @return bool
     */
    public function shouldRender(ReadQuery $query): bool
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
        return route('audio.show', $model);
    }
}
