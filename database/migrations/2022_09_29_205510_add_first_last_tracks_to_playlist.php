<?php

declare(strict_types=1);

use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (! Schema::hasColumn(Playlist::TABLE, Playlist::ATTRIBUTE_FIRST)) {
            Schema::table(Playlist::TABLE, function (Blueprint $table) {
                $table->unsignedBigInteger(Playlist::ATTRIBUTE_FIRST)->nullable();
                $table->foreign(Playlist::ATTRIBUTE_FIRST)->references(PlaylistTrack::ATTRIBUTE_ID)->on(PlaylistTrack::TABLE)->nullOnDelete();
            });
        }

        if (! Schema::hasColumn(Playlist::TABLE, Playlist::ATTRIBUTE_LAST)) {
            Schema::table(Playlist::TABLE, function (Blueprint $table) {
                $table->unsignedBigInteger(Playlist::ATTRIBUTE_LAST)->nullable();
                $table->foreign(Playlist::ATTRIBUTE_LAST)->references(PlaylistTrack::ATTRIBUTE_ID)->on(PlaylistTrack::TABLE)->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        if (Schema::hasColumn(Playlist::TABLE, Playlist::ATTRIBUTE_FIRST)) {
            Schema::table(Playlist::TABLE, function (Blueprint $table) {
                $table->dropConstrainedForeignId(Playlist::ATTRIBUTE_FIRST);
            });
        }

        if (Schema::hasColumn(Playlist::TABLE, Playlist::ATTRIBUTE_LAST)) {
            Schema::table(Playlist::TABLE, function (Blueprint $table) {
                $table->dropConstrainedForeignId(Playlist::ATTRIBUTE_LAST);
            });
        }
    }
};