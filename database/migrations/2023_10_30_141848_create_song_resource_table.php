<?php

declare(strict_types=1);

use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Song;
use App\Pivots\Wiki\SongResource;
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
        if (! Schema::hasTable(SongResource::TABLE)) {
            Schema::create(SongResource::TABLE, function (Blueprint $table) {
                $table->timestamps(6);
                $table->unsignedBigInteger(SongResource::ATTRIBUTE_SONG);
                $table->foreign(SongResource::ATTRIBUTE_SONG)->references(Song::ATTRIBUTE_ID)->on(Song::TABLE)->cascadeOnDelete();
                $table->unsignedBigInteger(SongResource::ATTRIBUTE_RESOURCE);
                $table->foreign(SongResource::ATTRIBUTE_RESOURCE)->references(ExternalResource::ATTRIBUTE_ID)->on(ExternalResource::TABLE)->cascadeOnDelete();
                $table->primary([SongResource::ATTRIBUTE_SONG, SongResource::ATTRIBUTE_RESOURCE]);
                $table->string(SongResource::ATTRIBUTE_AS)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(SongResource::TABLE);
    }
};
