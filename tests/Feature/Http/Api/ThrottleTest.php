<?php

declare(strict_types=1);

use App\Enums\Auth\SpecialPermission;
use App\Models\Auth\User;
use Laravel\Sanctum\Sanctum;

test('forwarded ip rate limited', function () {
    $response = $this->withHeader('x-forwarded-ip', fake()->ipv4())->get(route('api.anime.index'));

    $response->assertHeader('X-RateLimit-Limit');
    $response->assertHeader('X-RateLimit-Remaining');
});

test('client no forwarded ip not rate limited', function () {
    $response = $this->get(route('api.anime.index'));

    $response->assertHeaderMissing('X-RateLimit-Limit');
    $response->assertHeaderMissing('X-RateLimit-Remaining');
});

test('user not rate limited', function () {
    $user = User::factory()->withPermissions(SpecialPermission::BYPASS_API_RATE_LIMITER->value)->createOne();

    Sanctum::actingAs($user);

    $response = $this->get(route('api.anime.index'));

    $response->assertHeaderMissing('X-RateLimit-Limit');
    $response->assertHeaderMissing('X-RateLimit-Remaining');
});
