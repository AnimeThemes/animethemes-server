<?php

declare(strict_types=1);

namespace App\Filament\Components\Infolist;

use App\Constants\ModelConstants;
use App\Models\BaseModel;
use Filament\Schemas\Components\Section;

class TimestampSection
{
    /**
     * Create a section for displaying timestamps.
     */
    public static function make(): Section
    {
        return Section::make(__('filament.fields.base.timestamps'))
            ->columns(3)
            ->schema([
                TextEntry::make(BaseModel::ATTRIBUTE_CREATED_AT)
                    ->label(__('filament.fields.base.created_at'))
                    ->dateTime(),

                TextEntry::make(BaseModel::ATTRIBUTE_UPDATED_AT)
                    ->label(__('filament.fields.base.updated_at'))
                    ->dateTime(),

                TextEntry::make(ModelConstants::ATTRIBUTE_DELETED_AT)
                    ->label(__('filament.fields.base.deleted_at'))
                    ->dateTime(),
            ]);
    }
}
