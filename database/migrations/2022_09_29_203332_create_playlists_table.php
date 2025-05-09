<?php

declare(strict_types=1);

use App\Contracts\Models\HasHashids;
use App\Models\Auth\User;
use App\Models\BaseModel;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        if (! Schema::hasTable(Playlist::TABLE)) {
            Schema::create(Playlist::TABLE, function (Blueprint $table) {
                $table->id(Playlist::ATTRIBUTE_ID);
                $table->timestamps(6);
                $table->softDeletes(BaseModel::ATTRIBUTE_DELETED_AT, 6);
                $hashIdColumn = $table->string(HasHashids::ATTRIBUTE_HASHID)->nullable();
                if (DB::connection() instanceof MySqlConnection) {
                    // Set collation to binary to be case-sensitive
                    $hashIdColumn->collation('utf8mb4_bin');
                }
                $table->string(Playlist::ATTRIBUTE_NAME);
                $table->integer(Playlist::ATTRIBUTE_VISIBILITY);

                $table->unsignedBigInteger(Playlist::ATTRIBUTE_USER)->nullable();
                $table->foreign(Playlist::ATTRIBUTE_USER)->references(User::ATTRIBUTE_ID)->on(User::TABLE)->cascadeOnDelete();

                $table->unsignedBigInteger(Playlist::ATTRIBUTE_FIRST)->nullable();
                $table->foreign(Playlist::ATTRIBUTE_FIRST)->references(PlaylistTrack::ATTRIBUTE_ID)->on(PlaylistTrack::TABLE)->nullOnDelete();

                $table->unsignedBigInteger(Playlist::ATTRIBUTE_LAST)->nullable();
                $table->foreign(Playlist::ATTRIBUTE_LAST)->references(PlaylistTrack::ATTRIBUTE_ID)->on(PlaylistTrack::TABLE)->nullOnDelete();

                $table->text(Playlist::ATTRIBUTE_DESCRIPTION)->nullable();
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
        Schema::dropIfExists(Playlist::TABLE);
    }
};
