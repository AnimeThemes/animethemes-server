<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\User;

use App\Http\Api\Field\Base\CreatedAtField;
use App\Http\Api\Field\Base\UpdatedAtField;
use App\Http\Api\Field\Base\UuidField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\User\Notification\NotificationDataField;
use App\Http\Api\Field\User\Notification\NotificationNotifiableIdField;
use App\Http\Api\Field\User\Notification\NotificationNotifiableTypeField;
use App\Http\Api\Field\User\Notification\NotificationReadAtField;
use App\Http\Api\Field\User\Notification\NotificationTypeField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Resources\User\Resource\NotificationResource;

/**
 * Class NotificationSchema.
 */
class NotificationSchema extends EloquentSchema
{
    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return NotificationResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([]);
    }

    /**
     * Get the direct fields of the resource.
     *
     * @return Field[]
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function fields(): array
    {
        return [
            new CreatedAtField($this),
            new UpdatedAtField($this),
            new UuidField($this, 'id'),
            new NotificationTypeField($this),
            new NotificationNotifiableTypeField($this),
            new NotificationNotifiableIdField($this),
            new NotificationDataField($this),
            new NotificationReadAtField($this),
        ];
    }
}
