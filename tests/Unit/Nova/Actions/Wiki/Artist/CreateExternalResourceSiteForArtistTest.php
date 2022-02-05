<?php

declare(strict_types=1);

namespace Tests\Unit\Nova\Actions\Wiki\Artist;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Artist;
use App\Nova\Actions\Wiki\Artist\CreateExternalResourceSiteForArtistAction;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Actions\MockAction;
use JoshGaber\NovaUnit\Actions\NovaActionTest;
use JoshGaber\NovaUnit\Fields\FieldNotFoundException;
use Laravel\Nova\Fields\ActionFields;
use Tests\TestCase;

/**
 * Class CreateExternalResourceSiteForArtistTest.
 */
class CreateExternalResourceSiteForArtistTest extends TestCase
{
    use NovaActionTest;
    use WithFaker;
    use WithoutEvents;

    /**
     * The Create Artist Resource Action shall have a link field.
     *
     * @return void
     */
    public function testFields(): void
    {
        $action = new MockAction(CreateExternalResourceSiteForArtistAction::make(ResourceSite::getRandomValue()));

        $action->assertHasField(__('nova.link'));
    }

    /**
     * The Create Artist Resource Action shall have a link field.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     */
    public function testLinkField(): void
    {
        $site = ResourceSite::getRandomValue();

        $action = new MockAction(CreateExternalResourceSiteForArtistAction::make($site));

        $field = $action->field(__('nova.link'));

        $field->assertHasRule('required');
        $field->assertHasRule('max:192');
        $field->assertHasRule('url');
    }

    /**
     * The Create Artist Resource Action shall create a Resource.
     *
     * @return void
     */
    public function testResourceCreated(): void
    {
        $site = ResourceSite::OFFICIAL_SITE;

        $fields = ['link' => $this->faker->url()];

        $models = Artist::factory()->count($this->faker->randomDigitNotNull())->create();

        $action = new MockAction(CreateExternalResourceSiteForArtistAction::make($site));

        $action->handle($fields, $models)
            ->assertMessage(__('nova.Artist_create_resource_action_success'));
    }

    /**
     * The Create Artist Resource Action shall attach a Resource.
     *
     * @return void
     */
    public function testArtistHasResourceAttached(): void
    {
        $site = ResourceSite::OFFICIAL_SITE;

        $fields = ['link' => $this->faker->url()];

        $models = Artist::factory()->count($this->faker->randomDigitNotNull())->create();

        $action = CreateExternalResourceSiteForArtistAction::make($site);

        $action->handle(new ActionFields(collect($fields), collect()), $models);

        static::assertEquals($models->count(), Artist::query()->whereHas(Artist::RELATION_RESOURCES)->count());
    }
}
