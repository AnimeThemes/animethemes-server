<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Video;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\RequiredOnCreation;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\Enums\Models\Wiki\VideoOverlap;
use App\GraphQL\Definition\Fields\EnumField;
use App\Models\Wiki\Video;
use Illuminate\Validation\Rules\Enum;

class VideoOverlapField extends EnumField implements CreatableField, RequiredOnCreation, UpdatableField
{
    public function __construct()
    {
        parent::__construct(Video::ATTRIBUTE_OVERLAP, VideoOverlap::class);
    }

    public function description(): string
    {
        return 'The degree to which the sequence and episode content overlap';
    }

    /**
     * Set the creation validation rules for the field.
     *
     * @param  array<string, mixed>  $args
     * @return array
     */
    public function getCreationRules(array $args): array
    {
        return [
            'required',
            new Enum(VideoOverlap::class),
        ];
    }

    /**
     * Set the update validation rules for the field.
     *
     * @param  array<string, mixed>  $args
     * @return array
     */
    public function getUpdateRules(array $args): array
    {
        return [
            'sometimes',
            'required',
            new Enum(VideoOverlap::class),
        ];
    }
}
