<?php

declare(strict_types=1);

use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Group;
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
        if (! Schema::hasColumn(AnimeTheme::TABLE, AnimeTheme::ATTRIBUTE_THEME_GROUP)) {
            Schema::table(AnimeTheme::TABLE, function (Blueprint $table) {
                $table->unsignedBigInteger(AnimeTheme::ATTRIBUTE_THEME_GROUP)->nullable();
                $table->foreign(AnimeTheme::ATTRIBUTE_THEME_GROUP)->references(Group::ATTRIBUTE_ID)->on(Group::TABLE)->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn(AnimeTheme::TABLE, AnimeTheme::ATTRIBUTE_THEME_GROUP)) {
            Schema::table(AnimeTheme::TABLE, function (Blueprint $table) {
                $table->dropConstrainedForeignId(AnimeTheme::ATTRIBUTE_THEME_GROUP);
            });
        }
    }
};
