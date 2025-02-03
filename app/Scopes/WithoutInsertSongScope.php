<?php

declare(strict_types=1);

namespace App\Scopes;

use App\Enums\Models\Wiki\ThemeType;
use App\Models\Wiki\Anime\AnimeTheme;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class WithoutInsertSongScope.
 */
class WithoutInsertSongScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  Builder  $builder
     * @param  Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (Filament::isServing()) {
            return;
        }

        $builder->whereNot(AnimeTheme::ATTRIBUTE_TYPE, ThemeType::IN->value);
    }
}