<?php

namespace Tests\Feature\Auth;

use App\Enums\InvitationStatus;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * If the show registration form request does not have an invitation,
     * the application shall redirect the user to the welcome screen.
     *
     * @return void
     */
    public function testHasNoInvitationForRegistrationForm()
    {
        $response = $this->get(route('register'));

        $response->assertRedirect(route('welcome'));
    }

    /**
     * If the show registration form request has a token that does not belong to an invitation,
     * the application shall redirect the user to the welcome screen.
     *
     * @return void
     */
    public function testHasInvalidInvitationTokenForRegistrationForm()
    {
        $invitation = Invitation::factory()->make();

        $response = $this->get(route('register', ['token' => $invitation->token]));

        $response->assertRedirect(route('welcome'));
    }

    /**
     * If the show registration form request uses a closed invitation token,
     * the application shall redirect the user to the welcome screen.
     *
     * @return void
     */
    public function testHasClosedInvitationForRegistrationForm()
    {
        $invitation = Invitation::factory()->create([
            'status' => InvitationStatus::CLOSED,
        ]);

        $response = $this->get(route('register', ['token' => $invitation->token]));

        $response->assertRedirect(route('welcome'));
    }

    /**
     * If the show registration form request uses an open invitation token,
     * the application shall display the registration form.
     *
     * @return void
     */
    public function testHasInvitationForRegistrationForm()
    {
        $invitation = Invitation::factory()->create();

        $response = $this->get(route('register', ['token' => $invitation->token]));

        $response->assertViewIs('auth.register');
    }

    /**
     * If the registration request does not have an invitation,
     * the application shall redirect to the user to the welcome screen.
     *
     * @return void
     */
    public function testHasNoInvitationForRegistration()
    {
        $response = $this->post(route('register'));

        $response->assertRedirect(route('welcome'));
    }

    /**
     * If the registration request has a token that does not belong to an invitation,
     * the application shall redirect the user to the welcome screen.
     *
     * @return void
     */
    public function testHasInvalidInvitationTokenForRegistration()
    {
        $invitation = Invitation::factory()->make();

        $response = $this->post(route('register'), ['token' => $invitation->token]);

        $response->assertRedirect(route('welcome'));
    }

    /**
     * If the registration request uses a closed invitation token,
     * the application shall redirect the user to the welcome screen.
     *
     * @return void
     */
    public function testHasClosedInvitationForRegistration()
    {
        $invitation = Invitation::factory()->create([
            'status' => InvitationStatus::CLOSED,
        ]);

        $response = $this->post(route('register'), ['token' => $invitation->token]);

        $response->assertRedirect(route('welcome'));
    }

    /**
     * If the registration request uses an open invitation token,
     * the application shall process the validation.
     *
     * @return void
     */
    public function testHasInvitationForRegistration()
    {
        $invitation = Invitation::factory()->create();

        $response = $this->post(route('register'), ['token' => $invitation->token]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * The password field shall be required for the registration request.
     *
     * @return void
     */
    public function testPasswordRequiredValidationForRegistration()
    {
        $invitation = Invitation::factory()->create();

        $response = $this->post(route('register'), ['token' => $invitation->token]);

        $response->assertSessionHasErrors(['password' => 'The password field is required.']);
    }

    /**
     * The password field shall be a string for the registration request.
     *
     * @return void
     */
    public function testPasswordStringValidationForRegistration()
    {
        $invitation = Invitation::factory()->create();

        $response = $this->post(route('register'), ['token' => $invitation->token, 'password' => true]);

        $response->assertSessionHasErrors(['password' => 'The password must be a string.']);
    }

    /**
     * The password field shall be at least 8 characters in length for the registration request.
     *
     * @return void
     */
    public function testPasswordLengthValidationForRegistration()
    {
        $invitation = Invitation::factory()->create();

        $response = $this->post(route('register'), ['token' => $invitation->token, 'password' => $this->faker->password(6, 7)]);

        $response->assertSessionHasErrors(['password' => 'The password must be at least 8 characters.']);
    }

    /**
     * The password field shall be confirmed for the registration request.
     *
     * @return void
     */
    public function testPasswordConfirmationValidationForRegistration()
    {
        $invitation = Invitation::factory()->create();

        $response = $this->post(route('register'), ['token' => $invitation->token,
            'password' => $this->faker->password(6, 7),
            'password_confirmation' => $this->faker->password(8, 9),
        ]);

        $response->assertSessionHasErrors(['password' => 'The password confirmation does not match.']);
    }

    /**
     * The password field shall be scored at least a 3 by ZXCVBN for the registration request.
     *
     * @return void
     */
    public function testPasswordStrengthValidationForRegistration()
    {
        $invitation = Invitation::factory()->create();

        $weak_password = $this->faker->password(8, 8);
        $response = $this->post(route('register'), ['token' => $invitation->token,
            'password' => $weak_password,
            'password_confirmation' => $weak_password,
        ]);

        $response->assertSessionHasErrors(['password' => 'Your password is not secure enough.']);
    }

    /**
     * When the registration request is valid, the user shall be directed to the dashboard.
     *
     * @return void
     */
    public function testRedirectToDashboardForValidRegistration()
    {
        $invitation = Invitation::factory()->create();

        $strong_password = $this->faker->password(64, 128);
        $response = $this->post(route('register'), ['token' => $invitation->token,
            'password' => $strong_password,
            'password_confirmation' => $strong_password,
        ]);

        $response->assertRedirect(route('dashboard'));
    }

    /**
     * When the registration request is valid, the user shall be created.
     *
     * @return void
     */
    public function testUserCreatedForValidRegistration()
    {
        $invitation = Invitation::factory()->create();

        $strong_password = $this->faker->password(64, 128);
        $this->post(route('register'), ['token' => $invitation->token,
            'password' => $strong_password,
            'password_confirmation' => $strong_password,
        ]);

        $user = User::where('name', $invitation->name)->where('email', $invitation->email)->where('type', $invitation->type)->first();

        $this->assertNotNull($user);
    }

    /**
     * When the registration request is valid, the guest shall be authenticated as the newly created user.
     *
     * @return void
     */
    public function testUserAuthenticatedForValidRegistration()
    {
        $invitation = Invitation::factory()->create();

        $strong_password = $this->faker->password(64, 128);
        $this->post(route('register'), ['token' => $invitation->token,
            'password' => $strong_password,
            'password_confirmation' => $strong_password,
        ]);

        $user = User::where('name', $invitation->name)->where('email', $invitation->email)->where('type', $invitation->type)->first();

        $this->assertAuthenticatedAs($user);
    }
}
