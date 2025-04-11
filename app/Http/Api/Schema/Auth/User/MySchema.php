<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Auth\User;

use App\Http\Api\Field\Auth\User\UserEmailField;
use App\Http\Api\Field\Auth\User\UserEmailVerifiedAtField;
use App\Http\Api\Field\Auth\User\UserNameField;
use App\Http\Api\Field\Auth\User\UserTwoFactorConfirmedAtField;
use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\Auth\PermissionSchema;
use App\Http\Api\Schema\Auth\RoleSchema;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\List\ExternalProfileSchema;
use App\Http\Api\Schema\List\PlaylistSchema;
use App\Http\Api\Schema\User\NotificationSchema;
use App\Http\Resources\Auth\User\Resource\MyResource;
use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MySchema.
 */
class MySchema extends EloquentSchema
{
    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return MyResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([
            new AllowedInclude(new ExternalProfileSchema(), User::RELATION_EXTERNAL_PROFILES),
            new AllowedInclude(new NotificationSchema(), User::RELATION_NOTIFICATIONS),
            new AllowedInclude(new PermissionSchema(), User::RELATION_PERMISSIONS),
            new AllowedInclude(new PlaylistSchema(), User::RELATION_PLAYLISTS),
            new AllowedInclude(new RoleSchema(), User::RELATION_ROLES),
            new AllowedInclude(new PermissionSchema(), User::RELATION_ROLES_PERMISSIONS),
        ]);
    }

    /**
     * Get the direct fields of the resource.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return array_merge(
            parent::fields(),
            [
                new IdField($this, User::ATTRIBUTE_ID),
                new UserNameField($this),
                new UserEmailField($this),
                new UserEmailVerifiedAtField($this),
                new UserTwoFactorConfirmedAtField($this),
            ],
        );
    }

    /**
     * Resolve the owner model of the schema.
     *
     * @return Model
     */
    public function model(): Model
    {
        return new User();
    }
}
