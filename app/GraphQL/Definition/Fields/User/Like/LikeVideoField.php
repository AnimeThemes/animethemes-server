<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\User\Like;

use App\Contracts\GraphQL\Fields\BindableField;
use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\DeletableField;
use App\GraphQL\Controllers\User\LikeController;
use App\GraphQL\Definition\Fields\Field;
use App\Models\Wiki\Video;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Str;

class LikeVideoField extends Field implements BindableField, CreatableField, DeletableField
{
    public function __construct()
    {
        parent::__construct('video');
    }

    /**
     * The description of the field.
     */
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
     * Get the model that the field should bind to.
     *
     * @return class-string<Video>
     */
    public function bindTo(): string
    {
        return Video::class;
    }

    /**
     * Get the column that the field should use to bind.
     */
    public function bindUsingColumn(): string
    {
        return Video::ATTRIBUTE_ID;
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
            Str::of('prohibits:')->append(LikeController::ATTRIBUTE_PLAYLIST)->__toString(),
        ];
    }

    /**
     * Set the delete validation rules for the field.
     *
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
