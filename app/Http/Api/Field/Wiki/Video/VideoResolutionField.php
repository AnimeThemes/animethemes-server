<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Video;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\IntField;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Video;
use Illuminate\Http\Request;

class VideoResolutionField extends IntField implements CreatableField, UpdatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Video::ATTRIBUTE_RESOLUTION);
    }

    public function getCreationRules(Request $request): array
    {
        return [
            'sometimes',
            'required',
            'integer',
            'min:360',
            'max:1080',
        ];
    }

    public function shouldSelect(Query $query, Schema $schema): bool
    {
        $tagsField = new VideoTagsField($this->schema);
        // The tags attribute is dependent on this field.
        if (parent::shouldSelect($query, $schema)) {
            return true;
        }

        return $tagsField->shouldRender($query);
    }

    public function getUpdateRules(Request $request): array
    {
        return [
            'sometimes',
            'required',
            'integer',
            'min:360',
            'max:1080',
        ];
    }
}
