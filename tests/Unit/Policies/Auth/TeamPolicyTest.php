<?php

declare(strict_types=1);

namespace Tests\Unit\Policies\Auth;

use App\Actions\Jetstream\CreateTeam;
use App\Models\Auth\Team;
use App\Models\Auth\User;
use App\Policies\Auth\TeamPolicy;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * Class TeamPolicyTest.
 */
class TeamPolicyTest extends TestCase
{
    use RefreshDatabase;
    use WithoutEvents;
    use WithFaker;

    /**
     * Any user regardless of role can view any team.
     *
     * @return void
     */
    public function testViewAny()
    {
        $policy = new TeamPolicy();

        static::assertTrue($policy->viewAny());
    }

    /**
     * A team is viewable if the user belongs to the team.
     *
     * @return void
     */
    public function testView()
    {
        $team = Team::factory()->createOne();

        $member = User::factory()
            ->hasAttached($team)
            ->createOne();

        $user = User::factory()->createOne();

        $policy = new TeamPolicy();

        static::assertTrue($policy->view($member, $team));
        static::assertFalse($policy->view($user, $team));
    }

    /**
     * A team can be created by the creator user.
     *
     * @return void
     */
    public function testCreate()
    {
        $user = User::factory()->createOne();

        $creator = User::factory()->createOne();

        Config::set('jetstream.creator', $creator->getKey());

        $policy = new TeamPolicy();

        static::assertFalse($policy->create($user));
        static::assertTrue($policy->create($creator));
    }

    /**
     * A team can be updated by the owner.
     *
     * @return void
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function testUpdate()
    {
        $user = User::factory()->createOne();

        $owner = User::factory()->createOne();

        Config::set('jetstream.creator', $owner->getKey());

        $action = new CreateTeam();

        $team = $action->create($owner, ['name' => $this->faker->word()]);

        $policy = new TeamPolicy();

        static::assertFalse($policy->update($user, $team));
        static::assertTrue($policy->update($owner, $team));
    }

    /**
     * A team member can be added by the owner.
     *
     * @return void
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function testAddTeamMember()
    {
        $user = User::factory()->createOne();

        $owner = User::factory()->createOne();

        Config::set('jetstream.creator', $owner->getKey());

        $action = new CreateTeam();

        $team = $action->create($owner, ['name' => $this->faker->word()]);

        $policy = new TeamPolicy();

        static::assertFalse($policy->addTeamMember($user, $team));
        static::assertTrue($policy->addTeamMember($owner, $team));
    }

    /**
     * A team member can be updated by the owner.
     *
     * @return void
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function testUpdateTeamMember()
    {
        $user = User::factory()->createOne();

        $owner = User::factory()->createOne();

        Config::set('jetstream.creator', $owner->getKey());

        $action = new CreateTeam();

        $team = $action->create($owner, ['name' => $this->faker->word()]);

        $policy = new TeamPolicy();

        static::assertFalse($policy->updateTeamMember($user, $team));
        static::assertTrue($policy->updateTeamMember($owner, $team));
    }

    /**
     * A team member can be removed by the owner.
     *
     * @return void
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function testRemoveTeamMember()
    {
        $user = User::factory()->createOne();

        $owner = User::factory()->createOne();

        Config::set('jetstream.creator', $owner->getKey());

        $action = new CreateTeam();

        $team = $action->create($owner, ['name' => $this->faker->word()]);

        $policy = new TeamPolicy();

        static::assertFalse($policy->removeTeamMember($user, $team));
        static::assertTrue($policy->removeTeamMember($owner, $team));
    }

    /**
     * A team can be deleted by the owner.
     *
     * @return void
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function testDelete()
    {
        $user = User::factory()->createOne();

        $owner = User::factory()->createOne();

        Config::set('jetstream.creator', $owner->getKey());

        $action = new CreateTeam();

        $team = $action->create($owner, ['name' => $this->faker->word()]);

        $policy = new TeamPolicy();

        static::assertFalse($policy->delete($user, $team));
        static::assertTrue($policy->delete($owner, $team));
    }
}
