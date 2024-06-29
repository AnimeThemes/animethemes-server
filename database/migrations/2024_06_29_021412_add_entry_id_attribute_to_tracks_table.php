<?php

declare(strict_types=1);

use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
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
        if (! Schema::hasColumn(PlaylistTrack::TABLE, PlaylistTrack::ATTRIBUTE_ENTRY)) {
            Schema::table(PlaylistTrack::TABLE, function (Blueprint $table) {
                $table->unsignedBigInteger(PlaylistTrack::ATTRIBUTE_ENTRY)->nullable();
                $table->foreign(PlaylistTrack::ATTRIBUTE_ENTRY)->references(AnimeThemeEntry::ATTRIBUTE_ID)->on(AnimeThemeEntry::TABLE)->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn(PlaylistTrack::TABLE, PlaylistTrack::ATTRIBUTE_ENTRY)) {
            Schema::table(PlaylistTrack::TABLE, function (Blueprint $table) {
                $table->dropConstrainedForeignId(PlaylistTrack::ATTRIBUTE_ENTRY);
            });
        }
    }
};
