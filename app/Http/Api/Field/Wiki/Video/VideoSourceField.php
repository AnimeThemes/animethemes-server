<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Video;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Enums\Models\Wiki\VideoSource;
use App\Http\Api\Field\EnumField;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Video;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

/**
 * Class VideoSourceField.
 */
class VideoSourceField extends EnumField implements CreatableField, UpdatableField
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Video::ATTRIBUTE_SOURCE, VideoSource::class);
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
            'sometimes',
            'required',
            new Enum(VideoSource::class),
        ];
    }

    /**
     * Determine if the field should be included in the select clause of our query.
     *
     * @param  Query  $query
     * @param  Schema  $schema
     * @return bool
     */
    public function shouldSelect(Query $query, Schema $schema): bool
    {
        $tagsField = new VideoTagsField($this->schema);

        // The tags attribute is dependent on this field.
        return parent::shouldSelect($query, $schema) || $tagsField->shouldRender($query);
    }

    /**
     * Set the update validation rules for the field.
     *
     * @param  Request  $request
     * @return array
     */
    public function getUpdateRules(Request $request): array
    {
        return [
            'sometimes',
            'required',
            new Enum(VideoSource::class),
        ];
    }
}
