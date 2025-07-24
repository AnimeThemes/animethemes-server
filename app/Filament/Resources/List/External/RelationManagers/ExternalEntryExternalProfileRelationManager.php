<?php

declare(strict_types=1);

namespace App\Filament\Resources\List\External\RelationManagers;

use App\Filament\RelationManagers\List\External\ExternalEntryRelationManager;
use App\Models\List\External\ExternalEntry;
use App\Models\List\ExternalProfile;
use Filament\Tables\Table;

class ExternalEntryExternalProfileRelationManager extends ExternalEntryRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @var string
     */
    protected static string $relationship = ExternalProfile::RELATION_EXTERNAL_ENTRIES;

    /**
     * The index page of the resource.
     */
    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(ExternalEntry::RELATION_PROFILE)
        );
    }
}
