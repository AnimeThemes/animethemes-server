<?php

declare(strict_types=1);

use App\Enums\Models\List\ExternalProfileSite;
use App\Enums\Models\List\ExternalProfileVisibility;
use App\Models\Auth\User;
use App\Models\List\External\ExternalEntry;
use App\Models\List\External\ExternalToken;
use App\Models\List\ExternalProfile;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;

pest()->use(WithFaker::class);

test('casts site to enum', function (): void {
    $profile = ExternalProfile::factory()->createOne();

    $site = $profile->site;

    expect($site)->toBeInstanceOf(ExternalProfileSite::class);
});

test('casts visibility to enum', function (): void {
    $profile = ExternalProfile::factory()->createOne();

    $visibility = $profile->visibility;

    expect($visibility)->toBeInstanceOf(ExternalProfileVisibility::class);
});

test('nameable', function (): void {
    $profile = ExternalProfile::factory()->createOne();

    expect($profile->getName())->toBeString();
});

test('has subtitle', function (): void {
    $profile = ExternalProfile::factory()
        ->for(User::factory())
        ->createOne();

    expect($profile->getSubtitle())->toBeString();
});

test('searchable if public', function (): void {
    $profile = ExternalProfile::factory()
        ->createOne([
            ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value,
        ]);

    expect($profile->shouldBeSearchable())->toBeTrue();
});

test('not searchable if not public', function (): void {
    $visibility = null;

    while ($visibility == null) {
        $candidate = Arr::random(ExternalProfileVisibility::cases());
        if ($candidate !== ExternalProfileVisibility::PUBLIC) {
            $visibility = $candidate;
        }
    }

    $profile = ExternalProfile::factory()
        ->createOne([
            ExternalProfile::ATTRIBUTE_VISIBILITY => $visibility->value,
        ]);

    expect($profile->shouldBeSearchable())->toBeFalse();
});

test('claimed', function (): void {
    $claimedProfile = ExternalProfile::factory()
        ->for(User::factory())
        ->createOne();

    $unclaimedProfile = ExternalProfile::factory()
        ->createOne();

    expect($claimedProfile->isClaimed())->toBeTrue();
    expect($unclaimedProfile->isClaimed())->toBeFalse();
});

test('user', function (): void {
    $profile = ExternalProfile::factory()
        ->for(User::factory())
        ->createOne();

    expect($profile->user())->toBeInstanceOf(BelongsTo::class);
    expect($profile->user()->first())->toBeInstanceOf(User::class);
});

test('external token', function (): void {
    $profile = ExternalProfile::factory()
        ->has(ExternalToken::factory(), ExternalProfile::RELATION_EXTERNAL_TOKEN)
        ->createOne();

    expect($profile->externaltoken())->toBeInstanceOf(HasOne::class);
    expect($profile->externaltoken()->first())->toBeInstanceOf(ExternalToken::class);
});

test('external entries', function (): void {
    $entryCount = fake()->randomDigitNotNull();

    $profile = ExternalProfile::factory()->createOne();

    ExternalEntry::factory()
        ->for($profile)
        ->count($entryCount)
        ->create();

    expect($profile->externalentries())->toBeInstanceOf(HasMany::class);
    expect($profile->externalentries()->count())->toEqual($entryCount);
    expect($profile->externalentries()->first())->toBeInstanceOf(ExternalEntry::class);
});
