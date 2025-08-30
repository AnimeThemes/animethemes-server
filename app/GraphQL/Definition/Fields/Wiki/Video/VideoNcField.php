<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Video;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Definition\Fields\BooleanField;
use App\Models\Wiki\Video;

class VideoNcField extends BooleanField implements CreatableField, UpdatableField
{
    public function __construct()
    {
        parent::__construct(Video::ATTRIBUTE_NC, nullable: false);
    }

    public function description(): string
    {
        return 'Is the video creditless?';
    }

    /**
     * @param  array<string, mixed>  $args
     * @return array
     */
    public function getCreationRules(array $args): array
    {
        return [
            'sometimes',
            'required',
            'boolean',
        ];
    }

    /**
     * @param  array<string, mixed>  $args
     * @return array
     */
    public function getUpdateRules(array $args): array
    {
        return [
            'sometimes',
            'required',
            'boolean',
        ];
    }
}
