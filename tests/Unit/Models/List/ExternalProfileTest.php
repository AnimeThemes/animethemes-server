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

uses(WithFaker::class);

test('casts site to enum', function (): void {
    $profile = ExternalProfile::factory()->createOne();

    $site = $profile->site;

    $this->assertInstanceOf(ExternalProfileSite::class, $site);
});

test('casts visibility to enum', function (): void {
    $profile = ExternalProfile::factory()->createOne();

    $visibility = $profile->visibility;

    $this->assertInstanceOf(ExternalProfileVisibility::class, $visibility);
});

test('nameable', function (): void {
    $profile = ExternalProfile::factory()->createOne();

    $this->assertIsString($profile->getName());
});

test('has subtitle', function (): void {
    $profile = ExternalProfile::factory()
        ->for(User::factory())
        ->createOne();

    $this->assertIsString($profile->getSubtitle());
});

test('searchable if public', function (): void {
    $profile = ExternalProfile::factory()
        ->createOne([
            ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value,
        ]);

    $this->assertTrue($profile->shouldBeSearchable());
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

    $this->assertFalse($profile->shouldBeSearchable());
});

test('claimed', function (): void {
    $claimedProfile = ExternalProfile::factory()
        ->for(User::factory())
        ->createOne();

    $unclaimedProfile = ExternalProfile::factory()
        ->createOne();

    $this->assertTrue($claimedProfile->isClaimed());
    $this->assertFalse($unclaimedProfile->isClaimed());
});

test('user', function (): void {
    $profile = ExternalProfile::factory()
        ->for(User::factory())
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $profile->user());
    $this->assertInstanceOf(User::class, $profile->user()->first());
});

test('external token', function (): void {
    $profile = ExternalProfile::factory()
        ->has(ExternalToken::factory(), ExternalProfile::RELATION_EXTERNAL_TOKEN)
        ->createOne();

    $this->assertInstanceOf(HasOne::class, $profile->externaltoken());
    $this->assertInstanceOf(ExternalToken::class, $profile->externaltoken()->first());
});

test('external entries', function (): void {
    $entryCount = fake()->randomDigitNotNull();

    $profile = ExternalProfile::factory()->createOne();

    ExternalEntry::factory()
        ->for($profile)
        ->count($entryCount)
        ->create();

    $this->assertInstanceOf(HasMany::class, $profile->externalentries());
    $this->assertEquals($entryCount, $profile->externalentries()->count());
    $this->assertInstanceOf(ExternalEntry::class, $profile->externalentries()->first());
});
