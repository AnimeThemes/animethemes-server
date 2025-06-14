<?php

declare(strict_types=1);

namespace App\Concerns\Filament\Actions\Models\Wiki;

use App\Actions\Models\Wiki\AttachResourceAction;
use App\Contracts\Models\HasResources;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\BaseModel;
use App\Models\Wiki\ExternalResource;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Str;

/**
 * Trait AttachResourceActionTrait.
 */
trait AttachResourceActionTrait
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

        $this->modalWidth(MaxWidth::FourExtraLarge);

        $this->authorize('create', ExternalResource::class);

        $this->action(fn (BaseModel&HasResources $record, array $data, AttachResourceAction $attachResource) => $attachResource->handle($record, $data, $this->sites));
    }

    /**
     * Get the fields available on the action.
     *
     * @param  Form  $form
     * @return Form|null
     */
    public function getForm(Form $form): ?Form
    {
        $fields = [];
        $model = $this->getRecord();

        if (!($model instanceof HasResources)) return $form;

        $resources = $model->resources()
            ->get([ExternalResource::ATTRIBUTE_SITE])
            ->pluck(ExternalResource::ATTRIBUTE_SITE)
            ->keyBy(fn (ResourceSite $site) => $site->value)
            ->keys();

        foreach ($this->sites as $resourceSite) {
            if ($resources->contains($resourceSite->value)) continue;

            $resourceSiteLower = Str::lower($resourceSite->name);

            $fields[] = TextInput::make($resourceSite->name)
                ->label($resourceSite->localize())
                ->helperText(__("filament.actions.models.wiki.attach_resource.fields.{$resourceSiteLower}.help"))
                ->url()
                ->maxLength(192)
                ->rule($resourceSite->getFormatRule($model));
        }

        return $form
            ->schema($fields);
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
