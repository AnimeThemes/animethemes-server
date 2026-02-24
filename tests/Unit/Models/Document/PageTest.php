<?php

declare(strict_types=1);

use App\Models\Auth\Role;
use App\Models\Document\Page;
use App\Pivots\Document\PageRole;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

test('nameable', function () {
    $page = Page::factory()->createOne();

    $this->assertIsString($page->getName());
});

test('has subtitle', function () {
    $page = Page::factory()->createOne();

    $this->assertIsString($page->getSubtitle());
});

test('previous', function () {
    $page = Page::factory()
        ->for(Page::factory(), Page::RELATION_PREVIOUS)
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $page->previous());
    $this->assertInstanceOf(Page::class, $page->previous()->first());
});

test('next', function () {
    $page = Page::factory()
        ->for(Page::factory(), Page::RELATION_NEXT)
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $page->next());
    $this->assertInstanceOf(Page::class, $page->next()->first());
});

test('roles', function () {
    $roleCount = fake()->randomDigitNotNull();

    $page = Page::factory()->createOne();

    /** @var Role $role */
    $role = Role::findOrCreate(fake()->word());

    PageRole::factory()
        ->for($page, PageRole::RELATION_PAGE)
        ->for($role, PageRole::RELATION_ROLE)
        ->count($roleCount)
        ->create();

    $this->assertInstanceOf(BelongsToMany::class, $page->roles());
    $this->assertEquals($roleCount, $page->roles()->count());
    $this->assertInstanceOf(Role::class, $page->roles()->first());
    $this->assertEquals(PageRole::class, $page->roles()->getPivotClass());
});
