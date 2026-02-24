<?php

declare(strict_types=1);

use App\Enums\Auth\Role as RoleEnum;
use App\Enums\Pivots\Document\PageRoleType;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Models\Document\Page;
use App\Pivots\Document\PageRole;

use function Pest\Laravel\actingAs;

test('only public pages are readable by guests', function () {
    $publicPage = Page::factory()->createOne();

    /** @var Role $role */
    $role = Role::findOrCreate(fake()->word());

    PageRole::factory()
        ->for(Page::factory(), PageRole::RELATION_PAGE)
        ->for($role, PageRole::RELATION_ROLE)
        ->count(fake()->randomDigitNotNull())
        ->create([
            PageRole::ATTRIBUTE_TYPE => PageRoleType::VIEWER->value,
        ]);

    $pages = Page::query()->get();

    $this->assertCount(1, $pages);
    $this->assertEquals($publicPage->getKey(), $pages->first()->getKey());
})->repeat(5);

test('admin can see all pages', function () {
    $user = User::factory()->withAdmin()->createOne();

    Page::factory()->createOne();

    // Create pages with roles one by one to ensure they're separate
    $roleCount = fake()->randomDigitNotNull();
    foreach (range(1, $roleCount) as $i) {
        /** @var Role $role */
        $role = Role::findOrCreate(fake()->unique()->word());

        PageRole::factory()
            ->for(Page::factory(), PageRole::RELATION_PAGE)
            ->for($role, PageRole::RELATION_ROLE)
            ->createOne([
                PageRole::ATTRIBUTE_TYPE => PageRoleType::VIEWER->value,
            ]);
    }

    actingAs($user);

    $pages = Page::query()->get();

    $this->assertCount($roleCount + 1, $pages);
});

test('user with role can see pages with that role', function () {
    $user = User::factory()->createOne();

    /** @var Role $role */
    $role = Role::findOrCreate(RoleEnum::ENCODER->value);

    $user->assignRole($role);

    Page::factory()->createOne();

    // Create pages with user's role one by one
    $userPageCount = fake()->randomDigitNotNull();
    foreach (range(1, $userPageCount) as $i) {
        PageRole::factory()
            ->for(Page::factory(), PageRole::RELATION_PAGE)
            ->for($role, PageRole::RELATION_ROLE)
            ->createOne([
                PageRole::ATTRIBUTE_TYPE => PageRoleType::VIEWER->value,
            ]);
    }

    /** @var Role $role */
    $role = Role::findOrCreate(RoleEnum::CONTENT_MODERATOR->value);
    $otherCount = fake()->randomDigitNotNull();
    foreach (range(1, $otherCount) as $i) {
        PageRole::factory()
            ->for(Page::factory(), PageRole::RELATION_PAGE)
            ->for($role, PageRole::RELATION_ROLE)
            ->createOne([
                PageRole::ATTRIBUTE_TYPE => PageRoleType::VIEWER->value,
            ]);
    }

    actingAs($user);

    $pages = Page::query()->get();

    $this->assertCount($userPageCount + 1, $pages);
})->repeat(5);
