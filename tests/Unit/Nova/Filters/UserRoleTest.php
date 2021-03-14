<?php

namespace Tests\Unit\Nova\Filters;

use App\Enums\UserRole;
use App\Models\User;
use App\Nova\Filters\UserRoleFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

class UserRoleTest extends TestCase
{
    use NovaFilterTest, RefreshDatabase, WithFaker, WithoutEvents;

    /**
     * The User Status Filter shall be a select filter.
     *
     * @return void
     */
    public function testSelectFilter()
    {
        $this->novaFilter(UserRoleFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The User Status Filter shall have an option for each UserRole instance.
     *
     * @return void
     */
    public function testOptions()
    {
        $filter = $this->novaFilter(UserRoleFilter::class);

        foreach (UserRole::getInstances() as $season) {
            $filter->assertHasOption($season->description);
        }
    }

    /**
     * The User Status Filter shall filter Users By Status.
     *
     * @return void
     */
    public function testFilter()
    {
        $role = UserRole::getRandomInstance();

        User::factory()->count($this->faker->randomDigitNotNull)->create();

        $filter = $this->novaFilter(UserRoleFilter::class);

        $response = $filter->apply(User::class, $role->value);

        $filtered_users = User::where('role', $role->value)->get();
        foreach ($filtered_users as $filtered_user) {
            $response->assertContains($filtered_user);
        }
        $response->assertCount($filtered_users->count());
    }
}
