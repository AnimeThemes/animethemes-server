<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Video;

use App\Http\Api\Criteria\Field\Criteria;
use App\Http\Api\Field\StringField;
use App\Http\Resources\Wiki\Resource\VideoResource;
use App\Models\Wiki\Video;

/**
 * Class VideoBasenameField.
 */
class VideoBasenameField extends StringField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Video::ATTRIBUTE_BASENAME);
    }

    /**
     * Determine if the field should be included in the select clause of our query.
     *
     * @param  Criteria|null  $criteria
     * @return bool
     */
    public function shouldSelect(?Criteria $criteria): bool
    {
        // The link field is dependent on this field to build the route.
        return parent::shouldSelect($criteria) || $criteria->isAllowedField(VideoResource::ATTRIBUTE_LINK);
    }
}
