<?php

declare(strict_types=1);

use App\Pivots\Wiki\ArtistMember;
use App\Pivots\Wiki\ArtistSong;
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
        if (! Schema::hasColumn(ArtistSong::TABLE, ArtistSong::ATTRIBUTE_ALIAS)) {
            Schema::table(ArtistSong::TABLE, function (Blueprint $table) {
                $table->string(ArtistSong::ATTRIBUTE_ALIAS)->nullable();
            });
        }

        if (! Schema::hasColumn(ArtistMember::TABLE, ArtistMember::ATTRIBUTE_ALIAS)) {
            Schema::table(ArtistMember::TABLE, function (Blueprint $table) {
                $table->string(ArtistMember::ATTRIBUTE_ALIAS)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn(ArtistSong::TABLE, ArtistSong::ATTRIBUTE_ALIAS)) {
            Schema::table(ArtistSong::TABLE, function (Blueprint $table) {
                $table->dropColumn(ArtistSong::ATTRIBUTE_ALIAS);
            });
        }

        if (Schema::hasColumn(ArtistMember::TABLE, ArtistMember::ATTRIBUTE_ALIAS)) {
            Schema::table(ArtistMember::TABLE, function (Blueprint $table) {
                $table->dropColumn(ArtistMember::ATTRIBUTE_ALIAS);
            });
        }
    }
};
