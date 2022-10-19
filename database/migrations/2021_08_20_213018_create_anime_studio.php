<?php

declare(strict_types=1);

use App\Models\Wiki\Anime;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\AnimeStudio;
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
        if (! Schema::hasTable(AnimeStudio::TABLE)) {
            Schema::create(AnimeStudio::TABLE, function (Blueprint $table) {
                $table->timestamps(6);
                $table->unsignedBigInteger(AnimeStudio::ATTRIBUTE_ANIME);
                $table->foreign(AnimeStudio::ATTRIBUTE_ANIME)->references(Anime::ATTRIBUTE_ID)->on(Anime::TABLE)->cascadeOnDelete();
                $table->unsignedBigInteger(AnimeStudio::ATTRIBUTE_STUDIO);
                $table->foreign(AnimeStudio::ATTRIBUTE_STUDIO)->references(Studio::ATTRIBUTE_ID)->on(Studio::TABLE)->cascadeOnDelete();
                $table->primary([AnimeStudio::ATTRIBUTE_ANIME, AnimeStudio::ATTRIBUTE_STUDIO]);
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
        Schema::dropIfExists(AnimeStudio::TABLE);
    }
};
