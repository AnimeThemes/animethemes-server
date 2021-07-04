<?php

declare(strict_types=1);

namespace Tests\Unit\Nova\Actions\Wiki;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Nova\Actions\Wiki\CreateExternalResourceSiteForAnimeAction;
use App\Rules\Wiki\ResourceSiteDomainRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Actions\MockAction;
use JoshGaber\NovaUnit\Actions\NovaActionTest;
use JoshGaber\NovaUnit\Fields\FieldNotFoundException;
use Laravel\Nova\Fields\ActionFields;
use Tests\TestCase;

/**
 * Class CreateExternalResourceSiteForAnimeTest.
 */
class CreateExternalResourceSiteForAnimeTest extends TestCase
{
    use NovaActionTest;
    use RefreshDatabase;
    use WithFaker;
    use WithoutEvents;

    /**
     * The Create Anime Resource Action shall have a link field.
     *
     * @return void
     */
    public function testFields()
    {
        $action = new MockAction(CreateExternalResourceSiteForAnimeAction::make(ResourceSite::getRandomValue()));

        $action->assertHasField(__('nova.link'));
    }

    /**
     * The Create Anime Resource Action shall have a link field.
     *
     * @return void
     * @throws FieldNotFoundException
     */
    public function testLinkField()
    {
        $site = ResourceSite::getRandomValue();

        $action = new MockAction(CreateExternalResourceSiteForAnimeAction::make($site));

        $field = $action->field(__('nova.link'));

        $field->assertHasRule('required');
        $field->assertHasRule('max:192');
        $field->assertHasRule('url');
        $field->assertHasRule('unique:resource,link');
        $field->assertHasRule((new ResourceSiteDomainRule($site))->__toString());
    }

    /**
     * The Create Anime Resource Action shall create a Resource.
     *
     * @return void
     */
    public function testResourceCreated()
    {
        $site = ResourceSite::OFFICIAL_SITE;

        $fields = ['link' => $this->faker->url];

        $models = Anime::factory()->count($this->faker->randomDigitNotNull)->create();

        $action = new MockAction(CreateExternalResourceSiteForAnimeAction::make($site));

        $action->handle($fields, $models)
            ->assertMessage(__('nova.anime_create_resource_action_success'));
    }

    /**
     * The Create Anime Resource Action shall attach a Resource.
     *
     * @return void
     */
    public function testAnimeHasResourceAttached()
    {
        $site = ResourceSite::OFFICIAL_SITE;

        $fields = ['link' => $this->faker->url];

        $models = Anime::factory()->count($this->faker->randomDigitNotNull)->create();

        $action = CreateExternalResourceSiteForAnimeAction::make($site);

        $action->handle(new ActionFields(collect($fields), collect()), $models);

        static::assertEquals($models->count(), Anime::whereHas('resources')->count());
    }
}
