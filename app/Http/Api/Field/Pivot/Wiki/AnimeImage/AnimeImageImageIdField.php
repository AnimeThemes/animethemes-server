<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Pivot\Wiki\AnimeImage;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\SelectableField;
use App\Http\Api\Field\Field;
use App\Http\Api\Query\ReadQuery;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Image;
use App\Pivots\Wiki\AnimeImage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Class AnimeImageImageIdField.
 */
class AnimeImageImageIdField extends Field implements CreatableField, SelectableField
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, AnimeImage::ATTRIBUTE_IMAGE);
    }

    /**
     * Set the creation validation rules for the field.
     *
     * @param  Request  $request
     * @return array
     */
    public function getCreationRules(Request $request): array
    {
        return [
            'required',
            'integer',
            Rule::exists(Image::TABLE, Image::ATTRIBUTE_ID),
        ];
    }

    /**
     * Determine if the field should be included in the select clause of our query.
     *
     * @param  ReadQuery  $query
     * @return bool
     */
    public function shouldSelect(ReadQuery $query): bool
    {
        // Needed to match image relation.
        return true;
    }
}
