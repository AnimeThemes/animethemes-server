<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Anime\Synonym;

use App\Contracts\Http\Api\Field\SelectableField;
use App\Http\Api\Criteria\Field\Criteria;
use App\Http\Api\Field\Field;
use App\Models\Wiki\Anime\AnimeSynonym;

/**
 * Class SynonymAnimeIdField.
 */
class SynonymAnimeIdField extends Field implements SelectableField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(AnimeSynonym::ATTRIBUTE_ANIME);
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
