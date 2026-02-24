<?php

declare(strict_types=1);

namespace App\Filament\Resources\Document\Page\RelationManagers;

use App\Enums\Pivots\Document\PageRoleType;
use App\Filament\Actions\Base\AttachAction;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\RelationManagers\Auth\RoleRelationManager;
use App\Models\Auth\Role;
use App\Models\Document\Page;
use App\Pivots\Document\PageRole;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Component;
use Filament\Tables\Columns\Column;
use Filament\Tables\Table;

class RolePageRelationManager extends RoleRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = Page::RELATION_ROLES;

    /**
     * @return Component[]
     */
    public function getPivotComponents(): array
    {
        return [
            Select::make(PageRole::ATTRIBUTE_TYPE)
                ->label(__('filament.fields.page_role.type.name'))
                ->helperText(__('filament.fields.page_role.type.help'))
                ->options(PageRoleType::class)
                ->required(),
        ];
    }

    /**
     * @return Column[]
     */
    public function getPivotColumns(): array
    {
        return [
            TextColumn::make(PageRole::ATTRIBUTE_TYPE)
                ->label(__('filament.fields.page_role.type.name')),
        ];
    }

    public function table(Table $table): Table
    {
        return parent::table(
            $table->allowDuplicates()
        );
    }

    public static function getHeaderActions(): array
    {
        return [
            AttachAction::make()
                ->recordSelect(
                    fn (Select $select): Select => $select->options(
                        Role::query()
                            ->pluck(Role::ATTRIBUTE_NAME, Role::ATTRIBUTE_ID)
                    )
                ),
        ];
    }
}
