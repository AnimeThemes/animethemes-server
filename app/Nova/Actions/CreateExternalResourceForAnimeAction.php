<?php

namespace App\Nova\Actions;

use App\Enums\ResourceType;
use App\Models\ExternalResource;
use App\Rules\ResourceTypeDomain;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Text;

class CreateExternalResourceForAnimeAction extends Action
{
    use InteractsWithQueue, Queueable;

    private $type;

    public function __construct($type)
    {
        $this->type = $type;
    }

    /**
     * Get the displayable name of the action.
     *
     * @return string
     */
    public function name()
    {
        return __('nova.anime_create_resource_action', ['type' => ResourceType::getDescription($this->type)]);
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
        // We want this action applied to singular anime
        if ($models->count() !== 1) {
            return Action::danger(__('nova.anime_create_resource_action_cardinality_error'));
        }

        // Create Resource Model with link and provided type
        $resource = ExternalResource::create([
            'type' => $this->type,
            'link' => $fields->link
        ]);

        // Check if resource creation is successful
        if (!$resource->exists()) {
            return Action::danger(__('nova.error_resource_creation'));
        }

        // Attach Resource to Anime and provide success message
        $resource->anime()->sync($models);
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
                ->rules('required', 'max:192', 'url', 'unique:resource,link', new ResourceTypeDomain(ResourceType::MAL))
                ->help(__('nova.resource_link_help')),            
        ];
    }
}
