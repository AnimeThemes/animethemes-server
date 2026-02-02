<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Auth;

use App\Http\Api\Field\Auth\User\UserIdField;
use App\Http\Api\Field\Auth\User\UserNameField;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\List\PlaylistSchema;
use App\Http\Resources\Auth\Resource\UserJsonResource;
use App\Models\Auth\User;

class UserSchema extends EloquentSchema
{
    public function type(): string
    {
        return UserJsonResource::$wrap;
    }

    /**
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([
            new AllowedInclude(new PlaylistSchema(), User::RELATION_PLAYLISTS),
        ]);
    }

    /**
     * @return Field[]
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function fields(): array
    {
        return [
            new UserIdField($this),
            new UserNameField($this),
        ];
    }
}
