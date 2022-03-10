<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Video;

use App\Enums\Models\Wiki\VideoSource;
use App\Http\Api\Criteria\Field\Criteria;
use App\Http\Api\Field\EnumField;
use App\Models\Wiki\Video;

/**
 * Class VideoSourceField.
 */
class VideoSourceField extends EnumField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Video::ATTRIBUTE_SOURCE, VideoSource::class);
    }

    /**
     * Determine if the field should be included in the select clause of our query.
     *
     * @param  Criteria|null  $criteria
     * @return bool
     */
    public function shouldSelect(?Criteria $criteria): bool
    {
        // The tags attribute is dependent on this field.
        return parent::shouldSelect($criteria) || $criteria->isAllowedField(Video::ATTRIBUTE_TAGS);
    }
}
