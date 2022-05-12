<?php

declare(strict_types=1);

use App\Models\Wiki\Artist;
use App\Pivots\ArtistMember;
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
        if (! Schema::hasTable(ArtistMember::TABLE)) {
            Schema::create(ArtistMember::TABLE, function (Blueprint $table) {
                $table->timestamps(6);
                $table->unsignedBigInteger(ArtistMember::ATTRIBUTE_ARTIST);
                $table->foreign(ArtistMember::ATTRIBUTE_ARTIST)->references(Artist::ATTRIBUTE_ID)->on(Artist::TABLE)->cascadeOnDelete();
                $table->unsignedBigInteger(ArtistMember::ATTRIBUTE_MEMBER);
                $table->foreign(ArtistMember::ATTRIBUTE_MEMBER)->references(Artist::ATTRIBUTE_ID)->on(Artist::TABLE)->cascadeOnDelete();
                $table->primary([ArtistMember::ATTRIBUTE_ARTIST, ArtistMember::ATTRIBUTE_MEMBER]);
                $table->string(ArtistMember::ATTRIBUTE_AS)->nullable();
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
        Schema::dropIfExists(ArtistMember::TABLE);
    }
};
