<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('playlist_tracks')) {
            Schema::create('playlist_tracks', function (Blueprint $table) {
                $table->id('track_id');
                $table->timestamps(6);
                $hashIdColumn = $table->string('hashid')->nullable();
                if (DB::connection() instanceof MySqlConnection) {
                    // Set collation to binary to be case-sensitive
                    $hashIdColumn->collation('utf8mb4_bin');
                }

                $table->unsignedBigInteger('playlist_id');
                $table->foreign('playlist_id')->references('playlist_id')->on('playlists')->cascadeOnDelete();

                $table->unsignedBigInteger('entry_id')->nullable();
                $table->foreign('entry_id')->references('entry_id')->on('anime_theme_entries')->nullOnDelete();

                $table->unsignedBigInteger('video_id')->nullable();
                $table->foreign('video_id')->references('video_id')->on('videos')->nullOnDelete();

                $table->unsignedBigInteger('previous_id')->nullable();
                $table->foreign('previous_id')->references('track_id')->on('playlist_tracks')->nullOnDelete();

                $table->unsignedBigInteger('next_id')->nullable();
                $table->foreign('next_id')->references('track_id')->on('playlist_tracks')->nullOnDelete();
            });
        }
    }
};
