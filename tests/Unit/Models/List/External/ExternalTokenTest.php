<?php

declare(strict_types=1);

use App\Models\Auth\User;
use App\Models\List\External\ExternalToken;
use App\Models\List\ExternalProfile;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Znck\Eloquent\Relations\BelongsToThrough;

test('nameable', function (): void {
    $token = ExternalToken::factory()
        ->createOne();

    $this->assertIsString($token->getName());
});

test('has subtitle', function (): void {
    $token = ExternalToken::factory()
        ->for(ExternalProfile::factory())
        ->createOne();

    $this->assertIsString($token->getSubtitle());
});

test('profile', function (): void {
    $token = ExternalToken::factory()
        ->for(ExternalProfile::factory())
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $token->externalprofile());
    $this->assertInstanceOf(ExternalProfile::class, $token->externalprofile()->first());
});

test('user', function (): void {
    $token = ExternalToken::factory()
        ->for(ExternalProfile::factory()->for(User::factory()))
        ->createOne();

    $this->assertInstanceOf(BelongsToThrough::class, $token->user());
    $this->assertInstanceOf(User::class, $token->user()->first());
});
