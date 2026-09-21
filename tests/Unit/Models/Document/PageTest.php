<?php

declare(strict_types=1);

use App\Models\Auth\Role;
use App\Models\Document\Page;
use App\Pivots\Document\PageRole;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

test('nameable', function (): void {
    $page = Page::factory()->createOne();

    expect($page->getName())->toBeString();
});

test('has subtitle', function (): void {
    $page = Page::factory()->createOne();

    expect($page->getSubtitle())->toBeString();
});

test('previous', function (): void {
    $page = Page::factory()
        ->for(Page::factory(), Page::RELATION_PREVIOUS)
        ->createOne();

    expect($page->previous())->toBeInstanceOf(BelongsTo::class);
    expect($page->previous()->first())->toBeInstanceOf(Page::class);
});

test('next', function (): void {
    $page = Page::factory()
        ->for(Page::factory(), Page::RELATION_NEXT)
        ->createOne();

    expect($page->next())->toBeInstanceOf(BelongsTo::class);
    expect($page->next()->first())->toBeInstanceOf(Page::class);
});

test('roles', function (): void {
    $roleCount = fake()->randomDigitNotNull();

    $page = Page::factory()->createOne();

    /** @var Role $role */
    $role = Role::findOrCreate(fake()->word());

    PageRole::factory()
        ->for($page, PageRole::RELATION_PAGE)
        ->for($role, PageRole::RELATION_ROLE)
        ->count($roleCount)
        ->create();

    expect($page->roles())->toBeInstanceOf(BelongsToMany::class);
    expect($page->roles()->count())->toEqual($roleCount);
    expect($page->roles()->first())->toBeInstanceOf(Role::class);
    expect($page->roles()->getPivotClass())->toEqual(PageRole::class);
});
