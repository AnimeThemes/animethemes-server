<?php

declare(strict_types=1);

use App\Models\Auth\User;
use App\Models\List\External\ExternalToken;
use App\Models\List\ExternalProfile;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Znck\Eloquent\Relations\BelongsToThrough;

test('nameable', function () {
    $token = ExternalToken::factory()
        ->createOne();

    static::assertIsString($token->getName());
});

test('has subtitle', function () {
    $token = ExternalToken::factory()
        ->for(ExternalProfile::factory())
        ->createOne();

    static::assertIsString($token->getSubtitle());
});

test('profile', function () {
    $token = ExternalToken::factory()
        ->for(ExternalProfile::factory())
        ->createOne();

    static::assertInstanceOf(BelongsTo::class, $token->externalprofile());
    static::assertInstanceOf(ExternalProfile::class, $token->externalprofile()->first());
});

test('user', function () {
    $token = ExternalToken::factory()
        ->for(ExternalProfile::factory()->for(User::factory()))
        ->createOne();

    static::assertInstanceOf(BelongsToThrough::class, $token->user());
    static::assertInstanceOf(User::class, $token->user()->first());
});
