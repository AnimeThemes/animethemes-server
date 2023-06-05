<?php

declare(strict_types=1);

namespace App\Nova\Actions\Models\Wiki;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\ExternalResource;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class AttachResourceAction.
 */
abstract class AttachResourceAction extends Action
{
    /**
     * Create a new action instance.
     *
     * @param  ResourceSite  $site
     */
    public function __construct(protected ResourceSite $site)
    {
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
        return __('nova.actions.models.wiki.attach_resource.name', ['site' => $this->site->description]);
    }

    /**
     * Perform the action on the given models.
     *
     * @param  ActionFields  $fields
     * @param  Collection  $models
     * @return Collection
     */
    public function handle(ActionFields $fields, Collection $models): Collection
    {
        $resource = $this->getOrCreateResource($fields);

        $relation = $this->relation($resource);

        $relation->attach($models);

        return $models;
    }

    /**
     * Get or Create Resource from link field.
     *
     * @param  ActionFields  $fields
     * @return ExternalResource
     */
    protected function getOrCreateResource(ActionFields $fields): ExternalResource
    {
        /** @var string $link */
        $link = $fields->get('link');

        $resource = ExternalResource::query()
            ->where(ExternalResource::ATTRIBUTE_LINK, $link)
            ->first();

        if ($resource === null) {
            $resource = ExternalResource::query()->create([
                ExternalResource::ATTRIBUTE_EXTERNAL_ID => ResourceSite::parseIdFromLink($link),
                ExternalResource::ATTRIBUTE_LINK => $link,
                ExternalResource::ATTRIBUTE_SITE => $this->site->value,
            ]);
        }

        return $resource;
    }

    /**
     * Get the relation to the action models.
     *
     * @param  ExternalResource  $resource
     * @return BelongsToMany
     */
    abstract protected function relation(ExternalResource $resource): BelongsToMany;

    /**
     * Get the fields available on the action.
     *
     * @param  NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request): array
    {
        return array_merge(
            parent::fields($request),
            [
                Text::make(__('nova.actions.models.wiki.attach_resource.fields.link.name'), 'link')
                    ->required()
                    ->rules(['required', 'max:192', 'url', $this->getFormatRule()])
                    ->help(__('nova.actions.models.wiki.attach_resource.fields.link.help')),
            ]
        );
    }

    /**
     * Get the format validation rule.
     *
     * @return ValidationRule
     */
    abstract protected function getFormatRule(): ValidationRule;
}
