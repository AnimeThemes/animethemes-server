<?php

declare(strict_types=1);

use App\Actions\Fortify\CreateNewUser;
use App\Constants\Config\ValidationConstants;
use App\Enums\Rules\ModerationService;
use App\Models\Auth\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Mockery\MockInterface;
use Propaganistas\LaravelDisposableEmail\Validation\Indisposable;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('required', function () {
    $this->expectException(ValidationException::class);

    $action = new CreateNewUser();

    $action->create([]);
});

test('username alpha dash', function () {
    $this->expectException(ValidationException::class);

    $action = new CreateNewUser();

    $password = Str::password(20);

    $action->create([
        User::ATTRIBUTE_NAME => fake()->password(20),
        User::ATTRIBUTE_EMAIL => fake()->companyEmail(),
        User::ATTRIBUTE_PASSWORD => $password,
        'password_confirmation' => $password,
        'terms' => 'terms',
    ]);
});

test('username unique', function () {
    $this->expectException(ValidationException::class);

    $name = fake()->word();

    User::factory()->createOne([
        User::ATTRIBUTE_NAME => $name,
    ]);

    $action = new CreateNewUser();

    $password = Str::password(20);

    $action->create([
        User::ATTRIBUTE_NAME => $name,
        User::ATTRIBUTE_EMAIL => fake()->companyEmail(),
        User::ATTRIBUTE_PASSWORD => $password,
        'password_confirmation' => $password,
        'terms' => 'terms',
    ]);
});

test('created', function () {
    $action = new CreateNewUser();

    $password = Str::password(20);

    $action->create([
        User::ATTRIBUTE_NAME => fake()->word(),
        User::ATTRIBUTE_EMAIL => fake()->companyEmail(),
        User::ATTRIBUTE_PASSWORD => $password,
        'password_confirmation' => $password,
        'terms' => 'terms',
    ]);

    $this->assertDatabaseCount(User::class, 1);
});

test('created if not flagged by open ai', function () {
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

    $password = Str::password(20);

    $action->create([
        User::ATTRIBUTE_NAME => fake()->word(),
        User::ATTRIBUTE_EMAIL => fake()->companyEmail(),
        User::ATTRIBUTE_PASSWORD => $password,
        'password_confirmation' => $password,
        'terms' => 'terms',
    ]);

    $this->assertDatabaseCount(User::class, 1);
});

test('created if open ai fails', function () {
    Config::set(ValidationConstants::MODERATION_SERVICE_QUALIFIED, ModerationService::OPENAI->value);

    Http::fake([
        'https://api.openai.com/v1/moderations' => Http::response(status: 404),
    ]);

    $action = new CreateNewUser();

    $password = Str::password(20);

    $action->create([
        User::ATTRIBUTE_NAME => fake()->word(),
        User::ATTRIBUTE_EMAIL => fake()->companyEmail(),
        User::ATTRIBUTE_PASSWORD => $password,
        'password_confirmation' => $password,
        'terms' => 'terms',
    ]);

    $this->assertDatabaseCount(User::class, 1);
});

test('validation error when flagged by open ai', function () {
    $this->expectException(ValidationException::class);

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

    $password = Str::password(20);

    $action->create([
        User::ATTRIBUTE_NAME => fake()->word(),
        User::ATTRIBUTE_EMAIL => fake()->companyEmail(),
        User::ATTRIBUTE_PASSWORD => $password,
        'password_confirmation' => $password,
        'terms' => 'terms',
    ]);
});

test('disposable email', function () {
    $this->expectException(ValidationException::class);

    $this->mock(Indisposable::class, function (MockInterface $mock) {
        $mock->shouldReceive('validate')->once()->andReturn(false);
    });

    $action = new CreateNewUser();

    $password = Str::password(20);

    $action->create([
        User::ATTRIBUTE_NAME => fake()->word(),
        User::ATTRIBUTE_EMAIL => fake()->companyEmail(),
        User::ATTRIBUTE_PASSWORD => $password,
        'password_confirmation' => $password,
        'terms' => 'terms',
    ]);
});

test('indisposable email', function () {
    $this->mock(Indisposable::class, function (MockInterface $mock) {
        $mock->shouldReceive('validate')->once()->andReturn(true);
    });

    $action = new CreateNewUser();

    $password = Str::password(20);

    $action->create([
        User::ATTRIBUTE_NAME => fake()->word(),
        User::ATTRIBUTE_EMAIL => fake()->companyEmail(),
        User::ATTRIBUTE_PASSWORD => $password,
        'password_confirmation' => $password,
        'terms' => 'terms',
    ]);

    $this->assertDatabaseCount(User::class, 1);
});

test('email unique', function () {
    $this->expectException(ValidationException::class);

    $email = fake()->companyEmail();

    User::factory()->createOne([
        User::ATTRIBUTE_EMAIL => $email,
    ]);

    $action = new CreateNewUser();

    $password = Str::password(20);

    $action->create([
        User::ATTRIBUTE_NAME => fake()->word(),
        User::ATTRIBUTE_EMAIL => $email,
        User::ATTRIBUTE_PASSWORD => $password,
        'password_confirmation' => $password,
        'terms' => 'terms',
    ]);
});
