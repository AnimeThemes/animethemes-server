<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth\User\Me\List;

use App\Actions\Http\Api\IndexAction;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\User\NotificationSchema;
use App\Http\Controllers\Api\BaseController;
use App\Http\Middleware\Auth\Authenticate;
use App\Http\Requests\Api\IndexRequest;
use App\Http\Resources\User\Collection\NotificationCollection;
use App\Models\Auth\User;
use App\Models\User\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class MyNotificationController extends BaseController
{
    public function __construct()
    {
        $this->middleware(Authenticate::using('sanctum'));
        parent::__construct(Notification::class, 'notification');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  IndexAction  $action
     */
    public function index(IndexRequest $request, IndexAction $action): NotificationCollection
    {
        $query = new Query($request->validated());

        /** @var User $user */
        $user = Auth::user();

        $builder = $user->notifications()->getQuery();

        $notifications = $action->index($builder, $query, $request->schema());

        return new NotificationCollection($notifications, $query);
    }

    /**
     * Mark an unread notification as read.
     */
    public function read(Notification $notification): JsonResponse
    {
        $this->authorize('read', $notification);

        $notification->markAsRead();

        return new JsonResponse([
            'message' => __('notifications.read'),
        ]);
    }

    /**
     * Mark a read notification as unread.
     */
    public function unread(Notification $notification): JsonResponse
    {
        $this->authorize('unread', $notification);

        $notification->markAsUnread();

        return new JsonResponse([
            'message' => __('notifications.unread'),
        ]);
    }

    /**
     * Mark unread notifications as read.
     */
    public function readall(): JsonResponse
    {
        $this->authorize('readall', Notification::class);

        /** @var User $user */
        $user = Auth::user();

        $user->unreadNotifications()->update([Notification::ATTRIBUTE_READ_AT => now()]);

        return new JsonResponse([
            'message' => __('notifications.read_all'),
        ]);
    }

    /**
     * Get the underlying schema.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function schema(): NotificationSchema
    {
        return new NotificationSchema();
    }
}
