<?php

declare(strict_types=1);

namespace App\Nova\Actions\Models\Wiki;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Song;
use App\Models\Wiki\Studio;
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
     * @param  ResourceSite[]  $sites
     */
    public function __construct(protected array $sites)
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
        return __('nova.actions.models.wiki.attach_resource.name');
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
        $resources = $this->getOrCreateResource($fields);

        foreach ($resources as $resource) {
            $relation = $this->relation($resource);

            $relation->attach($models);
        }

        return $models;
    }

    /**
     * Get or Create Resource from link field.
     *
     * @param  ActionFields  $fields
     * @return ExternalResource[]
     */
    protected function getOrCreateResource(ActionFields $fields): array
    {
        $resources = [];

        foreach ($this->sites as $resourceSite) {
            $link = $fields->get($resourceSite->name);

            if (empty($link)) continue;

            $resource = ExternalResource::query()
                ->where(ExternalResource::ATTRIBUTE_LINK, $link)
                ->first();

            if ($resource === null) {
                $resource = ExternalResource::query()->create([
                    ExternalResource::ATTRIBUTE_EXTERNAL_ID => ResourceSite::parseIdFromLink($link),
                    ExternalResource::ATTRIBUTE_LINK => $link,
                    ExternalResource::ATTRIBUTE_SITE => $resourceSite->value,
                ]);
            }

            $resources[] = $resource;
        }

        return $resources;
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
        $fields = [];
        $model = $request->findModelQuery()->first();

        foreach ($this->sites as $resourceSite) {
            if ($model instanceof Anime || $model instanceof Artist || $model instanceof Song || $model instanceof Studio) {
                $resources = $model->resources();
                if ($resources->where(ExternalResource::ATTRIBUTE_SITE, $resourceSite->value)->exists()) continue;
            }
            
            $resourceSiteLower = strtolower($resourceSite->name);

            $fields[] = Text::make($resourceSite->localize(), $resourceSite->name)
                            ->help(__("nova.actions.models.wiki.attach_resource.fields.{$resourceSiteLower}.help"))
                            ->rules(fn ($request) => [
                                'max:192',
                                empty($request->input($resourceSite->name)) ? '' : 'url',
                                empty($request->input($resourceSite->name)) ? '' : $this->getFormatRule($resourceSite),
                            ]);
        }

        return array_merge(
            parent::fields($request),
            $fields
        );
    }

    /**
     * Get the format validation rule.
     *
     * @param  ResourceSite  $site
     * @return ValidationRule
     */
    abstract protected function getFormatRule(ResourceSite $site): ValidationRule;
}
