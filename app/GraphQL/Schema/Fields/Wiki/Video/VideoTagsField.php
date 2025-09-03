<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Wiki\Video;

use App\GraphQL\Schema\Fields\StringField;
use App\Models\Wiki\Video;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Database\Eloquent\Model;

class VideoTagsField extends StringField
{
    public function __construct()
    {
        parent::__construct(Video::ATTRIBUTE_TAGS);
    }

    public function description(): string
    {
        return 'The attributes used to distinguish the file within the context of a theme';
    }

    /**
     * Resolve the field.
     *
     * @param  Model  $root
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo): mixed
    {
        return implode('', $root->getAttribute($this->getColumn()));
    }
}
