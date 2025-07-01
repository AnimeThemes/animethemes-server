<?php

declare(strict_types=1);

namespace Tests\Unit\Filament;

use App\Enums\Auth\SpecialPermission;
use App\Models\Auth\User;
use Tests\TestCase;

/**
 * Class BaseResourceTestCase.
 */
abstract class BaseResourceTestCase extends TestCase
{
    /**
     * Initial setup for the tests.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        static::markTestSkipped('TODO');

        $user = User::factory()
            ->withPermissions(SpecialPermission::VIEW_FILAMENT->value)
            ->createOne();

        $this->actingAs($user);
    }
}
