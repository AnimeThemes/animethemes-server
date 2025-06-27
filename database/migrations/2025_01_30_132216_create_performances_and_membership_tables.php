<?php

declare(strict_types=1);

use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Models\Wiki\Song\Membership;
use App\Models\Wiki\Song\Performance;
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
        if (! Schema::hasTable(Performance::TABLE)) {
            Schema::create(Performance::TABLE, function (Blueprint $table) {
                $table->id(Performance::ATTRIBUTE_ID);

                $table->unsignedBigInteger(Performance::ATTRIBUTE_SONG);
                $table->foreign(Performance::ATTRIBUTE_SONG)->references(Song::ATTRIBUTE_ID)->on(Song::TABLE)->cascadeOnDelete();

                $table->morphs(Performance::ATTRIBUTE_ARTIST);

                $table->string(Performance::ATTRIBUTE_ALIAS)->nullable();
                $table->string(Performance::ATTRIBUTE_AS)->nullable();
                $table->timestamps(6);
                $table->softDeletes(Performance::ATTRIBUTE_DELETED_AT, 6);

                $table->unique([Performance::ATTRIBUTE_SONG, Performance::ATTRIBUTE_ARTIST_TYPE, Performance::ATTRIBUTE_ARTIST_ID], 'unique_performance');
            });
        }

        if (! Schema::hasTable(Membership::TABLE)) {
            Schema::create(Membership::TABLE, function (Blueprint $table) {
                $table->id(Membership::ATTRIBUTE_ID);

                $table->unsignedBigInteger(Membership::ATTRIBUTE_ARTIST);
                $table->foreign(Membership::ATTRIBUTE_ARTIST)->references(Artist::ATTRIBUTE_ID)->on(Artist::TABLE)->cascadeOnDelete();

                $table->unsignedBigInteger(Membership::ATTRIBUTE_MEMBER);
                $table->foreign(Membership::ATTRIBUTE_MEMBER)->references(Artist::ATTRIBUTE_ID)->on(Artist::TABLE)->cascadeOnDelete();

                $table->string(Performance::ATTRIBUTE_ALIAS)->nullable();
                $table->string(Performance::ATTRIBUTE_AS)->nullable();
                $table->timestamps(6);
                $table->softDeletes(Performance::ATTRIBUTE_DELETED_AT, 6);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(Performance::TABLE);
        Schema::dropIfExists(Membership::TABLE);
    }
};
