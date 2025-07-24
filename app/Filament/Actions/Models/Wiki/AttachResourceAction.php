<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Wiki;

use App\Actions\Models\Wiki\AttachResourceAction as AttachResource;
use App\Contracts\Models\HasResources;
use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Actions\BaseAction;
use App\Filament\Components\Fields\TextInput;
use App\Models\BaseModel;
use App\Models\Wiki\ExternalResource;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

abstract class AttachResourceAction extends BaseAction
{
    /**
     * The sites available for the action.
     *
     * @var array<int, ResourceSite>
     */
    protected array $sites = [];

    /**
     * Initial setup for the action.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.models.wiki.attach_resource.name'));
        $this->icon(__('filament-icons.actions.models.wiki.attach_resource'));

        $this->visible(Gate::allows('create', ExternalResource::class));

        $this->action(fn (BaseModel&HasResources $record, array $data, AttachResource $attachResource) => $attachResource->handle($record, $data, $this->sites));
    }

    /**
     * Get the schema available on the action.|null.
     */
    public function getSchema(Schema $schema): ?Schema
    {
        $components = [];
        $model = $this->getRecord();

        if (! ($model instanceof HasResources)) {
            return $schema;
        }

        $resources = $model->resources()
            ->get([ExternalResource::ATTRIBUTE_SITE])
            ->pluck(ExternalResource::ATTRIBUTE_SITE)
            ->keyBy(fn (ResourceSite $site) => $site->value)
            ->keys();

        foreach ($this->sites as $resourceSite) {
            if ($resources->contains($resourceSite->value)) {
                continue;
            }

            $resourceSiteLower = Str::lower($resourceSite->name);

            $components[] = TextInput::make($resourceSite->name)
                ->label($resourceSite->localize())
                ->helperText(__("filament.actions.models.wiki.attach_resource.fields.{$resourceSiteLower}.help"))
                ->uri()
                ->maxLength(192)
                ->rule($resourceSite->getFormatRule($model));
        }

        return $schema
            ->components($components);
    }

    /**
     * Get the sites available for the action.
     *
     * @param  ResourceSite[]  $sites
     * @return static
     */
    public function sites($sites): static
    {
        $this->sites = $sites;

        return $this;
    }
}
