<?php

declare(strict_types=1);

use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use App\Pivots\ArtistImage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateArtistImage.
 */
class CreateArtistImage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create(ArtistImage::TABLE, function (Blueprint $table) {
            $table->timestamps(6);
            $table->unsignedBigInteger(ArtistImage::ATTRIBUTE_ARTIST);
            $table->foreign(ArtistImage::ATTRIBUTE_ARTIST)->references(Artist::ATTRIBUTE_ID)->on(Artist::TABLE)->cascadeOnDelete();
            $table->unsignedBigInteger(ArtistImage::ATTRIBUTE_IMAGE);
            $table->foreign(ArtistImage::ATTRIBUTE_IMAGE)->references(Image::ATTRIBUTE_ID)->on(Image::TABLE)->cascadeOnDelete();
            $table->primary([ArtistImage::ATTRIBUTE_ARTIST, ArtistImage::ATTRIBUTE_IMAGE]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(ArtistImage::TABLE);
    }
}
