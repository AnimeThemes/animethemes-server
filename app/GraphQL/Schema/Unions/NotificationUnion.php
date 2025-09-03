<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Unions;

use App\GraphQL\Schema\Types\BaseType;
use App\GraphQL\Schema\Types\User\Notification\ExternalProfileSyncedNotificationType;
use App\Notifications\ExternalProfileSyncedNotification;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use RuntimeException;

class NotificationUnion extends BaseUnion
{
    /**
     * The name of the union type.
     * By default, it will be the class name.
     */
    public function getName(): string
    {
        return 'Notification';
    }

    public function description(): string
    {
        return 'Represents the notification types.';
    }

    /**
     * The types that this union can resolve to.
     *
     * @return BaseType[]
     */
    public function baseTypes(): array
    {
        return [
            new ExternalProfileSyncedNotificationType(),
        ];
    }

    public function resolveType($value): Type
    {
        $type = match ($value->type) {
            ExternalProfileSyncedNotification::class => new ExternalProfileSyncedNotificationType(),
            default => throw new RuntimeException("Type not specified for notification {$value->type}"),
        };

        return GraphQL::type($type->getName());
    }
}
