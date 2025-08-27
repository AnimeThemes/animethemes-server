<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\User\Notification;

use App\Enums\Models\User\NotificationType;
use App\GraphQL\Definition\Fields\EnumField;
use App\Models\User\Notification;
use GraphQL\Type\Definition\ResolveInfo;

class NotificationTypeField extends EnumField
{
    public function __construct()
    {
        parent::__construct(Notification::ATTRIBUTE_TYPE, NotificationType::class, nullable: false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The type of the notification';
    }

    /**
     * Resolve the field.
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo): mixed
    {
        return NotificationType::resolveType($root)->name;
    }
}
