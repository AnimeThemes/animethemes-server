<?php

declare(strict_types=1);

use App\Models\List\Playlist;
use App\Models\Wiki\Image;
use App\Pivots\List\PlaylistImage;
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
        if (! Schema::hasTable(PlaylistImage::TABLE)) {
            Schema::create(PlaylistImage::TABLE, function (Blueprint $table) {
                $table->timestamps(6);
                $table->unsignedBigInteger(PlaylistImage::ATTRIBUTE_PLAYLIST);
                $table->foreign(PlaylistImage::ATTRIBUTE_PLAYLIST)->references(Playlist::ATTRIBUTE_ID)->on(Playlist::TABLE)->cascadeOnDelete();
                $table->unsignedBigInteger(PlaylistImage::ATTRIBUTE_IMAGE);
                $table->foreign(PlaylistImage::ATTRIBUTE_IMAGE)->references(Image::ATTRIBUTE_ID)->on(Image::TABLE)->cascadeOnDelete();
                $table->primary([PlaylistImage::ATTRIBUTE_PLAYLIST, PlaylistImage::ATTRIBUTE_IMAGE]);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(PlaylistImage::TABLE);
    }
};
