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
use Illuminate\Support\Arr;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('casts site to enum', function () {
    $profile = ExternalProfile::factory()->createOne();

    $site = $profile->site;

    static::assertInstanceOf(ExternalProfileSite::class, $site);
});

test('casts visibility to enum', function () {
    $profile = ExternalProfile::factory()->createOne();

    $visibility = $profile->visibility;

    static::assertInstanceOf(ExternalProfileVisibility::class, $visibility);
});

test('nameable', function () {
    $profile = ExternalProfile::factory()->createOne();

    static::assertIsString($profile->getName());
});

test('has subtitle', function () {
    $profile = ExternalProfile::factory()
        ->for(User::factory())
        ->createOne();

    static::assertIsString($profile->getSubtitle());
});

test('searchable if public', function () {
    $profile = ExternalProfile::factory()
        ->createOne([
            ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value,
        ]);

    static::assertTrue($profile->shouldBeSearchable());
});

test('not searchable if not public', function () {
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

    static::assertFalse($profile->shouldBeSearchable());
});

test('claimed', function () {
    $claimedProfile = ExternalProfile::factory()
        ->for(User::factory())
        ->createOne();

    $unclaimedProfile = ExternalProfile::factory()
        ->createOne();

    static::assertTrue($claimedProfile->isClaimed());
    static::assertFalse($unclaimedProfile->isClaimed());
});

test('user', function () {
    $profile = ExternalProfile::factory()
        ->for(User::factory())
        ->createOne();

    static::assertInstanceOf(BelongsTo::class, $profile->user());
    static::assertInstanceOf(User::class, $profile->user()->first());
});

test('external token', function () {
    $profile = ExternalProfile::factory()
        ->has(ExternalToken::factory(), ExternalProfile::RELATION_EXTERNAL_TOKEN)
        ->createOne();

    static::assertInstanceOf(HasOne::class, $profile->externaltoken());
    static::assertInstanceOf(ExternalToken::class, $profile->externaltoken()->first());
});

test('external entries', function () {
    $entryCount = fake()->randomDigitNotNull();

    $profile = ExternalProfile::factory()->createOne();

    ExternalEntry::factory()
        ->for($profile)
        ->count($entryCount)
        ->create();

    static::assertInstanceOf(HasMany::class, $profile->externalentries());
    static::assertEquals($entryCount, $profile->externalentries()->count());
    static::assertInstanceOf(ExternalEntry::class, $profile->externalentries()->first());
});
