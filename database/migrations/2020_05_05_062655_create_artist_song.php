<?php

declare(strict_types=1);

use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Pivots\Wiki\ArtistSong;
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
        if (! Schema::hasTable(ArtistSong::TABLE)) {
            Schema::create(ArtistSong::TABLE, function (Blueprint $table) {
                $table->timestamps(6);
                $table->unsignedBigInteger(ArtistSong::ATTRIBUTE_ARTIST);
                $table->foreign(ArtistSong::ATTRIBUTE_ARTIST)->references(Artist::ATTRIBUTE_ID)->on(Artist::TABLE)->cascadeOnDelete();
                $table->unsignedBigInteger(ArtistSong::ATTRIBUTE_SONG);
                $table->foreign(ArtistSong::ATTRIBUTE_SONG)->references(Song::ATTRIBUTE_ID)->on(Song::TABLE)->cascadeOnDelete();
                $table->primary([ArtistSong::ATTRIBUTE_ARTIST, ArtistSong::ATTRIBUTE_SONG]);
                $table->string(ArtistSong::ATTRIBUTE_AS)->nullable();
                $table->string(ArtistSong::ATTRIBUTE_ALIAS)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(ArtistSong::TABLE);
    }
};
