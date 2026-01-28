<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\User\Like;

use App\Contracts\GraphQL\Fields\BindableField;
use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\DeletableField;
use App\GraphQL\Resolvers\User\LikeResolver;
use App\GraphQL\Schema\Fields\Field;
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
     * @param  array<string, mixed>  $args
     */
    public function getCreationRules(array $args): array
    {
        return [
            Str::of('prohibits:')->append(LikeResolver::ATTRIBUTE_ENTRY)->__toString(),
            'required_without_all:'.implode(',', [
                LikeResolver::ATTRIBUTE_ENTRY,
            ]),
        ];
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function getDeleteRules(array $args): array
    {
        return [
            Str::of('prohibits:')->append(LikeResolver::ATTRIBUTE_ENTRY)->__toString(),
            'required_without_all:'.implode(',', [
                LikeResolver::ATTRIBUTE_ENTRY,
            ]),
        ];
    }
}
