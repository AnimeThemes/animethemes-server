<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Theme;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\EntryVideo;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\post;

test('protected', function (): void {
    $entry = Entry::factory()
        ->for(Theme::factory()->for(Anime::factory()))
        ->create();

    $video = Video::factory()->createOne();

    $response = post(route('api.animethemeentryvideo.store', ['animethemeentry' => $entry, 'video' => $video]));

    $response->assertUnauthorized();
});

test('forbidden', function (): void {
    $entry = Entry::factory()
        ->for(Theme::factory()->for(Anime::factory()))
        ->create();

    $video = Video::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.animethemeentryvideo.store', ['animethemeentry' => $entry, 'video' => $video]));

    $response->assertForbidden();
});

test('create', function (): void {
    $entry = Entry::factory()
        ->for(Theme::factory()->for(Anime::factory()))
        ->create();

    $video = Video::factory()->createOne();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::CREATE->format(Entry::class),
            CrudPermission::CREATE->format(Video::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.animethemeentryvideo.store', ['animethemeentry' => $entry, 'video' => $video]));

    $response->assertCreated();
    $this->assertDatabaseCount(EntryVideo::class, 1);
});
