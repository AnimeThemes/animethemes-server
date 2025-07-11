<?php

declare(strict_types=1);

use App\Models\Admin\FeaturedTheme;
use App\Models\Auth\User;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
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
        if (! Schema::hasTable(FeaturedTheme::TABLE)) {
            Schema::create(FeaturedTheme::TABLE, function (Blueprint $table) {
                $table->id(FeaturedTheme::ATTRIBUTE_ID);
                $table->timestamps(6);
                $table->timestamp(FeaturedTheme::ATTRIBUTE_START_AT, 6)->nullable();
                $table->timestamp(FeaturedTheme::ATTRIBUTE_END_AT, 6)->nullable();

                $table->unsignedBigInteger(FeaturedTheme::ATTRIBUTE_USER)->nullable();
                $table->foreign(FeaturedTheme::ATTRIBUTE_USER)->references(User::ATTRIBUTE_ID)->on(User::TABLE)->nullOnDelete();

                $table->unsignedBigInteger(FeaturedTheme::ATTRIBUTE_ENTRY)->nullable();
                $table->foreign(FeaturedTheme::ATTRIBUTE_ENTRY)->references(AnimeThemeEntry::ATTRIBUTE_ID)->on(AnimeThemeEntry::TABLE)->nullOnDelete();

                $table->unsignedBigInteger(FeaturedTheme::ATTRIBUTE_VIDEO)->nullable();
                $table->foreign(FeaturedTheme::ATTRIBUTE_VIDEO)->references(Video::ATTRIBUTE_ID)->on(Video::TABLE)->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(FeaturedTheme::TABLE);
    }
};
