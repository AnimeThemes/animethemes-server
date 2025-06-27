<?php

declare(strict_types=1);

namespace Tests\Unit\Models\List\External;

use App\Models\Auth\User;
use App\Models\List\External\ExternalToken;
use App\Models\List\ExternalProfile;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;
use Znck\Eloquent\Relations\BelongsToThrough;

/**
 * Class ExternalTokenTest.
 */
class ExternalTokenTest extends TestCase
{
    /**
     * External Tokens shall be nameable.
     *
     * @return void
     */
    public function test_nameable(): void
    {
        $token = ExternalToken::factory()
            ->createOne();

        static::assertIsString($token->getName());
    }

    /**
     * External Tokens shall have subtitle.
     *
     * @return void
     */
    public function test_has_subtitle(): void
    {
        $token = ExternalToken::factory()
            ->for(ExternalProfile::factory())
            ->createOne();

        static::assertIsString($token->getSubtitle());
    }

    /**
     * External Tokens shall belong to an External Profile.
     *
     * @return void
     */
    public function test_profile(): void
    {
        $token = ExternalToken::factory()
            ->for(ExternalProfile::factory())
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $token->externalprofile());
        static::assertInstanceOf(ExternalProfile::class, $token->externalprofile()->first());
    }

    /**
     * External Tokens shall belong to a User through an External Profile.
     *
     * @return void
     */
    public function test_user(): void
    {
        $token = ExternalToken::factory()
            ->for(ExternalProfile::factory()->for(User::factory()))
            ->createOne();

        static::assertInstanceOf(BelongsToThrough::class, $token->user());
        static::assertInstanceOf(User::class, $token->user()->first());
    }
}
