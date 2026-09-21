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

    expect($token->getName())->toBeString();
});

test('has subtitle', function (): void {
    $token = ExternalToken::factory()
        ->for(ExternalProfile::factory())
        ->createOne();

    expect($token->getSubtitle())->toBeString();
});

test('profile', function (): void {
    $token = ExternalToken::factory()
        ->for(ExternalProfile::factory())
        ->createOne();

    expect($token->externalprofile())->toBeInstanceOf(BelongsTo::class);
    expect($token->externalprofile()->first())->toBeInstanceOf(ExternalProfile::class);
});

test('user', function (): void {
    $token = ExternalToken::factory()
        ->for(ExternalProfile::factory()->for(User::factory()))
        ->createOne();

    expect($token->user())->toBeInstanceOf(BelongsToThrough::class);
    expect($token->user()->first())->toBeInstanceOf(User::class);
});
