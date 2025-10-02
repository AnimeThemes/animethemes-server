<?php

declare(strict_types=1);

use App\Enums\Auth\SpecialPermission;
use App\Models\Auth\User;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\get;

test('client no forwarded ip not rate limited', function () {
    $response = get(route('api.anime.index'));

    $response->assertHeaderMissing('X-RateLimit-Limit');
    $response->assertHeaderMissing('X-RateLimit-Remaining');
});

test('user with bypass not rate limited', function () {
    $user = User::factory()->withPermissions(SpecialPermission::BYPASS_API_RATE_LIMITER->value)->createOne();

    Sanctum::actingAs($user);

    $response = get(route('api.anime.index'));

    $response->assertHeaderMissing('X-RateLimit-Limit');
    $response->assertHeaderMissing('X-RateLimit-Remaining');
});

test('forwarded ip rate limited', function () {
    $response = $this->withHeader('x-forwarded-ip', fake()->ipv4())->get(route('api.anime.index'));

    $response->assertHeader('X-RateLimit-Limit');
    $response->assertHeader('X-RateLimit-Remaining');
});

test('user without bypass rate limited', function () {
    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->withServerVariables([
        'REMOTE_ADDR' => fake()->ipv4(),
    ])->get(route('api.anime.index'));

    $response->assertHeader('X-RateLimit-Limit');
    $response->assertHeader('X-RateLimit-Remaining');
})->only();
