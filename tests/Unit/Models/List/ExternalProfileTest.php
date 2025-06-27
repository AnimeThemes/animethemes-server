<?php

declare(strict_types=1);

namespace Tests\Unit\Models\List;

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
use Tests\TestCase;

/**
 * Class ExternalProfileTest.
 */
class ExternalProfileTest extends TestCase
{
    use WithFaker;

    /**
     * The site attribute of a profile shall be cast to a ExternalProfileSite enum instance.
     *
     * @return void
     */
    public function test_casts_site_to_enum(): void
    {
        $profile = ExternalProfile::factory()->createOne();

        $site = $profile->site;

        static::assertInstanceOf(ExternalProfileSite::class, $site);
    }

    /**
     * The visibility attribute of a profile shall be cast to a ExternalProfileVisibility enum instance.
     *
     * @return void
     */
    public function test_casts_visibility_to_enum(): void
    {
        $profile = ExternalProfile::factory()->createOne();

        $visibility = $profile->visibility;

        static::assertInstanceOf(ExternalProfileVisibility::class, $visibility);
    }

    /**
     * Profile shall be nameable.
     *
     * @return void
     */
    public function test_nameable(): void
    {
        $profile = ExternalProfile::factory()->createOne();

        static::assertIsString($profile->getName());
    }

    /**
     * Profile shall have subtitle.
     *
     * @return void
     */
    public function test_has_subtitle(): void
    {
        $profile = ExternalProfile::factory()
            ->for(User::factory())
            ->createOne();

        static::assertIsString($profile->getSubtitle());
    }

    /**
     * Public profiles shall be searchable.
     *
     * @return void
     */
    public function test_searchable_if_public(): void
    {
        $profile = ExternalProfile::factory()
            ->createOne([
                ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value,
            ]);

        static::assertTrue($profile->shouldBeSearchable());
    }

    /**
     * Profiles shall not be searchable if not public.
     *
     * @return void
     */
    public function test_not_searchable_if_not_public(): void
    {
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
    }

    /**
     * Profile with a user shall be claimed.
     *
     * @return void
     */
    public function test_claimed(): void
    {
        $claimedProfile = ExternalProfile::factory()
            ->for(User::factory())
            ->createOne();

        $unclaimedProfile = ExternalProfile::factory()
            ->createOne();

        static::assertTrue($claimedProfile->isClaimed());
        static::assertFalse($unclaimedProfile->isClaimed());
    }

    /**
     * Profiles shall belong to a user.
     *
     * @return void
     */
    public function test_user(): void
    {
        $profile = ExternalProfile::factory()
            ->for(User::factory())
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $profile->user());
        static::assertInstanceOf(User::class, $profile->user()->first());
    }

    /**
     * Profiles shall have a one-to-one relationship with the type external token.
     *
     * @return void
     */
    public function test_external_token(): void
    {
        $profile = ExternalProfile::factory()
            ->has(ExternalToken::factory(), ExternalProfile::RELATION_EXTERNAL_TOKEN)
            ->createOne();

        static::assertInstanceOf(HasOne::class, $profile->externaltoken());
        static::assertInstanceOf(ExternalToken::class, $profile->externaltoken()->first());
    }

    /**
     * Profiles shall have a one-to-many relationship with the type ExternalEntry.
     *
     * @return void
     */
    public function test_external_entries(): void
    {
        $entryCount = $this->faker->randomDigitNotNull();

        $profile = ExternalProfile::factory()->createOne();

        ExternalEntry::factory()
            ->for($profile)
            ->count($entryCount)
            ->create();

        static::assertInstanceOf(HasMany::class, $profile->externalentries());
        static::assertEquals($entryCount, $profile->externalentries()->count());
        static::assertInstanceOf(ExternalEntry::class, $profile->externalentries()->first());
    }
}
