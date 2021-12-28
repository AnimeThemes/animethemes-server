<?php

declare(strict_types=1);

namespace App\Nova\Actions\Wiki\Studio;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\ExternalResource;
use App\Rules\Wiki\ResourceSiteDomainRule;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Text;

/**
 * Class CreateExternalResourceSiteForStudioAction.
 */
class CreateExternalResourceSiteForStudioAction extends Action
{
    use InteractsWithQueue;
    use Queueable;

    /**
     * @param  int  $site
     */
    public function __construct(protected int $site) {}

    /**
     * Get the displayable name of the action.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function name(): string
    {
        return __('nova.studio_create_resource_action', ['site' => ResourceSite::getDescription($this->site)]);
    }

    /**
     * Perform the action on the given models.
     *
     * @param  ActionFields  $fields
     * @param  Collection  $models
     * @return array
     */
    public function handle(ActionFields $fields, Collection $models): array
    {
        // Create Resource Model with link and provided site
        $resource = ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_EXTERNAL_ID => null,
            ExternalResource::ATTRIBUTE_LINK => $fields->get('link'),
            ExternalResource::ATTRIBUTE_SITE => $this->site,
        ]);

        // Attach Resource to Studio and provide success message
        $resource->studios()->attach($models);

        return Action::message(__('nova.studio_create_resource_action_success'));
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields(): array
    {
        return array_merge(
            parent::fields(),
            [
                Text::make(__('nova.link'), 'link')
                    ->rules([
                        'required',
                        'max:192',
                        'url',
                        Rule::unique(ExternalResource::TABLE),
                        new ResourceSiteDomainRule($this->site),
                    ])
                    ->help(__('nova.resource_link_help')),
            ]
        );
    }
}
