<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\List\Playlist\PlaylistTrack;

use App\Contracts\GraphQL\Fields\BindableField;
use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\RequiredOnCreation;
use App\Contracts\GraphQL\Fields\RequiredOnUpdate;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Schema\Fields\Field;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;

class PlaylistTrackPlaylistField extends Field implements BindableField, CreatableField, RequiredOnCreation, RequiredOnUpdate, UpdatableField
{
    final public const FIELD = PlaylistTrack::RELATION_PLAYLIST;

    public function __construct()
    {
        parent::__construct(self::FIELD, nullable: false);
    }

    public function description(): string
    {
        return 'The playlist of the track';
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
     * @param  array<string, mixed>  $args
     * @return array
     */
    public function getCreationRules(array $args): array
    {
        return [
            'required',
        ];
    }

    /**
     * @param  array<string, mixed>  $args
     * @return array
     */
    public function getUpdateRules(array $args): array
    {
        return [
            'required',
        ];
    }
}
