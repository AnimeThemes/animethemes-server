<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Wiki\Video;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\GraphQL\Schema\Fields\Field;
use App\Models\Wiki\Video;
use GraphQL\Type\Definition\Type;

class VideoPriorityField extends Field implements DisplayableField
{
    public function __construct()
    {
        parent::__construct(Video::ATTRIBUTE_PRIORITY, nullable: false);
    }

    public function description(): string
    {
        return 'The priority value for the video';
    }

    public function baseType(): Type
    {
        return Type::int();
    }

    public function canBeDisplayed(): bool
    {
        return true;
    }
}
