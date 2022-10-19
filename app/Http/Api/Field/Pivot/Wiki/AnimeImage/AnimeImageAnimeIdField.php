<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Pivot\Wiki\AnimeImage;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\SelectableField;
use App\Http\Api\Criteria\Field\Criteria;
use App\Http\Api\Field\Field;
use App\Models\Wiki\Anime;
use App\Pivots\Wiki\AnimeImage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Class AnimeImageAnimeIdField.
 */
class AnimeImageAnimeIdField extends Field implements CreatableField, SelectableField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(AnimeImage::ATTRIBUTE_ANIME);
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
            Rule::exists(Anime::TABLE, Anime::ATTRIBUTE_ID),
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
        // Needed to match anime relation.
        return true;
    }
}
