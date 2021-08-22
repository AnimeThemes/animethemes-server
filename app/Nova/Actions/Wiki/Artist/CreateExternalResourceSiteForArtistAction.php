<?php

declare(strict_types=1);

namespace App\Nova\Actions\Wiki\Artist;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\ExternalResource;
use App\Rules\Wiki\ResourceSiteDomainRule;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Text;

/**
 * Class CreateExternalResourceSiteForArtistAction.
 */
class CreateExternalResourceSiteForArtistAction extends Action
{
    use InteractsWithQueue;
    use Queueable;

    /**
     * The resource site key.
     *
     * @var int
     */
    protected int $site;

    /**
     * @param int $site
     */
    public function __construct(int $site)
    {
        $this->site = $site;
    }

    /**
     * Get the displayable name of the action.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function name(): string
    {
        return __('nova.artist_create_resource_action', ['site' => ResourceSite::getDescription($this->site)]);
    }

    /**
     * Perform the action on the given models.
     *
     * @param ActionFields $fields
     * @param Collection $models
     * @return array
     */
    public function handle(ActionFields $fields, Collection $models): array
    {
        // Create Resource Model with link and provided site
        $resource = ExternalResource::factory()->createOne([
            'site' => $this->site,
            'link' => $fields->get('link'),
            'external_id' => null,
        ]);

        // Attach Resource to Anime and provide success message
        $resource->artists()->attach($models);

        return Action::message(__('nova.artist_create_resource_action_success'));
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
                        'unique:resources,link',
                        (new ResourceSiteDomainRule($this->site))->__toString(),
                    ])
                    ->help(__('nova.resource_link_help')),
            ]
        );
    }
}
