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
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class LikePlaylistField extends Field implements BindableField, CreatableField, DeletableField
{
    public function __construct()
    {
        parent::__construct('playlist');
    }

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
     * The resolver to cast the model.
     */
    public function bindResolver(array $args): Playlist
    {
        return Playlist::query()
            ->where(Playlist::ATTRIBUTE_HASHID, Arr::get($args, $this->getName()))
            ->firstOrFail();
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
