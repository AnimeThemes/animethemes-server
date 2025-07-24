<?php

declare(strict_types=1);

namespace Tests\Unit\Filament;

use App\Enums\Auth\SpecialPermission;
use App\Models\Auth\User;
use Tests\TestCase;

abstract class BaseResourceTestCase extends TestCase
{
    /**
     * Initial setup for the tests.
     */
    protected function setUp(): void
    {
        parent::setUp();

        static::markTestSkipped('TODO');
        /** @phpstan-ignore-next-line */
        $user = User::factory()
            ->withPermissions(SpecialPermission::VIEW_FILAMENT->value)
            ->createOne();

        $this->actingAs($user);
    }
}
