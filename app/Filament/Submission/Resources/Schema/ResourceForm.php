<?php

declare(strict_types=1);

namespace App\Filament\Submission\Resources\Schema;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Fields\TextInput;
use App\Models\User\Submission\SubmissionExternalResource;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ResourceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make(SubmissionExternalResource::ATTRIBUTE_SITE)
                    ->label(__('filament.fields.external_resource.site.name'))
                    ->helperText(__('filament.fields.external_resource.site.help'))
                    ->options(ResourceSite::class)
                    ->required(),

                TextInput::make(SubmissionExternalResource::ATTRIBUTE_LINK)
                    ->label(__('filament.fields.external_resource.link.name'))
                    ->helperText(__('filament.fields.external_resource.link.help'))
                    ->required()
                    ->live()
                    ->uri()
                    ->partiallyRenderComponentsAfterStateUpdated([SubmissionExternalResource::ATTRIBUTE_SITE, SubmissionExternalResource::ATTRIBUTE_EXTERNAL_ID])
                    ->afterStateUpdated(function (Set $set, ?string $state): void {
                        if ($state !== null) {
                            $set(SubmissionExternalResource::ATTRIBUTE_SITE, ResourceSite::valueOf($state) ?? ResourceSite::OFFICIAL_SITE);
                            $set(SubmissionExternalResource::ATTRIBUTE_EXTERNAL_ID, ResourceSite::parseIdFromLink($state));
                        }
                    }),

                TextInput::make(SubmissionExternalResource::ATTRIBUTE_EXTERNAL_ID)
                    ->label(__('filament.fields.external_resource.external_id.name'))
                    ->helperText(__('filament.fields.external_resource.external_id.help'))
                    ->integer(),
            ])
            ->columns(1);
    }
}
