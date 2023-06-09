<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Fortify;

use App\Actions\Fortify\CreateNewUser;
use App\Constants\Config\ValidationConstants;
use App\Enums\Rules\ModerationService;
use App\Models\Auth\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Mockery\MockInterface;
use Propaganistas\LaravelDisposableEmail\Validation\Indisposable;
use Tests\TestCase;

/**
 * Class CreateNewUserTest.
 */
class CreateNewUserTest extends TestCase
{
    use WithFaker;

    /**
     * The Create New User Action shall require the name, email, password & terms fields.
     *
     * @return void
     *
     * @throws ValidationException
     */
    public function testRequired(): void
    {
        static::expectException(ValidationException::class);

        $action = new CreateNewUser();

        $action->create([]);
    }

    /**
     * The Create New User Action shall require usernames to be restricted to alphanumeric characters and dashes.
     *
     * @return void
     *
     * @throws ValidationException
     */
    public function testUsernameAlphaDash(): void
    {
        static::expectException(ValidationException::class);

        $action = new CreateNewUser();

        $password = $this->faker->password(20);

        $action->create([
            User::ATTRIBUTE_NAME => $this->faker->password(20),
            User::ATTRIBUTE_EMAIL => $this->faker->companyEmail(),
            User::ATTRIBUTE_PASSWORD => $password,
            'password_confirmation' => $password,
            'terms' => 'terms',
        ]);
    }

    /**
     * The Create New User Action shall require usernames to be unique.
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

        $action = new CreateNewUser();

        $password = $this->faker->password(20);

        $action->create([
            User::ATTRIBUTE_NAME => $name,
            User::ATTRIBUTE_EMAIL => $this->faker->companyEmail(),
            User::ATTRIBUTE_PASSWORD => $password,
            'password_confirmation' => $password,
            'terms' => 'terms',
        ]);
    }

    /**
     * The Create New User Action shall create a new user.
     *
     * @return void
     *
     * @throws ValidationException
     */
    public function testCreated(): void
    {
        $action = new CreateNewUser();

        $password = $this->faker->password(20);

        $action->create([
            User::ATTRIBUTE_NAME => $this->faker()->word(),
            User::ATTRIBUTE_EMAIL => $this->faker->companyEmail(),
            User::ATTRIBUTE_PASSWORD => $password,
            'password_confirmation' => $password,
            'terms' => 'terms',
        ]);

        static::assertDatabaseCount(User::class, 1);
    }

    /**
     * The Create New User Action shall create a new user if the name is not flagged by OpenAI.
     *
     * @return void
     *
     * @throws ValidationException
     */
    public function testCreatedIfNotFlaggedByOpenAI(): void
    {
        Config::set(ValidationConstants::MODERATION_SERVICE_QUALIFIED, ModerationService::OPENAI->value);

        Http::fake([
            'https://api.openai.com/v1/moderations' => Http::response([
                'results' => [
                    0 => [
                        'flagged' => false,
                    ],
                ],
            ]),
        ]);

        $action = new CreateNewUser();

        $password = $this->faker->password(20);

        $action->create([
            User::ATTRIBUTE_NAME => $this->faker()->word(),
            User::ATTRIBUTE_EMAIL => $this->faker->companyEmail(),
            User::ATTRIBUTE_PASSWORD => $password,
            'password_confirmation' => $password,
            'terms' => 'terms',
        ]);

        static::assertDatabaseCount(User::class, 1);
    }

    /**
     * The Create New User Action shall create a new user if the moderation service returns some error.
     *
     * @return void
     *
     * @throws ValidationException
     */
    public function testCreatedIfOpenAIFails(): void
    {
        Config::set(ValidationConstants::MODERATION_SERVICE_QUALIFIED, ModerationService::OPENAI->value);

        Http::fake([
            'https://api.openai.com/v1/moderations' => Http::response(status: 404),
        ]);

        $action = new CreateNewUser();

        $password = $this->faker->password(20);

        $action->create([
            User::ATTRIBUTE_NAME => $this->faker()->word(),
            User::ATTRIBUTE_EMAIL => $this->faker->companyEmail(),
            User::ATTRIBUTE_PASSWORD => $password,
            'password_confirmation' => $password,
            'terms' => 'terms',
        ]);

        static::assertDatabaseCount(User::class, 1);
    }

    /**
     * The Create New User Action shall prohibit users from creating usernames flagged by OpenAI.
     *
     * @return void
     *
     * @throws ValidationException
     */
    public function testValidationErrorWhenFlaggedByOpenAI(): void
    {
        static::expectException(ValidationException::class);

        Config::set(ValidationConstants::MODERATION_SERVICE_QUALIFIED, ModerationService::OPENAI->value);

        Http::fake([
            'https://api.openai.com/v1/moderations' => Http::response([
                'results' => [
                    0 => [
                        'flagged' => true,
                    ],
                ],
            ]),
        ]);

        $action = new CreateNewUser();

        $password = $this->faker->password(20);

        $action->create([
            User::ATTRIBUTE_NAME => $this->faker()->word(),
            User::ATTRIBUTE_EMAIL => $this->faker->companyEmail(),
            User::ATTRIBUTE_PASSWORD => $password,
            'password_confirmation' => $password,
            'terms' => 'terms',
        ]);
    }

    /**
     * The Create New User Action shall prohibit registrations using disposable email services.
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

        $action = new CreateNewUser();

        $password = $this->faker->password(20);

        $action->create([
            User::ATTRIBUTE_NAME => $this->faker()->word(),
            User::ATTRIBUTE_EMAIL => $this->faker->companyEmail(),
            User::ATTRIBUTE_PASSWORD => $password,
            'password_confirmation' => $password,
            'terms' => 'terms',
        ]);
    }

    /**
     * The Create New User Action shall permit registrations using indisposable email services.
     *
     * @return void
     *
     * @throws ValidationException
     */
    public function testIndisposableEmail(): void
    {
        $this->mock(Indisposable::class, function (MockInterface $mock) {
            $mock->shouldReceive('validate')->once()->andReturn(true);
        });

        $action = new CreateNewUser();

        $password = $this->faker->password(20);

        $action->create([
            User::ATTRIBUTE_NAME => $this->faker()->word(),
            User::ATTRIBUTE_EMAIL => $this->faker->companyEmail(),
            User::ATTRIBUTE_PASSWORD => $password,
            'password_confirmation' => $password,
            'terms' => 'terms',
        ]);

        static::assertDatabaseCount(User::class, 1);
    }

    /**
     * The Create New User Action shall require emails to be unique.
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

        $action = new CreateNewUser();

        $password = $this->faker->password(20);

        $action->create([
            User::ATTRIBUTE_NAME => $this->faker->word(),
            User::ATTRIBUTE_EMAIL => $email,
            User::ATTRIBUTE_PASSWORD => $password,
            'password_confirmation' => $password,
            'terms' => 'terms',
        ]);
    }
}
