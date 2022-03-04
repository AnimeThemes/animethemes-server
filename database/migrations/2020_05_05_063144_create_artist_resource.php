<?php

declare(strict_types=1);

use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Pivots\ArtistResource;
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
        Schema::create(ArtistResource::TABLE, function (Blueprint $table) {
            $table->timestamps(6);
            $table->unsignedBigInteger(ArtistResource::ATTRIBUTE_ARTIST);
            $table->foreign(ArtistResource::ATTRIBUTE_ARTIST)->references(Artist::ATTRIBUTE_ID)->on(Artist::TABLE)->cascadeOnDelete();
            $table->unsignedBigInteger(ArtistResource::ATTRIBUTE_RESOURCE);
            $table->foreign(ArtistResource::ATTRIBUTE_RESOURCE)->references(ExternalResource::ATTRIBUTE_ID)->on(ExternalResource::TABLE)->cascadeOnDelete();
            $table->primary([ArtistResource::ATTRIBUTE_ARTIST, ArtistResource::ATTRIBUTE_RESOURCE]);
            $table->string(ArtistResource::ATTRIBUTE_AS)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(ArtistResource::TABLE);
    }
};
