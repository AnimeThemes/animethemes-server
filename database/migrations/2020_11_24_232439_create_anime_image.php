<?php

declare(strict_types=1);

use App\Models\Wiki\Anime;
use App\Models\Wiki\Image;
use App\Pivots\Wiki\AnimeImage;
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
        if (! Schema::hasTable(AnimeImage::TABLE)) {
            Schema::create(AnimeImage::TABLE, function (Blueprint $table) {
                $table->timestamps(6);
                $table->unsignedBigInteger(AnimeImage::ATTRIBUTE_ANIME);
                $table->foreign(AnimeImage::ATTRIBUTE_ANIME)->references(Anime::ATTRIBUTE_ID)->on(Anime::TABLE)->cascadeOnDelete();
                $table->unsignedBigInteger(AnimeImage::ATTRIBUTE_IMAGE);
                $table->foreign(AnimeImage::ATTRIBUTE_IMAGE)->references(Image::ATTRIBUTE_ID)->on(Image::TABLE)->cascadeOnDelete();
                $table->primary([AnimeImage::ATTRIBUTE_ANIME, AnimeImage::ATTRIBUTE_IMAGE]);
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
        Schema::dropIfExists(AnimeImage::TABLE);
    }
};
