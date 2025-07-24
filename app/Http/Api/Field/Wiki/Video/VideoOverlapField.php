<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Video;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Http\Api\Field\EnumField;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Video;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class VideoOverlapField extends EnumField implements CreatableField, UpdatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Video::ATTRIBUTE_OVERLAP, VideoOverlap::class);
    }

    /**
     * Set the creation validation rules for the field.
     *
     * @return array
     */
    public function getCreationRules(Request $request): array
    {
        return [
            'sometimes',
            'required',
            new Enum(VideoOverlap::class),
        ];
    }

    /**
     * Set the update validation rules for the field.
     *
     * @return array
     */
    public function getUpdateRules(Request $request): array
    {
        return [
            'sometimes',
            'required',
            new Enum(VideoOverlap::class),
        ];
    }
}
