<?php

declare(strict_types=1);

use App\Contracts\Models\HasHashids;
use App\Models\BaseModel;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Video;
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
        if (! Schema::hasTable(PlaylistTrack::TABLE)) {
            Schema::create(PlaylistTrack::TABLE, function (Blueprint $table) {
                $table->id(PlaylistTrack::ATTRIBUTE_ID);
                $table->timestamps(6);
                $table->softDeletes(BaseModel::ATTRIBUTE_DELETED_AT, 6);
                $table->string(HasHashids::ATTRIBUTE_HASHID)->nullable()->collation('utf8mb4_bin');

                $table->unsignedBigInteger(PlaylistTrack::ATTRIBUTE_PLAYLIST);
                $table->foreign(PlaylistTrack::ATTRIBUTE_PLAYLIST)->references(Playlist::ATTRIBUTE_ID)->on(Playlist::TABLE)->cascadeOnDelete();

                $table->unsignedBigInteger(PlaylistTrack::ATTRIBUTE_VIDEO)->nullable();
                $table->foreign(PlaylistTrack::ATTRIBUTE_VIDEO)->references(Video::ATTRIBUTE_ID)->on(Video::TABLE)->nullOnDelete();

                $table->unsignedBigInteger(PlaylistTrack::ATTRIBUTE_PREVIOUS)->nullable();
                $table->foreign(PlaylistTrack::ATTRIBUTE_PREVIOUS)->references(PlaylistTrack::ATTRIBUTE_ID)->on(PlaylistTrack::TABLE)->nullOnDelete();

                $table->unsignedBigInteger(PlaylistTrack::ATTRIBUTE_NEXT)->nullable();
                $table->foreign(PlaylistTrack::ATTRIBUTE_NEXT)->references(PlaylistTrack::ATTRIBUTE_ID)->on(PlaylistTrack::TABLE)->nullOnDelete();
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
        Schema::dropIfExists(PlaylistTrack::TABLE);
    }
};
