<?php

declare(strict_types=1);

use App\Enums\Auth\SpecialPermission;
use App\Models\Auth\User;
use Illuminate\Support\Facades\Auth;

use function Pest\Laravel\actingAs;

test('client with no forwarded ip not rate limited', function () {
    $response = graphql([
        'query' => '{ animePagination(first: 1) { data { id } } }',
    ]);

    $response->assertHeaderMissing('X-RateLimit-Limit');
    $response->assertHeaderMissing('X-RateLimit-Remaining');
});

test('user with bypass not rate limited', function () {
    $user = User::factory()->withPermissions(SpecialPermission::BYPASS_GRAPHQL_RATE_LIMITER->value)->createOne();

    actingAs($user);

    $response = graphql([
        'query' => '{ animePagination(first: 1) { data { id } } }',
    ]);

    $response->assertHeaderMissing('X-RateLimit-Limit');
    $response->assertHeaderMissing('X-RateLimit-Remaining');
});

test('forwarded ip rate limited per query', function () {
    $count = fake()->numberBetween(1, 10);

    $query = '{';

    foreach (range(1, $count) as $_) {
        $query .= 'animePagination(first: 1) { data { id } }';
    }

    $query .= '}';

    $response = $this->withHeader('x-forwarded-ip', fake()->ipv4())
        ->postJson(route('graphql'), [
            'query' => $query,
        ]);

    $response->assertHeader('X-RateLimit-Limit');
    $response->assertHeader('X-RateLimit-Remaining');

    expect((int) $response->headers->get('X-RateLimit-Remaining'))
        ->toBe(80 - $count);
});

test('user without bypass rate limited per query', function () {
    $user = User::factory()->createOne();

    actingAs($user);

    $count = fake()->numberBetween(1, 10);

    $query = '{';

    foreach (range(1, $count) as $_) {
        $query .= 'animePagination(first: 1) { data { id } }';
    }

    $query .= '}';

    $response = $this->withServerVariables([
        'REMOTE_ADDR' => fake()->ipv4(),
    ])->postJson(route('graphql'), [
        'query' => $query,
    ]);

    $response->assertHeader('X-RateLimit-Limit');
    $response->assertHeader('X-RateLimit-Remaining');

    expect((int) $response->headers->get('X-RateLimit-Remaining'))
        ->toBe(80 - $count);
});
