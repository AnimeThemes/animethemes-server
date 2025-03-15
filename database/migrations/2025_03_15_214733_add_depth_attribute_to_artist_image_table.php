<?php

declare(strict_types=1);

use App\Pivots\Wiki\ArtistImage;
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
        if (! Schema::hasColumn(ArtistImage::TABLE, ArtistImage::ATTRIBUTE_DEPTH)) {
            Schema::table(ArtistImage::TABLE, function (Blueprint $table) {
                $table->integer(ArtistImage::ATTRIBUTE_DEPTH)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn(ArtistImage::TABLE, ArtistImage::ATTRIBUTE_DEPTH)) {
            Schema::table(ArtistImage::TABLE, function (Blueprint $table) {
                $table->dropColumn(ArtistImage::ATTRIBUTE_DEPTH);
            });
        }
    }
};
