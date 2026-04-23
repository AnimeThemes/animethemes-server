<?php

declare(strict_types=1);

use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Constants\Config\ValidationConstants;
use App\Enums\Rules\ModerationService;
use App\Models\Auth\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Mockery\MockInterface;
use Propaganistas\LaravelDisposableEmail\Validation\Indisposable;

uses(WithFaker::class);

test('required', function (): void {
    $this->expectException(ValidationException::class);

    $user = User::factory()->createOne();

    $action = new UpdateUserProfileInformation();

    $action->update($user, []);
});

test('username alpha dash', function (): void {
    $this->expectException(ValidationException::class);

    $user = User::factory()->createOne();

    $action = new UpdateUserProfileInformation();

    $action->update($user, [
        User::ATTRIBUTE_NAME => fake()->password(20),
    ]);
});

test('username unique', function (): void {
    $this->expectException(ValidationException::class);

    $name = fake()->word();

    User::factory()->createOne([
        User::ATTRIBUTE_NAME => $name,
    ]);

    $user = User::factory()->createOne();

    $action = new UpdateUserProfileInformation();

    $action->update($user, [
        User::ATTRIBUTE_NAME => $name,
    ]);
});

test('update name', function (): void {
    $name = fake()->unique()->word();

    $user = User::factory()->createOne([
        User::ATTRIBUTE_NAME => fake()->unique()->word(),
    ]);

    $action = new UpdateUserProfileInformation();

    $action->update($user, [
        User::ATTRIBUTE_NAME => $name,
    ]);

    $this->assertDatabaseCount(User::class, 1);
    $this->assertDatabaseHas(User::class, [
        User::ATTRIBUTE_NAME => $name,
    ]);
    $this->assertDatabaseMissing(User::class, [
        User::ATTRIBUTE_EMAIL_VERIFIED_AT => null,
    ]);
});

test('update email', function (): void {
    Notification::fake();

    $email = fake()->unique()->companyEmail();

    $user = User::factory()->createOne([
        User::ATTRIBUTE_EMAIL => fake()->unique()->companyEmail(),
    ]);

    $action = new UpdateUserProfileInformation();

    $action->update($user, [
        User::ATTRIBUTE_EMAIL => $email,
    ]);

    $this->assertDatabaseCount(User::class, 1);
    $this->assertDatabaseHas(User::class, [
        User::ATTRIBUTE_EMAIL => $email,
        User::ATTRIBUTE_EMAIL_VERIFIED_AT => null,
    ]);

    Notification::assertSentTimes(VerifyEmail::class, 1);
});

test('created if not flagged by open ai', function (): void {
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

    $name = fake()->unique()->word();

    $user = User::factory()->createOne([
        User::ATTRIBUTE_NAME => fake()->unique()->word(),
    ]);

    $action = new UpdateUserProfileInformation();

    $action->update($user, [
        User::ATTRIBUTE_NAME => $name,
    ]);

    $this->assertDatabaseCount(User::class, 1);
    $this->assertDatabaseHas(User::class, [
        User::ATTRIBUTE_NAME => $name,
    ]);
    $this->assertDatabaseMissing(User::class, [
        User::ATTRIBUTE_EMAIL_VERIFIED_AT => null,
    ]);
});

test('created if open ai fails', function (): void {
    Config::set(ValidationConstants::MODERATION_SERVICE_QUALIFIED, ModerationService::OPENAI->value);

    Http::fake([
        'https://api.openai.com/v1/moderations' => Http::response(status: 404),
    ]);

    $name = fake()->unique()->word();

    $user = User::factory()->createOne([
        User::ATTRIBUTE_NAME => fake()->unique()->word(),
    ]);

    $action = new UpdateUserProfileInformation();

    $action->update($user, [
        User::ATTRIBUTE_NAME => $name,
    ]);

    $this->assertDatabaseCount(User::class, 1);
    $this->assertDatabaseHas(User::class, [
        User::ATTRIBUTE_NAME => $name,
    ]);
    $this->assertDatabaseMissing(User::class, [
        User::ATTRIBUTE_EMAIL_VERIFIED_AT => null,
    ]);
});

test('validation error when flagged by open ai', function (): void {
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

    $name = fake()->unique()->word();

    $user = User::factory()->createOne([
        User::ATTRIBUTE_NAME => fake()->unique()->word(),
    ]);

    $action = new UpdateUserProfileInformation();

    $action->update($user, [
        User::ATTRIBUTE_NAME => $name,
    ]);
});

test('disposable email', function (): void {
    $this->expectException(ValidationException::class);

    $this->mock(Indisposable::class, function (MockInterface $mock): void {
        $mock->shouldReceive('validate')->once()->andReturn(false);
    });

    $email = fake()->unique()->companyEmail();

    $user = User::factory()->createOne([
        User::ATTRIBUTE_EMAIL => fake()->unique()->companyEmail(),
    ]);

    $action = new UpdateUserProfileInformation();

    $action->update($user, [
        User::ATTRIBUTE_EMAIL => $email,
    ]);
});

test('indisposable email', function (): void {
    Notification::fake();

    $this->mock(Indisposable::class, function (MockInterface $mock): void {
        $mock->shouldReceive('validate')->once()->andReturn(true);
    });

    $email = fake()->unique()->companyEmail();

    $user = User::factory()->createOne([
        User::ATTRIBUTE_EMAIL => fake()->unique()->companyEmail(),
    ]);

    $action = new UpdateUserProfileInformation();

    $action->update($user, [
        User::ATTRIBUTE_EMAIL => $email,
    ]);

    $this->assertDatabaseCount(User::class, 1);
    $this->assertDatabaseHas(User::class, [
        User::ATTRIBUTE_EMAIL => $email,
        User::ATTRIBUTE_EMAIL_VERIFIED_AT => null,
    ]);

    Notification::assertSentTimes(VerifyEmail::class, 1);
});

test('email unique', function (): void {
    $this->expectException(ValidationException::class);

    $email = fake()->companyEmail();

    User::factory()->createOne([
        User::ATTRIBUTE_EMAIL => $email,
    ]);

    $user = User::factory()->createOne();

    $action = new UpdateUserProfileInformation();

    $action->update($user, [
        User::ATTRIBUTE_EMAIL => $email,
    ]);
});
