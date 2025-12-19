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
        if (! Schema::hasColumn('playlists', 'first_id')) {
            Schema::table('playlists', function (Blueprint $table) {
                $table->unsignedBigInteger('first_id')->nullable();
                $table->foreign('first_id')->references('track_id')->on('playlist_tracks')->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('playlists', 'last_id')) {
            Schema::table('playlists', function (Blueprint $table) {
                $table->unsignedBigInteger('last_id')->nullable();
                $table->foreign('last_id')->references('track_id')->on('playlist_tracks')->nullOnDelete();
            });
        }
    }
};
