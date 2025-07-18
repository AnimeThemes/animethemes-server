<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\List\Playlist\PlaylistTrack;

use App\Contracts\GraphQL\Fields\BindableField;
use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\RequiredOnCreation;
use App\Contracts\GraphQL\Fields\RequiredOnUpdate;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Definition\Fields\Field;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use GraphQL\Type\Definition\Type;

/**
 * Class PlaylistTrackPlaylistField.
 */
class PlaylistTrackPlaylistField extends Field implements BindableField, CreatableField, RequiredOnCreation, RequiredOnUpdate, UpdatableField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(PlaylistTrack::RELATION_PLAYLIST, nullable: false);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The playlist of the track';
    }

    /**
     * The type returned by the field.
     *
     * @return Type
     */
    public function type(): Type
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
     *
     * @return string
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
            'required',
        ];
    }

    /**
     * Set the update validation rules for the field.
     *
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
