<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumns('anime_theme_entries', ['likes_count', 'tracks_count'])) {
            Schema::table('anime_theme_entries', function (Blueprint $table) {
                $table->integer('likes_count')->default(0);
                $table->integer('tracks_count')->default(0);
            });
        }
    }
};
