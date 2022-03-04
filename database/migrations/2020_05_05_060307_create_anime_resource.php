<?php

declare(strict_types=1);

use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Pivots\AnimeResource;
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
        Schema::create(AnimeResource::TABLE, function (Blueprint $table) {
            $table->timestamps(6);
            $table->unsignedBigInteger(AnimeResource::ATTRIBUTE_ANIME);
            $table->foreign(AnimeResource::ATTRIBUTE_ANIME)->references(Anime::ATTRIBUTE_ID)->on(Anime::TABLE)->cascadeOnDelete();
            $table->unsignedBigInteger(AnimeResource::ATTRIBUTE_RESOURCE);
            $table->foreign(AnimeResource::ATTRIBUTE_RESOURCE)->references(ExternalResource::ATTRIBUTE_ID)->on(ExternalResource::TABLE)->cascadeOnDelete();
            $table->primary([AnimeResource::ATTRIBUTE_ANIME, AnimeResource::ATTRIBUTE_RESOURCE]);
            $table->string(AnimeResource::ATTRIBUTE_AS)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(AnimeResource::TABLE);
    }
};
