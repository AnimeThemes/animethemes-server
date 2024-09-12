<?php

declare(strict_types=1);

// config for Awcodes/Recently
// Filament Plugin
return [
    'user_model' => App\Models\Auth\User::class,
    'max_items' => 20,
    'width' => 'xl', 
    'global_search' => true,
    'menu' => true,
    'icon' => 'heroicon-o-arrow-uturn-left',
];
