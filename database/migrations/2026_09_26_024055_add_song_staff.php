<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
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
        if (! Schema::hasTable('song_staff')) {
            Schema::rename('performances', 'song_staff');

            Schema::table('song_staff', function (Blueprint $table) {
                $table->renameColumn('performance_id', 'id');

                $table->string('role')->nullable()->after('member_id');

                $table->dropForeign('performances_artist_id_foreign');
                $table->dropForeign('performances_member_id_foreign');
                $table->dropForeign('performances_song_id_foreign');

                $table->dropIndex('performances_artist_type_artist_id_index');
                $table->dropIndex('performances_member_id_foreign');

                $table->dropUnique('unique_performance');

                $table->unique(
                    ['song_id', 'artist_id', 'member_id', 'role', 'deleted_at'],
                    'unique_song_staff'
                );

                $table->foreign('artist_id')->references('artist_id')->on('artists')->cascadeOnDelete();
                $table->foreign('member_id')->references('artist_id')->on('artists')->cascadeOnDelete();
                $table->foreign('song_id')->references('song_id')->on('songs')->cascadeOnDelete();
            });

            DB::table('song_staff')->update([
                'role' => 'Performance',
            ]);

            Schema::table('song_staff', function (Blueprint $table) {
                $table->string('role')->nullable(false)->change();
            });
        }
    }
};
