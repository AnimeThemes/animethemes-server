<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Http\Api\Query\Query;
use App\Http\Resources\User\Collection\NotificationCollection;
use App\Models\Auth\User;
use App\Models\User\Notification;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\get;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('protected', function () {
    $response = get(route('api.me.notification.index'));

    $response->assertUnauthorized();
});

test('forbidden if missing permission', function () {
    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = get(route('api.me.notification.index'));

    $response->assertForbidden();
});

test('only sees owned notifications', function () {
    Notification::factory()
        ->for(User::factory(), Notification::RELATION_NOTIFIABLE)
        ->count(fake()->randomDigitNotNull())
        ->create();

    $user = User::factory()->withPermissions(CrudPermission::VIEW->format(Notification::class))->createOne();

    $notificationCount = fake()->randomDigitNotNull();

    $notifications = Notification::factory()
        ->for($user, Notification::RELATION_NOTIFIABLE)
        ->count($notificationCount)
        ->create()
        ->sortBy(Model::CREATED_AT);

    Sanctum::actingAs($user);

    $response = get(route('api.me.notification.index'));

    $response->assertJsonCount($notificationCount, NotificationCollection::$wrap);

    $response->assertJson(
        json_decode(
            json_encode(
                new NotificationCollection($notifications, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
