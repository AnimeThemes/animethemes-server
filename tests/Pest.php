<?php

declare(strict_types=1);

use App\Enums\Auth\SpecialPermission;
use App\Filament\Resources\BaseResource;
use App\Models\Auth\User;

use function Pest\Laravel\actingAs;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature', 'Unit');

pest()
    ->in('Unit/Filament/Resources')
    ->beforeEach(function () {
        $user = User::factory()
            ->withPermissions(SpecialPermission::VIEW_FILAMENT->value)
            ->createOne();

        actingAs($user);
    });

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

// Filament
/**
 * @param  class-string<BaseResource>  $resource
 */
function getIndexPage(string $resource): string
{
    $pages = $resource::getPages();

    return $pages['index']->getPage();
}

/**
 * @param  class-string<BaseResource>  $resource
 */
function getViewPage(string $resource): string
{
    $pages = $resource::getPages();

    return $pages['view']->getPage();
}
