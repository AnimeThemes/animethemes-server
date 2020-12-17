<?php

namespace App\Nova\Actions;

use App\Enums\ResourceSite;
use App\Models\ExternalResource;
use App\Rules\ResourceSiteDomainRule;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Text;

class CreateExternalResourceSiteForAnimeAction extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * The resource site key.
     *
     * @var int
     */
    private $site;

    /**
     * @param int $site
     */
    public function __construct($site)
    {
        $this->site = $site;
    }

    /**
     * Get the displayable name of the action.
     *
     * @return array|string|null
     */
    public function name()
    {
        return __('nova.anime_create_resource_action', ['site' => ResourceSite::getDescription($this->site)]);
    }

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        // Create Resource Model with link and provided site
        $resource = ExternalResource::create([
            'site' => $this->site,
            'link' => $fields->get('link'),
        ]);

        // Check if resource creation is successful
        if (! $resource->exists()) {
            return Action::danger(__('nova.error_resource_creation'));
        }

        // Attach Resource to Anime and provide success message
        $resource->anime()->attach($models);

        return Action::message(__('nova.anime_create_resource_action_success'));
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return [
            Text::make(__('nova.link'), 'link')
                ->rules('required', 'max:192', 'url', 'unique:resource,link', new ResourceSiteDomainRule($this->site))
                ->help(__('nova.resource_link_help')),
        ];
    }
}
