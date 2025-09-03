<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\User\Like;

use App\Contracts\GraphQL\Fields\BindableField;
use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\DeletableField;
use App\GraphQL\Controllers\User\LikeController;
use App\GraphQL\Schema\Fields\Field;
use App\Models\Wiki\Video;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class LikeVideoField extends Field implements BindableField, CreatableField, DeletableField
{
    public function __construct()
    {
        parent::__construct('video');
    }

    public function description(): string
    {
        return 'The id of the video to like';
    }

    /**
     * The type returned by the field.
     */
    public function baseType(): Type
    {
        return Type::int();
    }

    /**
     * The resolver to cast the model.
     */
    public function bindResolver(array $args): Video
    {
        return Video::query()
            ->where(Video::ATTRIBUTE_ID, Arr::get($args, $this->getName()))
            ->firstOrFail();
    }

    /**
     * @param  array<string, mixed>  $args
     * @return array
     */
    public function getCreationRules(array $args): array
    {
        return [
            Str::of('prohibits:')->append(LikeController::ATTRIBUTE_PLAYLIST)->__toString(),
        ];
    }

    /**
     * @param  array<string, mixed>  $args
     * @return array
     */
    public function getDeleteRules(array $args): array
    {
        return [
            Str::of('prohibits:')->append(LikeController::ATTRIBUTE_PLAYLIST)->__toString(),
        ];
    }
}
