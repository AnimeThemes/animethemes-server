<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Morph\Resourceable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

test('fails without id or link', function () {
    $resourceSite = Arr::random(ResourceSite::cases());

    $response = $this->graphQL(
        '
        query($site: ResourceSite!) {
            findAnimeByExternalSite(site: $site) {
                id
            }
        }
        ',
        [
            'site' => $resourceSite->name,
        ],
    );

    $response->assertOk();
    $response->assertGraphQLValidationKeys(['id', 'link']);
});

test('fails with for than 100 ids', function () {
    $resourceSite = Arr::random(ResourceSite::cases());

    $response = $this->graphQL(
        '
        query($site: ResourceSite!, $ids: [Int!]) {
            findAnimeByExternalSite(site: $site, id: $ids) {
                id
            }
        }
        ',
        [
            'site' => $resourceSite->name,
            'ids' => Collection::times(101, fn (int $int) => $int + 1)->toArray(),
        ],
    );

    $response->assertOk();
    $response->assertGraphQLValidationKeys(['id']);
});

test('passes with id', function () {
    Resourceable::factory()
        ->for(
            ExternalResource::factory()->create([
                ExternalResource::ATTRIBUTE_EXTERNAL_ID => $externalId = fake()->randomDigitNotNull(),
            ]),
            Resourceable::RELATION_RESOURCE
        )
        ->forAnime()
        ->create();

    $resourceSite = ExternalResource::query()
        ->first()
        ->getAttribute(ExternalResource::ATTRIBUTE_SITE);

    $response = $this->graphQL(
        '
        query($site: ResourceSite!, $externalId: [Int!]) {
            findAnimeByExternalSite(site: $site, id: $externalId) {
                id
            }
        }
        ',
        [
            'site' => $resourceSite->name,
            'externalId' => $externalId,
        ],
    );

    $response->assertOk();
    $response->assertExactJsonStructure([
        'data' => [
            'findAnimeByExternalSite' => [['id']],
        ],
    ]);
});

test('passes with link', function () {
    Resourceable::factory()
        ->for(
            ExternalResource::factory()->create([
                ExternalResource::ATTRIBUTE_LINK => $link = fake()->url(),
            ]),
            Resourceable::RELATION_RESOURCE
        )
        ->forAnime()
        ->create();

    $resourceSite = ExternalResource::query()
        ->first()
        ->getAttribute(ExternalResource::ATTRIBUTE_SITE);

    $response = $this->graphQL(
        '
        query($site: ResourceSite!, $link: String) {
            findAnimeByExternalSite(site: $site, link: $link) {
                id
            }
        }
        ',
        [
            'site' => $resourceSite->name,
            'link' => (string) $link,
        ],
    );

    $response->assertOk();
    $response->assertJsonStructure([
        'data' => [
            'findAnimeByExternalSite' =>[['id']],
        ],
    ]);
});
