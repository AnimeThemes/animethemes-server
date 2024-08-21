<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Models\Wiki;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\HeaderActions\BaseHeaderAction;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Song;
use App\Models\Wiki\Studio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

/**
 * Class AttachResourceHeaderAction.
 */
abstract class AttachResourceHeaderAction extends BaseHeaderAction
{
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
        $this->icon('heroicon-o-queue-list');

        $this->modalWidth(MaxWidth::FourExtraLarge);

        $this->authorize('create', ExternalResource::class);
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

        foreach ($this->sites as $resourceSite) {
            if ($model instanceof Anime || $model instanceof Artist || $model instanceof Song || $model instanceof Studio) {
                $resources = $model->resources();
                if ($resources->where(ExternalResource::ATTRIBUTE_SITE, $resourceSite->value)->exists()) continue;
            }
            
            $resourceSiteLower = Str::lower($resourceSite->name);

            $fields[] = TextInput::make($resourceSite->name)
                            ->label($resourceSite->localize())
                            ->helperText(__("filament.actions.models.wiki.attach_resource.fields.{$resourceSiteLower}.help"))
                            ->url()
                            ->maxLength(192)
                            ->rules(['max:192', $this->getFormatRule($resourceSite)]);
        }

        return $form
            ->schema($fields);
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

    /**
     * Get the format validation rule.
     *
     * @param  ResourceSite  $site
     * @return ValidationRule
     */
    abstract protected function getFormatRule(ResourceSite $site): ValidationRule;
}
