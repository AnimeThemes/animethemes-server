<?php

declare(strict_types=1);

namespace Tests\Unit\Nova\Actions\Wiki\Studio;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Studio;
use App\Nova\Actions\Wiki\Studio\CreateExternalResourceSiteForStudioAction;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Actions\MockAction;
use JoshGaber\NovaUnit\Actions\NovaActionTest;
use JoshGaber\NovaUnit\Fields\FieldNotFoundException;
use Laravel\Nova\Fields\ActionFields;
use Tests\TestCase;

/**
 * Class CreateExternalResourceSiteForStudioTest.
 */
class CreateExternalResourceSiteForStudioTest extends TestCase
{
    use NovaActionTest;
    use WithFaker;
    use WithoutEvents;

    /**
     * The Create Studio Resource Action shall have a link field.
     *
     * @return void
     */
    public function testFields()
    {
        $action = new MockAction(CreateExternalResourceSiteForStudioAction::make(ResourceSite::getRandomValue()));

        $action->assertHasField(__('nova.link'));
    }

    /**
     * The Create Studio Resource Action shall have a link field.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     */
    public function testLinkField()
    {
        $site = ResourceSite::getRandomValue();

        $action = new MockAction(CreateExternalResourceSiteForStudioAction::make($site));

        $field = $action->field(__('nova.link'));

        $field->assertHasRule('required');
        $field->assertHasRule('max:192');
        $field->assertHasRule('url');
    }

    /**
     * The Create Studio Resource Action shall create a Resource.
     *
     * @return void
     */
    public function testResourceCreated()
    {
        $site = ResourceSite::OFFICIAL_SITE;

        $fields = ['link' => $this->faker->url()];

        $models = Studio::factory()->count($this->faker->randomDigitNotNull())->create();

        $action = new MockAction(CreateExternalResourceSiteForStudioAction::make($site));

        $action->handle($fields, $models)
            ->assertMessage(__('nova.studio_create_resource_action_success'));
    }

    /**
     * The Create Studio Resource Action shall attach a Resource.
     *
     * @return void
     */
    public function testStudioHasResourceAttached()
    {
        $site = ResourceSite::OFFICIAL_SITE;

        $fields = ['link' => $this->faker->url()];

        $models = Studio::factory()->count($this->faker->randomDigitNotNull())->create();

        $action = CreateExternalResourceSiteForStudioAction::make($site);

        $action->handle(new ActionFields(collect($fields), collect()), $models);

        static::assertEquals($models->count(), Studio::query()->whereHas(Studio::RELATION_RESOURCES)->count());
    }
}
