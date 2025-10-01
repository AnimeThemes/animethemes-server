<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Wiki\Video;

use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Schema\Fields\IntField;
use App\Models\Wiki\Video;

class VideoResolutionField extends IntField implements UpdatableField
{
    public function __construct()
    {
        parent::__construct(Video::ATTRIBUTE_RESOLUTION);
    }

    public function description(): string
    {
        return 'The frame height of the file in storage';
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function getUpdateRules(array $args): array
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
