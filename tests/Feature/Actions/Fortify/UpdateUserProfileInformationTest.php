<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Fortify;

use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Constants\Config\ValidationConstants;
use App\Enums\Rules\ModerationService;
use App\Models\Auth\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Mockery\MockInterface;
use Propaganistas\LaravelDisposableEmail\Validation\Indisposable;
use Tests\TestCase;

/**
 * Class UpdateUserProfileInformationTest.
 */
class UpdateUserProfileInformationTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * The Update User Profile Information Action shall require the name, email, password & terms fields.
     *
     * @return void
     *
     * @throws ValidationException
     */
    public function testRequired(): void
    {
        static::expectException(ValidationException::class);

        $user = User::factory()->createOne();

        $action = new UpdateUserProfileInformation();

        $action->update($user, []);
    }

    /**
     * The Update User Profile Information Action shall require usernames to be restricted to alphanumeric characters and dashes.
     *
     * @return void
     *
     * @throws ValidationException
     */
    public function testUsernameAlphaDash(): void
    {
        static::expectException(ValidationException::class);

        $user = User::factory()->createOne();

        $action = new UpdateUserProfileInformation();

        $action->update($user, [
            User::ATTRIBUTE_NAME => $this->faker->password(20),
        ]);
    }

    /**
     * The Update User Profile Information Action shall require usernames to be unique.
     *
     * @return void
     *
     * @throws ValidationException
     */
    public function testUsernameUnique(): void
    {
        static::expectException(ValidationException::class);

        $name = $this->faker()->word();

        User::factory()->createOne([
            User::ATTRIBUTE_NAME => $name,
        ]);

        $user = User::factory()->createOne();

        $action = new UpdateUserProfileInformation();

        $action->update($user, [
            User::ATTRIBUTE_NAME => $name,
        ]);
    }

    /**
     * The Update User Profile Information Action shall update the user name.
     *
     * @return void
     *
     * @throws ValidationException
     */
    public function testUpdateName(): void
    {
        $name = $this->faker->unique()->word();

        $user = User::factory()->createOne([
            User::ATTRIBUTE_NAME => $this->faker->unique()->word(),
        ]);

        $action = new UpdateUserProfileInformation();

        $action->update($user, [
            User::ATTRIBUTE_NAME => $name,
        ]);

        static::assertDatabaseCount(User::class, 1);
        static::assertDatabaseHas(User::class, [
            User::ATTRIBUTE_NAME => $name,
        ]);
        static::assertDatabaseMissing(User::class, [
            User::ATTRIBUTE_EMAIL_VERIFIED_AT => null,
        ]);
    }

    /**
     * The Update User Profile Information Action shall update the user email.
     *
     * @return void
     *
     * @throws ValidationException
     */
    public function testUpdateEmail(): void
    {
        Notification::fake();

        $email = $this->faker->unique()->companyEmail();

        $user = User::factory()->createOne([
            User::ATTRIBUTE_EMAIL => $this->faker->unique()->companyEmail(),
        ]);

        $action = new UpdateUserProfileInformation();

        $action->update($user, [
            User::ATTRIBUTE_EMAIL => $email,
        ]);

        static::assertDatabaseCount(User::class, 1);
        static::assertDatabaseHas(User::class, [
            User::ATTRIBUTE_EMAIL => $email,
            User::ATTRIBUTE_EMAIL_VERIFIED_AT => null,
        ]);

        Notification::assertSentTimes(VerifyEmail::class, 1);
    }

    /**
     * The Update User Profile Information Action shall update the user if the name is not flagged by OpenAI.
     *
     * @return void
     *
     * @throws ValidationException
     */
    public function testCreatedIfNotFlaggedByOpenAI(): void
    {
        Config::set(ValidationConstants::MODERATION_SERVICE_QUALIFIED, ModerationService::OPENAI);

        Http::fake([
            'https://api.openai.com/v1/moderations' => Http::response([
                'results' => [
                    0 => [
                        'flagged' => false,
                    ],
                ],
            ]),
        ]);

        $name = $this->faker->unique()->word();

        $user = User::factory()->createOne([
            User::ATTRIBUTE_NAME => $this->faker->unique()->word(),
        ]);

        $action = new UpdateUserProfileInformation();

        $action->update($user, [
            User::ATTRIBUTE_NAME => $name,
        ]);

        static::assertDatabaseCount(User::class, 1);
        static::assertDatabaseHas(User::class, [
            User::ATTRIBUTE_NAME => $name,
        ]);
        static::assertDatabaseMissing(User::class, [
            User::ATTRIBUTE_EMAIL_VERIFIED_AT => null,
        ]);
    }

    /**
     * The Update User Profile Information Action shall update the user if the moderation service returns some error.
     *
     * @return void
     *
     * @throws ValidationException
     */
    public function testCreatedIfOpenAIFails(): void
    {
        Config::set(ValidationConstants::MODERATION_SERVICE_QUALIFIED, ModerationService::OPENAI);

        Http::fake([
            'https://api.openai.com/v1/moderations' => Http::response(status: 404),
        ]);

        $name = $this->faker->unique()->word();

        $user = User::factory()->createOne([
            User::ATTRIBUTE_NAME => $this->faker->unique()->word(),
        ]);

        $action = new UpdateUserProfileInformation();

        $action->update($user, [
            User::ATTRIBUTE_NAME => $name,
        ]);

        static::assertDatabaseCount(User::class, 1);
        static::assertDatabaseHas(User::class, [
            User::ATTRIBUTE_NAME => $name,
        ]);
        static::assertDatabaseMissing(User::class, [
            User::ATTRIBUTE_EMAIL_VERIFIED_AT => null,
        ]);
    }

    /**
     * The Update User Profile Information Action shall prohibit users from updating usernames flagged by OpenAI.
     *
     * @return void
     *
     * @throws ValidationException
     */
    public function testValidationErrorWhenFlaggedByOpenAI(): void
    {
        static::expectException(ValidationException::class);

        Config::set(ValidationConstants::MODERATION_SERVICE_QUALIFIED, ModerationService::OPENAI);

        Http::fake([
            'https://api.openai.com/v1/moderations' => Http::response([
                'results' => [
                    0 => [
                        'flagged' => true,
                    ],
                ],
            ]),
        ]);

        $name = $this->faker->unique()->word();

        $user = User::factory()->createOne([
            User::ATTRIBUTE_NAME => $this->faker->unique()->word(),
        ]);

        $action = new UpdateUserProfileInformation();

        $action->update($user, [
            User::ATTRIBUTE_NAME => $name,
        ]);
    }

    /**
     * The Update User Profile Information Action shall prohibit updating user emails using disposable email services.
     *
     * @return void
     *
     * @throws ValidationException
     */
    public function testDisposableEmail(): void
    {
        static::expectException(ValidationException::class);

        $this->mock(Indisposable::class, function (MockInterface $mock) {
            $mock->shouldReceive('validate')->once()->andReturn(false);
        });

        $email = $this->faker->unique()->companyEmail();

        $user = User::factory()->createOne([
            User::ATTRIBUTE_EMAIL => $this->faker->unique()->companyEmail(),
        ]);

        $action = new UpdateUserProfileInformation();

        $action->update($user, [
            User::ATTRIBUTE_EMAIL => $email,
        ]);
    }

    /**
     * The Update User Profile Information Action shall permit updating user emails using indisposable email services.
     *
     * @return void
     *
     * @throws ValidationException
     */
    public function testIndisposableEmail(): void
    {
        Notification::fake();

        $this->mock(Indisposable::class, function (MockInterface $mock) {
            $mock->shouldReceive('validate')->once()->andReturn(true);
        });

        $email = $this->faker->unique()->companyEmail();

        $user = User::factory()->createOne([
            User::ATTRIBUTE_EMAIL => $this->faker->unique()->companyEmail(),
        ]);

        $action = new UpdateUserProfileInformation();

        $action->update($user, [
            User::ATTRIBUTE_EMAIL => $email,
        ]);

        static::assertDatabaseCount(User::class, 1);
        static::assertDatabaseHas(User::class, [
            User::ATTRIBUTE_EMAIL => $email,
            User::ATTRIBUTE_EMAIL_VERIFIED_AT => null,
        ]);

        Notification::assertSentTimes(VerifyEmail::class, 1);
    }

    /**
     * The Update User Profile Information Action shall require emails to be unique.
     *
     * @return void
     *
     * @throws ValidationException
     */
    public function testEmailUnique(): void
    {
        static::expectException(ValidationException::class);

        $email = $this->faker()->companyEmail();

        User::factory()->createOne([
            User::ATTRIBUTE_EMAIL => $email,
        ]);

        $user = User::factory()->createOne();

        $action = new UpdateUserProfileInformation();

        $action->update($user, [
            User::ATTRIBUTE_EMAIL => $email,
        ]);
    }
}
