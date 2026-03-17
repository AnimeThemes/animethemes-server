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
        Schema::table('performances', function (Blueprint $table) {
            $table->index('song_id');
            $table->dropUnique('unique_performance');
            $table->unique(['song_id', 'artist_type', 'artist_id', 'deleted_at'], 'unique_performance');
            $table->dropIndex('performances_song_id_index');
        });
    }
};
