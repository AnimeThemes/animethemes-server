<?php

declare(strict_types=1);

use App\Models\Wiki\Artist;
use App\Pivots\Wiki\ArtistImage;
use App\Pivots\Wiki\ArtistMember;
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
        if (! Schema::hasColumn(Artist::TABLE, Artist::ATTRIBUTE_INFORMATION)) {
            Schema::table(Artist::TABLE, function (Blueprint $table) {
                $table->text(Artist::ATTRIBUTE_INFORMATION)->nullable();
            });
        }

        if (! Schema::hasColumn(ArtistImage::TABLE, ArtistImage::ATTRIBUTE_DEPTH)) {
            Schema::table(ArtistImage::TABLE, function (Blueprint $table) {
                $table->integer(ArtistImage::ATTRIBUTE_DEPTH)->nullable();
            });
        }

        if (! Schema::hasColumn(ArtistMember::TABLE, ArtistMember::ATTRIBUTE_DETAILS)) {
            Schema::table(ArtistMember::TABLE, function (Blueprint $table) {
                $table->string(ArtistMember::ATTRIBUTE_DETAILS)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn(Artist::TABLE, Artist::ATTRIBUTE_INFORMATION)) {
            Schema::table(Artist::TABLE, function (Blueprint $table) {
                $table->dropColumn(Artist::ATTRIBUTE_INFORMATION);
            });
        }

        if (Schema::hasColumn(ArtistImage::TABLE, ArtistImage::ATTRIBUTE_DEPTH)) {
            Schema::table(ArtistImage::TABLE, function (Blueprint $table) {
                $table->dropColumn(ArtistImage::ATTRIBUTE_DEPTH);
            });
        }

        if (Schema::hasColumn(ArtistMember::TABLE, ArtistMember::ATTRIBUTE_DETAILS)) {
            Schema::table(ArtistMember::TABLE, function (Blueprint $table) {
                $table->dropColumn(ArtistMember::ATTRIBUTE_DETAILS);
            });
        }
    }
};
