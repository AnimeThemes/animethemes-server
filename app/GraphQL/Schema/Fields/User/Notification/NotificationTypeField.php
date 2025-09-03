<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\User\Notification;

use App\Enums\Models\User\NotificationType;
use App\GraphQL\Schema\Fields\EnumField;
use App\Models\User\Notification;
use GraphQL\Type\Definition\ResolveInfo;

class NotificationTypeField extends EnumField
{
    public function __construct()
    {
        parent::__construct(Notification::ATTRIBUTE_TYPE, NotificationType::class, nullable: false);
    }

    public function description(): string
    {
        return 'The type of the notification';
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo): mixed
    {
        return NotificationType::resolveType($root)->name;
    }
}
