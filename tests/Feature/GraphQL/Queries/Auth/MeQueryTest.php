<?php

declare(strict_types=1);

use App\Models\Auth\User;

use function Pest\Laravel\actingAs;

test('unauthenticated returns null', function (): void {
    $response = $this->graphQL('
        query {
            me {
                id
            }
        }
    ', );

    $response->assertOk();
    $response->assertJson([
        'data' => [
            'me' => null,
        ],
    ]);
});

test('authenticated returns user', function (): void {
    $user = User::factory()->createOne();

    actingAs($user);

    $response = $this->graphQL('
        query {
            me {
                id
            }
        }
    ');

    $response->assertOk();
    $response->assertJson([
        'data' => [
            'me' => [
                'id' => $user->getKey(),
            ],
        ],
    ]);
});
