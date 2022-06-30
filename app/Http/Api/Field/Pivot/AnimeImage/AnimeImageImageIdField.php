<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Pivot\AnimeImage;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\SelectableField;
use App\Http\Api\Criteria\Field\Criteria;
use App\Http\Api\Field\Field;
use App\Models\Wiki\Image;
use App\Pivots\AnimeImage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Class AnimeImageImageIdField.
 */
class AnimeImageImageIdField extends Field implements CreatableField, SelectableField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(AnimeImage::ATTRIBUTE_IMAGE);
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
     * @param  Criteria|null  $criteria
     * @return bool
     */
    public function shouldSelect(?Criteria $criteria): bool
    {
        // Needed to match image relation.
        return true;
    }
}
