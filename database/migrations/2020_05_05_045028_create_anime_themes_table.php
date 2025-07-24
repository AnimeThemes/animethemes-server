<?php

declare(strict_types=1);

use App\Constants\ModelConstants;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Song;
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
        if (! Schema::hasTable(AnimeTheme::TABLE)) {
            Schema::create(AnimeTheme::TABLE, function (Blueprint $table) {
                $table->id(AnimeTheme::ATTRIBUTE_ID);
                $table->timestamps(6);
                $table->softDeletes(ModelConstants::ATTRIBUTE_DELETED_AT, 6);
                $table->integer(AnimeTheme::ATTRIBUTE_TYPE)->nullable();
                $table->integer(AnimeTheme::ATTRIBUTE_SEQUENCE)->nullable();
                $table->string(AnimeTheme::ATTRIBUTE_SLUG);

                $table->unsignedBigInteger(AnimeTheme::ATTRIBUTE_ANIME);
                $table->foreign(AnimeTheme::ATTRIBUTE_ANIME)->references(Anime::ATTRIBUTE_ID)->on(Anime::TABLE)->cascadeOnDelete();

                $table->unsignedBigInteger(AnimeTheme::ATTRIBUTE_SONG)->nullable();
                $table->foreign(AnimeTheme::ATTRIBUTE_SONG)->references(Song::ATTRIBUTE_ID)->on(Song::TABLE)->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(AnimeTheme::TABLE);
    }
};
