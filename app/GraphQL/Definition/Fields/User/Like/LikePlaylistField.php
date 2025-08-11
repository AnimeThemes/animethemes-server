<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\User\Like;

use App\Contracts\GraphQL\Fields\BindableField;
use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\DeletableField;
use App\GraphQL\Controllers\User\LikeController;
use App\GraphQL\Definition\Fields\Field;
use App\Models\List\Playlist;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Str;

class LikePlaylistField extends Field implements BindableField, CreatableField, DeletableField
{
    public function __construct()
    {
        parent::__construct('playlist');
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The hashid of the playlist to like';
    }

    /**
     * The type returned by the field.
     */
    public function baseType(): Type
    {
        return Type::string();
    }

    /**
     * Get the model that the field should bind to.
     *
     * @return class-string<Playlist>
     */
    public function bindTo(): string
    {
        return Playlist::class;
    }

    /**
     * Get the column that the field should use to bind.
     */
    public function bindUsingColumn(): string
    {
        return Playlist::ATTRIBUTE_HASHID;
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
            Str::of('prohibits:')->append(LikeController::ATTRIBUTE_VIDEO)->__toString(),
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
            Str::of('prohibits:')->append(LikeController::ATTRIBUTE_VIDEO)->__toString(),
        ];
    }
}
