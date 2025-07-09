<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Wiki;

use App\Actions\Models\Wiki\AttachResourceAction as AttachResource;
use App\Contracts\Models\HasResources;
use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Actions\BaseAction;
use App\Models\BaseModel;
use App\Models\Wiki\ExternalResource;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Class AttachResourceAction.
 */
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
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.models.wiki.attach_resource.name'));
        $this->icon(__('filament-icons.actions.models.wiki.attach_resource'));

        $this->visible(Auth::user()->can('create', ExternalResource::class));

        $this->action(fn (BaseModel&HasResources $record, array $data, AttachResource $attachResource) => $attachResource->handle($record, $data, $this->sites));
    }

    /**
     * Get the fields available on the action.
     *
     * @param  Schema  $schema
     * @return Schema|null
     */
    public function getSchema(Schema $schema): ?Schema
    {
        $fields = [];
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

            $fields[] = TextInput::make($resourceSite->name)
                ->label($resourceSite->localize())
                ->helperText(__("filament.actions.models.wiki.attach_resource.fields.{$resourceSiteLower}.help"))
                ->url()
                ->maxLength(192)
                ->rule($resourceSite->getFormatRule($model));
        }

        return $schema
            ->components($fields);
    }

    /**
     * Get the sites available for the action.
     *
     * @param  array<int, ResourceSite>  $sites
     * @return static
     */
    public function sites($sites): static
    {
        $this->sites = $sites;

        return $this;
    }
}
