<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Auth\User\Me\Notification;

use App\Enums\Auth\CrudPermission;
use App\Http\Api\Query\Query;
use App\Http\Resources\User\Collection\NotificationCollection;
use App\Models\Auth\User;
use App\Models\User\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class MyNotificationIndexTest.
 */
class MyNotificationIndexTest extends TestCase
{
    use WithFaker;

    /**
     * The My Notification Index Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function test_protected(): void
    {
        $response = $this->get(route('api.me.notification.index'));

        $response->assertUnauthorized();
    }

    /**
     * The My Notification Index Endpoint shall forbid users without the view notification permission.
     *
     * @return void
     */
    public function test_forbidden_if_missing_permission(): void
    {
        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->get(route('api.me.notification.index'));

        $response->assertForbidden();
    }

    /**
     * The My Notification Index Endpoint shall return notifications owned by the user.
     *
     * @return void
     */
    public function test_only_sees_owned_notifications(): void
    {
        Notification::factory()
            ->for(User::factory(), Notification::RELATION_NOTIFIABLE)
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $user = User::factory()->withPermissions(CrudPermission::VIEW->format(Notification::class))->createOne();

        $notificationCount = $this->faker->randomDigitNotNull();

        $notifications = Notification::factory()
            ->for($user, Notification::RELATION_NOTIFIABLE)
            ->count($notificationCount)
            ->create()
            ->sortBy(Model::CREATED_AT);

        Sanctum::actingAs($user);

        $response = $this->get(route('api.me.notification.index'));

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
    }
}
