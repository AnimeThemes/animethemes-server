<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('resourceables', 'id')) {
            Schema::table('resourceables', function (Blueprint $table) {
                $table->unsignedBigInteger('id')->nullable()->first();
            });

            if (DB::connection() instanceof SQLiteConnection) {
                DB::statement('SET @count = 0;');
                DB::statement('UPDATE resourceables SET id = (@count := @count + 1) ORDER BY created_at;');
            }

            Schema::table('resourceables', function (Blueprint $table) {
                $table->dropForeign('resourceables_resource_id_foreign');
                $table->unique([
                    'resource_id',
                    'resourceable_type',
                    'resourceable_id',
                ], 'resourceables_unique_index');
                $table->dropPrimary();
                $table->unsignedBigInteger('id', true)->primary()->change();
                $table->foreign('resource_id')->references('resource_id')->on('resources')->cascadeOnDelete();
            });
        }

        if (! Schema::hasColumn('imageables', 'id')) {
            Schema::table('imageables', function (Blueprint $table) {
                $table->unsignedBigInteger('id')->nullable()->first();
            });

            if (DB::connection() instanceof SQLiteConnection) {
                DB::statement('SET @count = 0;');
                DB::statement('UPDATE imageables SET id = (@count := @count + 1) ORDER BY created_at;');
            }

            Schema::table('imageables', function (Blueprint $table) {
                $table->dropForeign('imageables_image_id_foreign');
                $table->unique([
                    'image_id',
                    'imageable_type',
                    'imageable_id',
                ], 'imageables_unique_index');
                $table->dropPrimary();
                $table->unsignedBigInteger('id', true)->primary()->change();
                $table->foreign('image_id')->references('image_id')->on('images')->cascadeOnDelete();
            });
        }

        if (! Schema::hasColumn('anime_series', 'id')) {
            Schema::table('anime_series', function (Blueprint $table) {
                $table->unsignedBigInteger('id')->nullable()->first();
            });
            
            if (DB::connection() instanceof SQLiteConnection) {
                DB::statement('SET @count = 0;');
                DB::statement('UPDATE anime_series SET id = (@count := @count + 1) ORDER BY created_at;');
            }

            Schema::table('anime_series', function (Blueprint $table) {
                $table->dropForeign('anime_series_series_id_foreign');
                $table->unique(['anime_id', 'series_id'], 'anime_series_unique_index');
                $table->dropPrimary();
                $table->unsignedBigInteger('id', true)->primary()->change();
                $table->dropForeign('anime_series_anime_id_foreign');
                $table->foreign('anime_id')->references('anime_id')->on('animes')->cascadeOnDelete();
                $table->foreign('series_id')->references('series_id')->on('series')->cascadeOnDelete();
            });
        }

        if (! Schema::hasColumn('anime_theme_entry_video', 'id')) {
            Schema::table('anime_theme_entry_video', function (Blueprint $table) {
                $table->unsignedBigInteger('id')->nullable()->first();
            });
            
            if (DB::connection() instanceof SQLiteConnection) {
                DB::statement('SET @count = 0;');
                DB::statement('UPDATE anime_theme_entry_video SET id = (@count := @count + 1) ORDER BY created_at;');
            }

            Schema::table('anime_theme_entry_video', function (Blueprint $table) {
                $table->dropForeign('anime_theme_entry_video_video_id_foreign');
                $table->unique(['entry_id', 'video_id'], 'entry_video_unique_index');
                $table->dropPrimary();
                $table->unsignedBigInteger('id', true)->primary()->change();
                $table->dropForeign('anime_theme_entry_video_entry_id_foreign');
                $table->foreign('entry_id')->references('entry_id')->on('anime_theme_entries')->cascadeOnDelete();
                $table->foreign('video_id')->references('video_id')->on('videos')->cascadeOnDelete();
            });
        }

        if (! Schema::hasColumn('artist_song', 'id')) {
            Schema::table('artist_song', function (Blueprint $table) {
                $table->unsignedBigInteger('id')->nullable()->first();
            });
            
            if (DB::connection() instanceof SQLiteConnection) {
                DB::statement('SET @count = 0;');
                DB::statement('UPDATE artist_song SET id = (@count := @count + 1) ORDER BY created_at;');
            }

            Schema::table('artist_song', function (Blueprint $table) {
                $table->dropForeign('artist_song_song_id_foreign');
                $table->unique(['artist_id', 'song_id'], 'artist_song_unique_index');
                $table->dropPrimary();
                $table->unsignedBigInteger('id', true)->primary()->change();
                $table->dropForeign('artist_song_artist_id_foreign');
                $table->foreign('artist_id')->references('artist_id')->on('artists')->cascadeOnDelete();
                $table->foreign('song_id')->references('song_id')->on('songs')->cascadeOnDelete();
            });
        }

        if (! Schema::hasColumn('artist_member', 'id')) {
            Schema::table('artist_member', function (Blueprint $table) {
                $table->unsignedBigInteger('id')->nullable()->first();
            });
            
            if (DB::connection() instanceof SQLiteConnection) {
                DB::statement('SET @count = 0;');
                DB::statement('UPDATE artist_member SET id = (@count := @count + 1) ORDER BY created_at;');
            }

            Schema::table('artist_member', function (Blueprint $table) {
                $table->dropForeign('artist_member_member_id_foreign');
                $table->unique(['artist_id', 'member_id'], 'artist_member_unique_index');
                $table->dropPrimary();
                $table->unsignedBigInteger('id', true)->primary()->change();
                $table->dropForeign('artist_member_artist_id_foreign');
                $table->foreign('artist_id')->references('artist_id')->on('artists')->cascadeOnDelete();
                $table->foreign('member_id')->references('artist_id')->on('artists')->cascadeOnDelete();
            });
        }

        if (! Schema::hasColumn('anime_studio', 'id')) {
            Schema::table('anime_studio', function (Blueprint $table) {
                $table->unsignedBigInteger('id')->nullable()->first();
            });
            
            if (DB::connection() instanceof SQLiteConnection) {
                DB::statement('SET @count = 0;');
                DB::statement('UPDATE anime_studio SET id = (@count := @count + 1) ORDER BY created_at;');
            }

            Schema::table('anime_studio', function (Blueprint $table) {
                $table->dropForeign('anime_studio_studio_id_foreign');
                $table->unique(['anime_id', 'studio_id'], 'anime_studio_unique_index');
                $table->dropPrimary();
                $table->unsignedBigInteger('id', true)->primary()->change();
                $table->dropForeign('anime_studio_anime_id_foreign');
                $table->foreign('anime_id')->references('anime_id')->on('animes')->cascadeOnDelete();
                $table->foreign('studio_id')->references('studio_id')->on('studios')->cascadeOnDelete();
            });
        }
    }
};
