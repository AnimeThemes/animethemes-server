<?php

declare(strict_types=1);

namespace App\Http\Resources\User\Collection;

use App\Http\Resources\BaseCollection;
use App\Http\Resources\User\Resource\NotificationResource;
use App\Models\User\Notification;
use Illuminate\Http\Request;

class NotificationCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'notifications';

    /**
     * Transform the resource into a JSON array.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(fn (Notification $notification) => new NotificationResource($notification, $this->query))->all();
    }
}
