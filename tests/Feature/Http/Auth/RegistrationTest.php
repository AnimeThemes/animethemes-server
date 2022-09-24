<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Auth;

use App\Enums\Models\Auth\InvitationStatus;
use App\Models\Auth\Invitation;
use App\Models\Auth\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

/**
 * Class RegistrationTest.
 */
class RegistrationTest extends TestCase
{
    use WithFaker;

    /**
     * If the show registration form request uses a closed invitation,
     * the user shall receive a forbidden exception.
     *
     * @return void
     */
    public function testHasClosedInvitationForRegistrationForm(): void
    {
        $invitation = Invitation::factory()->create([
            Invitation::ATTRIBUTE_STATUS => InvitationStatus::CLOSED,
        ]);

        $url = URL::temporarySignedRoute(
            'register',
            now()->addMinutes(30),
            ['invitation' => $invitation]
        );

        $response = $this->get($url);

        $response->assertForbidden();
    }

    /**
     * If the show registration form request uses a soft deleted invitation,
     * the user shall receive a forbidden exception.
     *
     * @return void
     */
    public function testHasSoftDeletedInvitationForRegistrationForm(): void
    {
        $invitation = Invitation::factory()->create();

        $invitation->delete();

        $url = URL::temporarySignedRoute(
            'register',
            now()->addMinutes(30),
            ['invitation' => $invitation]
        );

        $response = $this->get($url);

        $response->assertForbidden();
    }

    /**
     * If the show registration form request uses an open invitation,
     * the application shall display the registration form.
     *
     * @return void
     */
    public function testHasInvitationForRegistrationForm(): void
    {
        $invitation = Invitation::factory()->create();

        $url = URL::temporarySignedRoute(
            'register',
            now()->addMinutes(30),
            ['invitation' => $invitation]
        );

        $response = $this->get($url);

        $response->assertViewIs('auth.register');
    }

    /**
     * If the registration request uses a closed invitation,
     * the user shall receive a forbidden exception.
     *
     * @return void
     */
    public function testHasClosedInvitationForRegistration(): void
    {
        $invitation = Invitation::factory()->create([
            Invitation::ATTRIBUTE_STATUS => InvitationStatus::CLOSED,
        ]);

        $url = URL::temporarySignedRoute(
            'register',
            now()->addMinutes(30),
            ['invitation' => $invitation]
        );

        $response = $this->post($url);

        $response->assertForbidden();
    }

    /**
     * If the registration request uses an open invitation,
     * the application shall process the validation.
     *
     * @return void
     */
    public function testHasInvitationForRegistration(): void
    {
        $invitation = Invitation::factory()->create();

        $url = URL::temporarySignedRoute(
            'register',
            now()->addMinutes(30),
            ['invitation' => $invitation]
        );

        $response = $this->post($url);

        $response->assertInvalid(['password']);
    }

    /**
     * The password field shall be required for the registration request.
     *
     * @return void
     */
    public function testPasswordRequiredValidationForRegistration(): void
    {
        $invitation = Invitation::factory()->create();

        $url = URL::temporarySignedRoute(
            'register',
            now()->addMinutes(30),
            ['invitation' => $invitation]
        );

        $response = $this->post($url);

        $response->assertInvalid(['password' => 'The password field is required.']);
    }

    /**
     * The password field shall be at least 8 characters in length for the registration request.
     *
     * @return void
     */
    public function testPasswordLengthValidationForRegistration(): void
    {
        $invitation = Invitation::factory()->create();

        $url = URL::temporarySignedRoute(
            'register',
            now()->addMinutes(30),
            ['invitation' => $invitation, 'password' => $this->faker->password(6, 7)]
        );

        $response = $this->post($url);

        $response->assertInvalid(['password' => 'The password must be at least 8 characters.']);
    }

    /**
     * The password field shall be confirmed for the registration request.
     *
     * @return void
     */
    public function testPasswordConfirmationValidationForRegistration(): void
    {
        $invitation = Invitation::factory()->create();

        $url = URL::temporarySignedRoute(
            'register',
            now()->addMinutes(30),
            [
                'invitation' => $invitation,
                'password' => $this->faker->password(6, 7),
                'password_confirmation' => $this->faker->password(8, 9),
                'terms' => 'terms',
            ]
        );

        $response = $this->post($url);

        $response->assertInvalid(['password' => 'The password confirmation does not match.']);
    }

    /**
     * The password field shall be scored at least a 3 by ZXCVBN for the registration request.
     *
     * @return void
     */
    public function testPasswordStrengthValidationForRegistration(): void
    {
        $invitation = Invitation::factory()->create();

        $weakPassword = $this->faker->password(8, 8);

        $url = URL::temporarySignedRoute(
            'register',
            now()->addMinutes(30),
            [
                'invitation' => $invitation,
                'password' => $weakPassword,
                'password_confirmation' => $weakPassword,
                'terms' => 'terms',
            ]
        );

        $response = $this->post($url);

        $response->assertInvalid(['password' => 'The password is not strong enough.']);
    }

    /**
     * The terms field shall be required.
     *
     * @return void
     */
    public function testTermAcceptanceRequiredForRegistration(): void
    {
        $invitation = Invitation::factory()->create();

        $strongPassword = $this->faker->password(64, 128);

        $url = URL::temporarySignedRoute(
            'register',
            now()->addMinutes(30),
            [
                'invitation' => $invitation,
                'password' => $strongPassword,
                'password_confirmation' => $strongPassword,
            ]
        );

        $response = $this->post($url);

        $response->assertInvalid(['terms' => 'The terms field is required.']);
    }

    /**
     * When the registration request is valid, the user shall be directed to the dashboard.
     *
     * @return void
     */
    public function testRedirectToDashboardForValidRegistration(): void
    {
        $invitation = Invitation::factory()->create();

        $strongPassword = $this->faker->password(64, 128);

        $url = URL::temporarySignedRoute(
            'register',
            now()->addMinutes(30),
            [
                'invitation' => $invitation,
                'password' => $strongPassword,
                'password_confirmation' => $strongPassword,
                'terms' => 'terms',
            ]
        );

        $response = $this->post($url);

        $response->assertRedirect(route('dashboard'));
    }

    /**
     * When the registration request is valid, the user shall be created.
     *
     * @return void
     */
    public function testUserCreatedForValidRegistration(): void
    {
        $invitation = Invitation::factory()->createOne();

        $strongPassword = $this->faker->password(64, 128);

        $url = URL::temporarySignedRoute(
            'register',
            now()->addMinutes(30),
            [
                'invitation' => $invitation,
                'password' => $strongPassword,
                'password_confirmation' => $strongPassword,
                'terms' => 'terms',
            ]
        );

        $this->post($url);

        $user = User::query()
            ->where(Invitation::ATTRIBUTE_NAME, $invitation->name)
            ->where(Invitation::ATTRIBUTE_EMAIL, $invitation->email)
            ->first();

        static::assertNotNull($user);
    }

    /**
     * When the registration request is valid, the guest shall be authenticated as the newly created user.
     *
     * @return void
     */
    public function testUserAuthenticatedForValidRegistration(): void
    {
        $invitation = Invitation::factory()->createOne();

        $strongPassword = $this->faker->password(64, 128);

        $url = URL::temporarySignedRoute(
            'register',
            now()->addMinutes(30),
            [
                'invitation' => $invitation,
                'password' => $strongPassword,
                'password_confirmation' => $strongPassword,
                'terms' => 'terms',
            ]
        );

        $this->post($url);

        $this->assertAuthenticated();
    }
}
