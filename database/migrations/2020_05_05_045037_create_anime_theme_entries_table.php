<?php

declare(strict_types=1);

use App\Constants\ModelConstants;
use App\Models\Wiki\Anime\AnimeTheme;
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
        if (! Schema::hasTable(AnimeThemeEntry::TABLE)) {
            Schema::create(AnimeThemeEntry::TABLE, function (Blueprint $table) {
                $table->id(AnimeThemeEntry::ATTRIBUTE_ID);
                $table->timestamps(6);
                $table->softDeletes(ModelConstants::ATTRIBUTE_DELETED_AT, 6);
                $table->integer(AnimeThemeEntry::ATTRIBUTE_VERSION)->nullable();
                $table->string(AnimeThemeEntry::ATTRIBUTE_EPISODES)->nullable();
                $table->boolean(AnimeThemeEntry::ATTRIBUTE_NSFW)->default(false);
                $table->boolean(AnimeThemeEntry::ATTRIBUTE_SPOILER)->default(false);
                $table->text(AnimeThemeEntry::ATTRIBUTE_NOTES)->nullable();

                $table->unsignedBigInteger(AnimeThemeEntry::ATTRIBUTE_THEME);
                $table->foreign(AnimeThemeEntry::ATTRIBUTE_THEME)->references(AnimeTheme::ATTRIBUTE_ID)->on(AnimeTheme::TABLE)->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(AnimeThemeEntry::TABLE);
    }
};
